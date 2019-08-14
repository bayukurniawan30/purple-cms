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
use App\Validator\Api\SubscriberAddValidator;
use App\Validator\Api\SubscriberDeleteValidator;

class SubscribersController extends AppController
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

        $this->loadModel('Subscribers');
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
                $subscribers = $this->Subscribers->find('all', [
                    'order' => ['id' => 'ASC']
                    ]);

                if ($subscribers->count() > 0) {
                    $return = [
                        'status'      => 'ok',
                        'total'       => $subscribers->count(),
                        'subscribers' => $subscribers,
                        'error'       => $error
                    ];
                }
                else {
                    $return = [
                        'status'      => 'ok',
                        'total'       => 0,
                        'subscribers' => NULL,
                        'error'       => $error
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
    public function detail($id)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $id     = trim($id);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $subscribers = $this->Subscribers->find('all', [
                    'order' => ['id' => 'ASC']
                    ])->where(['id' => $id])->limit(1);

                if ($subscribers->count() > 0) {
                    $return = [
                        'status'     => 'ok',
                        'subscriber' => $subscribers->first(),
                        'error'      => $error
                    ];
                }
                else {
                    $return = [
                        'status'     => 'ok',
                        'total'      => 0,
                        'subscriber' => NULL,
                        'error'      => $error
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
                $subscriberAddValidator = new SubscriberAddValidator();
                $errorValidate          = $subscriberAddValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $purpleApi = new PurpleProjectApi();
                    $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                    if ($verifyEmail == true) {
                        $findDuplicate = $this->Subscribers->find()->where(['email' => $this->request->getData('email')]);

                        if ($findDuplicate->count() >= 1) {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't save data due to duplication of data"
                            ];
                        }
                        else {
                            $subscriber = $this->Subscribers->newEntity();
                            $subscriber = $this->Subscribers->patchEntity($subscriber, $this->request->getData());
                            $subscriber->status = 'active';
                        
                            if ($this->Subscribers->save($subscriber)) {
                                $recordId   = $subscriber->id;
                                $subscriber = $this->Subscribers->get($recordId);

                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */

                                $options = [
                                    'title'    => '(API) Addition of New Subscriber',
                                    'detail'   => ' add '.$subscriber->email.' as a new subscriber.',
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
                                    'status'     => 'ok',
                                    'subscriber' => $subscriber,
                                    'activity'   => $activity,
                                    'error'      => $error
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
                            'error'  => 'Email is not valid. Please use a real email'
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
                $subscriberDeleteValidator = new SubscriberDeleteValidator();
                $errorValidate             = $subscriberDeleteValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->Subscribers->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $subscriber = $this->Subscribers->get($this->request->getData('id'));
                        $email      = $subscriber->email;

                        $result = $this->Subscribers->delete($subscriber);

                        if ($result) {
                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => '(API) Deletion of a Subscriber',
                                'detail'   => ' delete '.$email.' from subscriber.',
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
                            'error'  => "Subscriber not found."
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