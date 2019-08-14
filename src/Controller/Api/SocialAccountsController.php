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
use App\Validator\Api\SocialAddValidator;
use App\Validator\Api\SocialUpdateValidator;
use App\Validator\Api\SocialDeleteValidator;

class SocialAccountsController extends AppController
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
        $this->Auth->allow(['view', 'detail']);

        $this->loadModel('Socials');
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
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $socials = $this->Socials->find('all', [
                    'order' => ['ordering' => 'ASC']
                    ]);

                if ($socials->count() > 0) {
                    $return = [
                        'status'  => 'ok',
                        'total'   => $socials->count(),
                        'socials' => $socials,
                        'error'   => $error
                    ];
                }
                else {
                    $return = [
                        'status'  => 'ok',
                        'total'   => 0,
                        'socials' => NULL,
                        'error'   => $error
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
    public function detail($account)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey  = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $account = trim($account);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $availableAccount = ['facebook', 'instagram', 'twitter', 'google-plus', 'youtube', 'pinterest', 'github'];
                if (in_array($account, $availableAccount)) {
                    $socials = $this->Socials->find('all', [
                        'order' => ['ordering' => 'ASC']
                        ])->where(['name' => $account])->limit(1);

                    if ($socials->count() > 0) {
                        $return = [
                            'status' => 'ok',
                            'social' => $socials->first(),
                            'error'  => $error
                        ];
                    }
                    else {
                        $return = [
                            'status' => 'ok',
                            'social' => NULL,
                            'error'  => $error
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => 'Invalid account type. Please read the documentation for available account type.'
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
                $availableAccount = ['facebook', 'instagram', 'twitter', 'google-plus', 'youtube', 'pinterest', 'github'];
                if (in_array(trim($this->request->getData('name')), $availableAccount)) {
                    $socialAddValidator = new SocialAddValidator();
                    $errorValidate      = $socialAddValidator->validate()->errors($this->request->getData());
                    if (empty($errorValidate)) {
                        $social = $this->Socials->newEntity();
                        $social = $this->Socials->patchEntity($social, $this->request->getData());
                    
                        if ($this->Socials->save($social)) {
                            $recordId = $social->id;

                            $social = $this->Socials->get($recordId);
                            $social->ordering = $recordId;
                            $result = $this->Socials->save($social);


                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */

                            $options = [
                                'title'    => '(API) Addition of a New Social Media',
                                'detail'   => ' add '.ucwords($social).' as a new social media.',
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
                                'social'   => $social,
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
                        'error'  => 'Invalid account type. Please read the documentation for available account type.'
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
        if (($this->request->is('patch') || $this->request->is('post'))  && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $availableAccount = ['facebook', 'instagram', 'twitter', 'google-plus', 'youtube', 'pinterest', 'github'];
                if (in_array(trim($this->request->getData('name')), $availableAccount)) {
                    $socialUpdateValidator = new SocialUpdateValidator();
                    $errorValidate         = $socialUpdateValidator->validate()->errors($this->request->getData());
                    if (empty($errorValidate)) {
                        $checkExist = $this->Socials->find()->where(['id' => $this->request->getData('id')]);
                        if ($checkExist->count() == 1) {
                            $social = $this->Socials->get($this->request->getData('id'));
                            $this->Socials->patchEntity($social, $this->request->getData());

                            if ($this->Socials->save($social)) {
                                $recordId = $social->id;
                                $social   = $this->Socials->get($recordId);

                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */

                                $options = [
                                    'title'    => '(API) Data Change of a Social Media',
                                    'detail'   => ' change '.ucwords($social->name).' url.',
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
                                    'social'   => $social,
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
                        else {
                            $return = [
                                'status' => 'error',
                                'error'  => "Social media not found."
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
                        'error'  => 'Invalid account type. Please read the documentation for available account type.'
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
                $socialDeleteValidator = new SocialDeleteValidator();
                $errorValidate         = $socialDeleteValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->Socials->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $social = $this->Socials->get($this->request->getData('id'));
                        $name   = $social->name;

                        $result = $this->Socials->delete($social);

                        if ($result) {
                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => '(API) Deletion of a Social Media',
                                'detail'   => ' delete '.$name.' from social media.',
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
                            'error'  => "Social media not found."
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