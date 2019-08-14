<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use App\Validator\Api\PostCategoryAddValidator;
use App\Validator\Api\PostCategoryUpdateValidator;
use App\Validator\Api\PostCategoryDeleteValidator;

class PostCategoriesController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $purpleGlobal = new PurpleProjectGlobal();
        $databaseInfo   = $purpleGlobal->databaseInfo();
        if ($databaseInfo == 'default') {
            throw new NotFoundException(__('Page not found'));
        }
        else {
            $purpleSettings = new PurpleProjectSettings();
            $maintenance    = $purpleSettings->maintenanceMode();
            $userLoggedIn   = $purpleSettings->checkUserLoggedIn();

            if ($maintenance == 'enable' && $userLoggedIn == false) {
                throw new NotFoundException(__('Page not found'));
            }
        }
    }
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Basic' => [
                    'fields'    => ['username' => 'username', 'password' => 'api_key'],
                    'userModel' => 'Admins',
                ],
            ],
            'authorize'            => 'Controller',
            'authError'            => 'Unauthorized access',
            'storage'              => 'Memory',
            'unauthorizedRedirect' => false,
            'loginAction'          => [
                '_name' => 'adminLoginApi',
            ]
        ]);

        // Allow GET method
        $this->Auth->allow(['view', 'viewInPage', 'detail', 'totalPost']);

        $this->loadModel('BlogCategories');
        $this->loadModel('Blogs');
        $this->loadModel('Pages');
        $this->loadModel('Admins');
        $this->loadModel('Settings');
        $this->loadModel('Histories');

        $this->viewBuilder()->enableAutoLayout(false);

        $purpleGlobal = new PurpleProjectGlobal();
		$protocol     = $purpleGlobal->protocol();

        $data = [
            'baseUrl' => $protocol . $this->request->host() . $this->request->getAttribute("webroot")
        ];

        $this->set($data);
    }
    public function isAuthorized($user)
    {
        // Only admins can access admin functions
        if (isset($user['level']) && $user['level'] == '1') {
            return true;
        }

        // Default deny
        return false;
    }
    public function view() 
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            // Check if paging and limit query string is exist
            $paging = $this->request->getQuery('paging');
            $limit  = $this->request->getQuery('limit');
            if ($paging !== NULL && $limit !== NULL) {
                $this->loadComponent('Paginator');
            }

            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                // Query string for additional condition
                $orderBy = $this->request->getQuery('order_by');
                $order   = $this->request->getQuery('order');
                if ($order !== NULL && $orderBy !== NULL) {
                    /**
                     * Order By : name, created, ordering
                     * Order : ASC, DESC
                     */
                    $availableOrderBy = ['name', 'created', 'ordering'];
                    $availableOrder   = ['asc', 'desc'];
                    
                    if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                        $orderQuery = ['BlogCategories.' . $orderBy => strtoupper($order)];
                    }
                    else {
                        $orderQuery = ['BlogCategories.ordering' => 'ASC'];
                        $error      = "Invalid query string. Please read the documentation for available query string.";
                    }
                }
                else {
                    $orderQuery = ['BlogCategories.ordering' => 'ASC'];
                }

                $blogCategories = $this->BlogCategories->find('all', [
                    'order' => $orderQuery
                    ])
                    ->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })
                    ->where([
                        'BlogCategories.page_id is' => NULL
                    ]);

                if ($blogCategories->count() > 0) {
                    $return = [
                        'status'          => 'ok',
                        'total'           => $blogCategories->count(),
                        'error'           => $error
                    ];

                    if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                        $this->paginate = [
                            'limit' => $limit,
                            'page'  => $paging
                        ];
                        $blogCategoryList          = $this->paginate($blogCategories);
                        $return['post_categories'] = $blogCategories;
                        $return['page']            = $paging;
                        $return['limit']           = $limit;
                    }
                    else {
                        $return['post_categories'] = $blogCategories;
                    }
                }
                else {
                    $return = [
                        'status'          => 'ok',
                        'total'           => 0,
                        'post_categories' => NULL,
                        'error'           => $error
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
    public function viewInPage($page) 
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            // Check if paging and limit query string is exist
            $paging = $this->request->getQuery('paging');
            $limit  = $this->request->getQuery('limit');
            if ($paging !== NULL && $limit !== NULL) {
                $this->loadComponent('Paginator');
            }

            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $page   = trim($page);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $checkPage = $this->Pages->find()->where(['slug' => $page, 'status' => '1']);
                if ($checkPage->count() == 1) { 
                    $page = $checkPage->first();

                    // Query string for additional condition
                    $orderBy = $this->request->getQuery('order_by');
                    $order   = $this->request->getQuery('order');
                    if ($order !== NULL && $orderBy !== NULL) {
                        /**
                         * Order By : name, created, ordering
                         * Order : ASC, DESC
                         */
                        $availableOrderBy = ['name', 'created', 'ordering'];
                        $availableOrder   = ['asc', 'desc'];
                        
                        if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                            $orderQuery = ['BlogCategories.' . $orderBy => strtoupper($order)];
                        }
                        else {
                            $orderQuery = ['BlogCategories.ordering' => 'ASC'];
                            $error      = "Invalid query string. Please read the documentation for available query string.";
                        }
                    }
                    else {
                        $orderQuery = ['BlogCategories.ordering' => 'ASC'];
                    }

                    $blogCategories = $this->BlogCategories->find('all', [
                        'order' => $orderQuery
                        ])
                        ->contain('Admins', function (Query $q) {
                            return $q
                                ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                        })
                        ->where([
                            'BlogCategories.page_id' => $page->id
                        ]);

                    if ($blogCategories->count() > 0) {
                        $return = [
                            'status'          => 'ok',
                            'total'           => $blogCategories->count(),
                            'page'            => $page,
                            'error'           => $error
                        ];

                        if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                            $this->paginate = [
                                'limit' => $limit,
                                'page'  => $paging
                            ];
                            $blogCategoryList          = $this->paginate($blogCategories);
                            $return['post_categories'] = $blogCategories;
                            $return['page']            = $paging;
                            $return['limit']           = $limit;
                        }
                        else {
                            $return['post_categories'] = $blogCategories;
                        }
                    }
                    else {
                        $return = [
                            'status'          => 'ok',
                            'total'           => 0,
                            'post_categories' => NULL,
                            'error'           => $error
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => 'Page not found'
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
    public function detail($slug)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $slug   = trim($slug);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $blogCategories = $this->BlogCategories->find('all')
                    ->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })
                    ->where([
                        'BlogCategories.slug' => $slug 
                    ]);

                if ($blogCategories->count() > 0) {
                    $postCategoryDetail = $blogCategories->first();

                    $return = [
                        'status'        => 'ok',
                        'post_category' => $postCategoryDetail,
                        'error'         => $error
                    ];
                }
                else {
                    $return = [
                        'status'        => 'ok',
                        'post_category' => NULL,
                        'error'         => 'Post category not found'
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
    public function totalPost($slug)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $slug   = trim($slug);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $blogCategories = $this->BlogCategories->find('all')
                    ->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })
                    ->where([
                        'BlogCategories.slug' => $slug 
                    ]);

                if ($blogCategories->count() > 0) {
                    $postCategoryDetail = $blogCategories->first();
                    $blogs = $this->Blogs->find('all', [
                        'order' => ['Blogs.created' => 'DESC']
                        ])
                        ->contain('BlogCategories', function (Query $q) {
                            return $q
                                ->select(['id', 'name', 'slug', 'admin_id']);  
                        })
                        ->contain('Admins', function (Query $q) {
                            return $q
                                ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                        })
                        ->where([
                            'Blogs.status' => '1',
                            'BlogCategories.slug' => $slug 
                        ]);
                    $totalPosts = $blogs->count();

                    $return = [
                        'status'      => 'ok',
                        'total_posts' => $totalPosts,
                        'error'       => $error
                    ];
                }
                else {
                    $return = [
                        'status'        => 'ok',
                        'post_category' => NULL,
                        'error'         => 'Post category not found'
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
    public function add()
    {
        if ($this->request->is('post') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $postCategoryAddValidator = new PostCategoryAddValidator();
                $errorValidate            = $postCategoryAddValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $slug = Text::slug(strtolower($this->request->getData('name')));
                    $findDuplicate = $this->BlogCategories->find()->where(['slug' => $slug]);
                    if ($findDuplicate->count() >= 1) {
                        $return = [
                            'status' => 'error',
                            'error'  => "Can't save data due to duplication of data"
                        ];}
                    else {
                        $blogCategory = $this->BlogCategories->newEntity();
                        $blogCategory = $this->BlogCategories->patchEntity($blogCategory, $this->request->getData());
                        $blogCategory->admin_id = $this->Auth->user('id');

                        if ($this->BlogCategories->save($blogCategory)) {
                            $recordId = $blogCategory->id;

                            $blogCategory = $this->BlogCategories->get($recordId);
                            $blogCategory->ordering = $recordId;
                            $result       = $this->BlogCategories->save($blogCategory);
                            $blogCategory = $this->BlogCategories->get($recordId);

                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */

                            $options = [
                                'title'    => '(API) Addition of New Post Category',
                                'detail'   => ' add '.$blogCategory->name.' as a new post category.',
                                'admin_id' => $this->Auth->user('id')
                            ];

                            $saveActivity   = $this->Histories->saveActivity($options);

                            if ($saveActivity == true) {
                                $activity = true;
                            }
                            else {
                                $activity = false;
                            }

                            $return = [
                                'status'        => 'ok',
                                'post_category' => $blogCategory,
                                'activity'      => $activity,
                                'error'         => $error
                            ];
                        }
                        else {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't save data"
                            ];
                        }
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => $errorValidate
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            if (Configure::read('debug')) {
                $return['data'] = $this->request->getData();
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function update()
    {
        if (($this->request->is('put') || $this->request->is('post'))  && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $postCategoryUpdateValidator = new PostCategoryUpdateValidator();
                $errorValidate               = $postCategoryUpdateValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->BlogCategories->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $slug = Text::slug(strtolower($this->request->getData('name')));
                        $findDuplicate = $this->BlogCategories->find()->where(['slug' => $slug]);
                        if ($findDuplicate->count() >= 1) {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't save data due to duplication of data"
                            ];
                        }
                        else {
                            $category = $this->BlogCategories->get($this->request->getData('id'));
                            $category->admin_id = $this->Auth->user('id');

                            $this->BlogCategories->patchEntity($category, $this->request->getData());

                            if ($this->BlogCategories->save($category)) {
                                $recordId = $category->id;

                                $category  = $this->BlogCategories->get($recordId);

                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */

                                $options = [
                                    'title'    => '(API) Data Change of a Post Category',
                                    'detail'   => ' update '.$category->name.' data from post category.',
                                    'admin_id' => $this->Auth->user('id')
                                ];

                                $saveActivity   = $this->Histories->saveActivity($options);

                                if ($saveActivity == true) {
                                    $activity = true;
                                }
                                else {
                                    $activity = false;
                                }

                                $return = [
                                    'status'        => 'ok',
                                    'post_category' => $category,
                                    'activity'      => $activity,
                                    'error'         => $error
                                ];
                            }
                            else {
                                $return = [
                                    'status' => 'error',
                                    'error'  => "Can't save data"
                                ];
                            }
                        }
                    }
                    else {
                        $return = [
                            'status' => 'error',
                            'error'  => "Post category not found."
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => $errorValidate
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            if (Configure::read('debug')) {
                $return['data'] = $this->request->getData();
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
    public function delete()
    {
        if ($this->request->is('delete') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $postCategoryDeleteValidator = new PostCategoryDeleteValidator();
                $errorValidate               = $postCategoryDeleteValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->BlogCategories->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $category  = $this->BlogCategories->get($this->request->getData('id'));
                        $name      = $category->name;

                        $result = $this->BlogCategories->delete($category);

                        if ($result) {
                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => '(API) Deletion of a Post Category',
                                'detail'   => ' delete '.$name.' from post category.',
                                'admin_id' => $this->Auth->user('id')
                            ];

                            $saveActivity   = $this->Histories->saveActivity($options);

                            if ($saveActivity == true) {
                                $activity = true;
                            }
                            else {
                                $activity = false;
                            }

                            $return = [
                                'status'   => 'ok',
                                'activity' => $activity,
                                'error'    => $error
                            ];
                        }
                        else {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't delete data"
                            ];
                        }
                    }
                    else {
                        $return = [
                            'status' => 'error',
                            'error'  => "Post category not found."
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => $errorValidate
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            if (Configure::read('debug')) {
                $return['data'] = $this->request->getData();
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
}