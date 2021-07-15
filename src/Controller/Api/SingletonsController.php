<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Http\Exception\NotFoundException;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;

class SingletonsController extends AppController
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

        $this->loadModel('Singletons');
        $this->loadModel('SingletonDatas');

        $this->viewBuilder()->enableAutoLayout(false);

        $purpleGlobal = new PurpleProjectGlobal();
		$protocol     = $purpleGlobal->protocol();

        $data = [
            'baseUrl' => $protocol . $this->request->host() . $this->request->getAttribute("webroot")
        ];

        $this->set($data);
    }
    public function view()
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                // Query string for additional condition
                $orderBy = $this->request->getQuery('order_by');
                $order   = $this->request->getQuery('order');
                if ($order !== NULL && $orderBy !== NULL) {
                    /**
                     * Order By : name, created, modified  default is created
                     * Order : ASC, DESC
                     */
                    $availableOrderBy = ['name', 'created', 'modified'];
                    $availableOrder   = ['asc', 'desc'];

                    if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                        $orderQuery = ['Singletons.' . $orderBy => strtoupper($order)];
                    }
                    else {
                        $orderQuery = ['Singletons.created' => 'DESC'];
                        $error      = "Invalid query string. Please read the documentation for available query string.";
                    }
                
                }
                else {
                    $orderQuery = ['Singletons.created' => 'DESC'];
                }

                $singletons = $this->Singletons->find('all', [
                    'order' => $orderQuery
                    ])
                    ->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })
                    ->where([
                        'Singletons.status' => '1',
                    ]);

                if ($singletons->count() > 0) {
                    $return = [
                        'status'      => 'ok',
                        'total'       => $singletons->count(),
                        'error'       => $error,
                        'singletons'  => []
                    ];

                    foreach ($singletons as $singleton) {
                        $decodeFields = json_decode($singleton->fields, true);
                        $singletonArray = [
                            'name'     => $singleton->name,
                            'slug'     => $singleton->slug,
                            'fields'   => [],
                            'status'   => $singleton->text_status,
                            'created'  => $singleton->created,
                            'modified' => $singleton->modified,
                        ];

                        foreach ($decodeFields as $field) {
                            $decodeField = json_decode($field, true);

                            $fieldArray = [
                                'field_type' => $decodeField['field_type'],
                                'label'      => $decodeField['label'],
                                'info'       => $decodeField['info'],
                                'required'   => $decodeField['required'] == '1' ? true : false,
                                'options'    => $decodeField['options']
                            ];

                            array_push($singletonArray['fields'], $fieldArray);
                        }

                        array_push($return['singletons'], $singletonArray);
                    }
                }
                else {
                    $return = [
                        'status'      => 'ok',
                        'total'       => 0,
                        'singletons'  => NULL,
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
    public function data($slug)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $slug   = trim($slug);
            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $singleton = $this->Singletons->findBySlug($slug);

                if ($singleton->count() > 0) {
                    $selectedSingleton = $singleton->first();
                    // Query string for additional condition
                    $orderBy = $this->request->getQuery('order_by');
                    $order   = $this->request->getQuery('order');

                    if ($order !== NULL && $orderBy !== NULL) {
                        /**
                         * Order By : name, created, modified  default is created
                         * Order : ASC, DESC
                         */
                        $availableOrderBy = ['name', 'created', 'modified'];
                        $availableOrder   = ['asc', 'desc'];

                        if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                            $orderQuery = ['SingletonDatas.' . $orderBy => strtoupper($order)];
                        }
                        else {
                            $orderQuery = ['SingletonDatas.created' => 'DESC'];
                            $error      = "Invalid query string. Please read the documentation for available query string.";
                        }
                    }
                    else {
                        $orderQuery = ['SingletonDatas.created' => 'DESC'];
                    }

                    $singletonData = $this->SingletonDatas->find('all', [
                        'order' => $orderQuery
                        ])
                        ->contain('Singletons')
                        ->contain('Admins', function (Query $q) {
                            return $q
                                ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                        })
                        ->where([
                            'Singletons.status' => '1',
                            'Singletons.slug'   => $slug
                        ])
                        ->limit(1);

                    $totalAllData = $singletonData->count();

                    if ($singletonData->count() > 0) {
                        $return = [
                            'status'      => 'ok',
                            'singleton'  => [
                                'name' => $selectedSingleton->name,
                                'slug' => $selectedSingleton->slug
                            ],
                            'data'        => NULL,
                            'total'       => $totalAllData,
                            'error'       => $error
                        ];

                        $uidSlugArray = [];
                        $decodeFields = json_decode($selectedSingleton->fields, true);
                        foreach ($decodeFields as $field) {
                            $decodeField = json_decode($field, true);

                            $uidSlugArray[$decodeField['uid']] = $decodeField['slug'];
                        }

                        $singletonData = $singletonData->first();
                        $decodeContent = json_decode($singletonData->content, true);

                        $dateArray = [
                            'created'  => $singletonData->created,
                            'modified' => $singletonData->modified,
                        ];

                        $content = [];
                        foreach ($decodeContent as $key => $value) {
                            $content[$uidSlugArray[$key]] = $value;
                        }

                        $return['data'] = $content + $dateArray;
                    }
                    else {
                        $return = [
                            'status'      => 'ok',
                            'singleton'  => [
                                'name' => $selectedSingleton->name,
                                'slug' => $selectedSingleton->slug
                            ],
                            'data'        => NULL,
                            'total'       => 0,
                            'error'       => $error
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => 'Singleton is not exist'
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
}