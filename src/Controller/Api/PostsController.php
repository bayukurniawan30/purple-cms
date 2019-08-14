<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use App\Validator\Api\PostAddValidator;
use App\Validator\Api\PostUpdateValidator;
use App\Validator\Api\PostDeleteValidator;
use Carbon\Carbon;
use Bulletproof;
use Gregwar\Image\Image;

class PostsController extends AppController
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
        $this->Auth->allow(['view', 'viewInPage', 'viewByCategory', 'detail']);

        $this->loadModel('Blogs');
        $this->loadModel('Tags');
        $this->loadModel('BlogsTags');
        $this->loadModel('Pages');
        $this->loadModel('Admins');
        $this->loadModel('Medias');
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
                     * Order By : title, created
                     * Order : ASC, DESC
                     */
                    $availableOrderBy = ['title', 'created'];
                    $availableOrder   = ['asc', 'desc'];
                    
                    if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                        $orderQuery = ['Blogs.' . $orderBy => strtoupper($order)];
                    }
                    else {
                        $orderQuery = ['Blogs.created' => 'DESC'];
                        $error      = "Invalid query string. Please read the documentation for available query string.";
                    }
                }
                else {
                    $orderQuery = ['Blogs.created' => 'DESC'];
                }

                $blogs = $this->Blogs->find('all', [
                    'order' => $orderQuery
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
                    ]);

                if ($blogs->count() > 0) {
                    $return = [
                        'status' => 'ok',
                        'total'  => $blogs->count(),
                        'error'  => $error
                    ];

                    if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                        $this->paginate = [
                            'limit' => $limit,
                            'page'  => $paging
                        ];
                        $blogsList = $this->paginate($blogs);
                        $return['posts'] = $blogsList;
                        $return['page']  = $paging;
                        $return['limit'] = $limit;
                    }
                    else {
                        $return['posts'] = $blogs;
                    }
                }
                else {
                    $return = [
                        'status' => 'ok',
                        'total'  => 0,
                        'posts'  => NULL,
                        'error'  => $error
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
                         * Order By : title, created
                         * Order : ASC, DESC
                         */
                        $availableOrderBy = ['title', 'created'];
                        $availableOrder   = ['asc', 'desc'];
                        
                        if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                            $orderQuery = ['Blogs.' . $orderBy => strtoupper($order)];
                        }
                        else {
                            $orderQuery = ['Blogs.created' => 'DESC'];
                            $error      = "Invalid query string. Please read the documentation for available query string.";
                        }
                    }
                    else {
                        $orderQuery = ['Blogs.created' => 'DESC'];
                    }

                    $blogs = $this->Blogs->find('all', [
                        'order' => $orderQuery
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
                            'BlogCategories.page_id' => $page->id
                        ]);

                    if ($blogs->count() > 0) {
                        $return = [
                            'status' => 'ok',
                            'total'  => $blogs->count(),
                            'error'  => $error
                        ];

                        if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                            $this->paginate = [
                                'limit' => $limit,
                                'page'  => $paging
                            ];
                            $blogsList = $this->paginate($blogs);
                            $return['posts'] = $blogsList;
                            $return['page']  = $paging;
                            $return['limit'] = $limit;
                        }
                        else {
                            $return['posts'] = $blogs;
                        }
                    }
                    else {
                        $return = [
                            'status' => 'ok',
                            'total'  => 0,
                            'posts'  => NULL,
                            'error'  => $error
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
    public function viewByCategory($category)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            // Check if paging and limit query string is exist
            $paging = $this->request->getQuery('paging');
            $limit  = $this->request->getQuery('limit');
            if ($paging !== NULL && $limit !== NULL) {
                $this->loadComponent('Paginator');
            }

            $apiKey   = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $category = trim($category);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                // Query string for additional condition
                $orderBy = $this->request->getQuery('order_by');
                $order   = $this->request->getQuery('order');
                if ($order !== NULL && $orderBy !== NULL) {
                    /**
                     * Order By : title, created
                     * Order : ASC, DESC
                     */
                    $availableOrderBy = ['title', 'created'];
                    $availableOrder   = ['asc', 'desc'];
                    
                    if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                        $orderQuery = ['Blogs.' . $orderBy => strtoupper($order)];
                    }
                    else {
                        $orderQuery = ['Blogs.created' => 'DESC'];
                        $error      = "Invalid query string. Please read the documentation for available query string.";
                    }
                }
                else {
                    $orderQuery = ['Blogs.created' => 'DESC'];
                }

                $blogs = $this->Blogs->find('all', [
                    'order' => $orderQuery
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
                        'BlogCategories.slug' => $category 
                    ]);

                if ($blogs->count() > 0) {
                    $return = [
                        'status' => 'ok',
                        'total'  => $blogs->count(),
                        'error'  => $error
                    ];

                    if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                        $this->paginate = [
                            'limit' => $limit,
                            'page'  => $paging
                        ];
                        $blogsList = $this->paginate($blogs);
                        $return['posts'] = $blogsList;
                        $return['page']  = $paging;
                        $return['limit'] = $limit;
                    }
                    else {
                        $return['posts'] = $blogs;
                    }
                }
                else {
                    $return = [
                        'status' => 'ok',
                        'total'  => 0,
                        'posts'  => NULL,
                        'error'  => $error
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
                        'Blogs.slug' => $slug 
                    ]);

                if ($blogs->count() > 0) {
                    $postDetail = $blogs->first();

                    $return = [
                        'status' => 'ok',
                        'post'   => $postDetail,
                        'error'  => $error
                    ];
                }
                else {
                    $return = [
                        'status' => 'ok',
                        'post'   => NULL,
                        'error'  => 'Post not found'
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
    public function uploadImages($file, $adminId)
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if (!empty($file)) {
            $purpleSettings = new PurpleProjectSettings();
            $timezone       = $purpleSettings->timezone();
            $date           = Carbon::now($timezone);

            $uploadPath     = TMP . 'uploads' . DS . 'images' . DS;
            $fileName       = $file['name'];

            $image = new Bulletproof\Image($file);
            $newName         = Text::slug($fileName, ['preserve' => '.']);
            $explodeNewName  = explode(".", $newName);
            $fileExtension   = end($explodeNewName);
            $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
            $dateSlug        = Text::slug($date);
            $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;

            $image->setName($generatedName)
                    ->setMime(array('jpeg', 'jpg', 'png'))
                    ->setSize(100, 3145728)
                    ->setLocation($uploadPath);
            if ($image->upload()) {
                $fullSizeImage              = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
                $uploadedThumbnailSquare    = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '300x300' . DS;
                $uploadedThumbnailLandscape = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '480x270' . DS;
                if (file_exists($image->getFullPath())) {
                    $readImageFile   = new File($image->getFullPath());
                    $imageSize       = $readImageFile->size();
                    
                    $fullSize           = Image::open($image->getFullPath())->save($fullSizeImage . $generatedName, 'guess', 90);
                    $thumbnailSquare    = Image::open($image->getFullPath())
                                        ->zoomCrop(300, 300)
                                        ->save($uploadedThumbnailSquare . $generatedName, 'guess', 90);
                    $thumbnailLandscape = Image::open($image->getFullPath())
                                        ->zoomCrop(480, 270)
                                        ->save($uploadedThumbnailLandscape . $generatedName, 'guess', 90);

                    $media = $this->Medias->newEntity();

                    $media->name     = $generatedName;
                    $media->title    = $fileOnlyName;
                    $media->size     = $imageSize;
                    $media->admin_id = $adminId;

                    if ($this->Medias->save($media)) {
                        $readImageFile   = new File($image->getFullPath());
                        $deleteImage     = $readImageFile->delete();
                        
                        return $generatedName;
                    }
                    else {
                        return false;
                    }
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
	}
    public function add()
    {
        if ($this->request->is('post') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $postAddValidator = new PostAddValidator();
                $errorValidate    = $postAddValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $slug = Text::slug(strtolower(trim($this->request->getData('title'))));
                    $findDuplicate = $this->Blogs->find()->where(['slug' => $slug]);
                    if ($findDuplicate->count() >= 1) {
                        $return = [
                            'status' => 'error',
                            'error'  => "Can't save data due to duplication of data. Please try again with another title."
                        ];
                    }
                    else {
                        $blog = $this->Blogs->newEntity();
                        $blog = $this->Blogs->patchEntity($blog, $this->request->getData());
                        if (empty($this->request->getData('featured'))) {
                            $blog->blog_type_id = '1';
                        }
                        else {
                            $upload = $this->uploadImages($this->request->getData('featured'), $this->Auth->user('id'));
                            if ($upload) {
                                $blog->featured = $upload;
                            }
                            $blog->blog_type_id = '2';
                        }
                        $blog->admin_id = $this->Auth->user('id');

                        if ($this->Blogs->save($blog)) {
                            $recordId = $blog->id;

                            // Save Tags
                            if(!empty($this->request->getData('tags'))) {
                                $explodeTags = explode(',', $this->request->getData('tags'));
                                foreach ($explodeTags as $addedTag) {
                                    $checkTag       = $this->Tags->checkExists($addedTag);
                                    
                                    if ($checkTag == false) {
                                        $tag = $this->Tags->newEntity();
                                        $tag->title = $addedTag;
                                        $this->Tags->save($tag);
                                        $tagId = $tag->id;

                                        $checkBlogsTags = $this->BlogsTags->checkExists($recordId, $tagId);

                                        if ($checkBlogsTags == false) {
                                            $blogsTags = $this->BlogsTags->newEntity();
                                            $blogsTags->blog_id = $recordId;
                                            $blogsTags->tag_id  = $tagId;
                                            $this->BlogsTags->save($blogsTags);
                                        }
                                    }
                                }
                            }

                            $blog = $this->Blogs->find('all', [
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
                                    'Blogs.id' => $recordId,
                                ])->first();

                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */

                            $options = [
                                'title'    => '(API) Addition of New Post',
                                'detail'   => ' add '.$blog->title.' as a new post.',
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
                                'post'     => $blog,
                                'activity' => $activity,
                                'error'    => $error
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
                $postUpdateValidator = new PostUpdateValidator();
                $errorValidate       = $postUpdateValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->Blogs->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $slug = Text::slug(strtolower(trim($this->request->getData('title'))));
                        $findDuplicate = $this->Blogs->find()->where(['slug' => $slug, 'id <>' => $this->request->getData('id')]);
                        if ($findDuplicate->count() >= 1) {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't save data due to duplication of data. Please try again with another title."
                            ];
                        }
                        else {
                            $blog = $this->Blogs->get($this->request->getData('id'));
                            if ($this->request->is('post')) {
                                if (empty($this->request->getData('featured'))) {
                                    $blog->blog_type_id = '1';
                                }
                                else {
                                    $upload = $this->uploadImages($this->request->getData('featured'), $this->Auth->user('id'));
                                    if ($upload) {
                                        $blog->featured = $upload;
                                    }
                                    $blog->blog_type_id = '2';
                                }
                            }

                            $blog->admin_id = $this->Auth->user('id');
                            $blog = $this->Blogs->patchEntity($blog, $this->request->getData());

                            if ($this->Blogs->save($blog)) {
                                $recordId = $blog->id;

                                // Save Tags
                                if(!empty($this->request->getData('tags'))) {
                                    $explodeTags = explode(',', $this->request->getData('tags'));
                                    foreach ($explodeTags as $addedTag) {
                                        $checkTag       = $this->Tags->checkExists($addedTag);
                                        
                                        if ($checkTag == false) {
                                            $tag = $this->Tags->newEntity();
                                            $tag->title = $addedTag;
                                            $this->Tags->save($tag);
                                            $tagId = $tag->id;

                                            $checkBlogsTags = $this->BlogsTags->checkExists($recordId, $tagId);

                                            if ($checkBlogsTags == false) {
                                                $blogsTags = $this->BlogsTags->newEntity();
                                                $blogsTags->blog_id = $recordId;
                                                $blogsTags->tag_id  = $tagId;
                                                $this->BlogsTags->save($blogsTags);
                                            }
                                        }
                                    }
                                }

                                $blog = $this->Blogs->find('all', [
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
                                        'Blogs.id' => $recordId,
                                    ])->first();

                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */

                                $options = [
                                    'title'    => '(API) Data Change of a Post',
                                    'detail'   => ' update '.$blog->title.' data from posts.',
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
                                    'post'     => $blog,
                                    'activity' => $activity,
                                    'error'    => $error
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
                            'error'  => "Post not found."
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
                $postDeleteValidator = new PostDeleteValidator();
                $errorValidate       = $postDeleteValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->Blogs->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $blog  = $this->Blogs->get($this->request->getData('id'));
                        $title = $blog->title;

                        $result = $this->Blogs->delete($blog);

                        if ($result) {
                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => '(API) Deletion of a Post',
                                'detail'   => ' delete '.$title.' from posts.',
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
                            'error'  => "Post not found."
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