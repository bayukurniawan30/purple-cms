<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\NotFoundException;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;

class CollectionsController extends AppController
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

        $this->loadModel('Collections');
        $this->loadModel('CollectionDatas');

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
                        $orderQuery = ['Collections.' . $orderBy => strtoupper($order)];
                    }
                    else {
                        $orderQuery = ['Collections.created' => 'DESC'];
                        $error      = "Invalid query string. Please read the documentation for available query string.";
                    }
                
                }
                else {
                    $orderQuery = ['Collections.created' => 'DESC'];
                }

                $collections = $this->Collections->find('all', [
                    'order' => $orderQuery
                    ])
                    ->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })
                    ->where([
                        'Collections.status' => '1',
                    ]);

                if ($collections->count() > 0) {
                    $return = [
                        'status'      => 'ok',
                        'total'       => $collections->count(),
                        'error'       => $error,
                        'collections' => []
                    ];

                    foreach ($collections as $collection) {
                        $decodeFields = json_decode($collection->fields, true);
                        $collectionArray = [
                            'name'     => $collection->name,
                            'slug'     => $collection->slug,
                            'fields'   => [],
                            'status'   => $collection->text_status,
                            'created'  => $collection->created,
                            'modified' => $collection->modified,
                        ];

                        foreach ($decodeFields as $field) {
                            $decodeField = json_decode($field, true);

                            $fieldArray = [
                                'field_type' => $decodeField['field_type'],
                                'label'      => $decodeField['label'],
                                'slug'       => $decodeField['slug'],
                                'info'       => $decodeField['info'],
                                'required'   => $decodeField['required'] == '1' ? true : false,
                                'options'    => $decodeField['options']
                            ];

                            array_push($collectionArray['fields'], $fieldArray);
                        }

                        array_push($return['collections'], $collectionArray);
                    }
                }
                else {
                    $return = [
                        'status'      => 'ok',
                        'total'       => 0,
                        'collections' => NULL,
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
            // Check if paging and limit query string is exist
            $paging = $this->request->getQuery('paging');
            $limit  = $this->request->getQuery('limit');
            if ($paging !== NULL && $limit !== NULL) {
                $this->loadComponent('Paginator');
            }

            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $slug   = trim($slug);
            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $collection = $this->Collections->findBySlug($slug);

                if ($collection->count() > 0) {
                    $selectedCollection = $collection->first();
                    // Query string for additional condition
                    $orderBy = $this->request->getQuery('order_by');
                    $order   = $this->request->getQuery('order');

                    $defaultSorting      = $selectedCollection->sorting;
                    $defaultSortingOrder = $selectedCollection->sorting_order;

                    if ($order !== NULL && $orderBy !== NULL) {
                        /**
                         * Order By : name, created, modified  default is created
                         * Order : ASC, DESC
                         */
                        $availableOrderBy = ['name', 'created', 'modified'];
                        $availableOrder   = ['asc', 'desc'];

                        if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                            $orderQuery = ['CollectionDatas.' . $orderBy => strtoupper($order)];
                        }
                        else {
                            $orderQuery = ['CollectionDatas.' . $defaultSorting => $defaultSortingOrder];
                            $error      = "Invalid query string. Please read the documentation for available query string.";
                        }
                    }
                    else {
                        $orderQuery = ['CollectionDatas.' . $defaultSorting => $defaultSortingOrder];
                    }

                    $collectionData = $this->CollectionDatas->find('all', [
                        'order' => $orderQuery
                        ])
                        ->contain('Collections')
                        ->contain('Admins', function (Query $q) {
                            return $q
                                ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                        })
                        ->where([
                            'Collections.status' => '1',
                            'Collections.slug'   => $slug
                        ]);

                    $totalAllData = $collectionData->count();

                    if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                        $this->paginate = [
                            'limit' => $limit,
                            'page'  => $paging
                        ];
                        $collectionData = $this->paginate($collectionData);
                    }
    
                    if ($collectionData->count() > 0) {
                        $return = [
                            'status'      => 'ok',
                            'collection'  => [
                                'name' => $selectedCollection->name,
                                'slug' => $selectedCollection->slug
                            ],
                            'data'        => [],
                            'total'       => $totalAllData,
                            'error'       => $error
                        ];

                        if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                            $totalPage = ceil($totalAllData / $limit);
                            $return['page']       = $paging;
                            $return['total_page'] = $totalPage;
                            $return['limit']      = $limit;
                        }

                        $uidSlugArray = [];
                        $decodeFields = json_decode($selectedCollection->fields, true);
                        foreach ($decodeFields as $field) {
                            $decodeField = json_decode($field, true);


                            $uidSlugArray[$decodeField['uid']] = $decodeField['slug'];
                        }

                        foreach ($collectionData as $data) {
                            $decodeContent = json_decode($data->content, true);

                            $moreArray = [
                                'slug'     => $data->slug,
                                'created'  => $data->created,
                                'modified' => $data->modified,
                            ];

                            $content = [];
                            foreach ($decodeContent as $key => $value) {
                                if (strpos($value['field_type'], 'connecting_') !== false) {
                                    $collectionId     = (int)str_replace('connecting_', '', $value['field_type']);
                                    $collectionDataId = $value['value'];

                                    $selectedCollection     = $this->Collections->get($collectionId);
                                    $selectedCollectionData = $this->CollectionDatas->get($collectionDataId);
                                    $decodeSelectedContent  = json_decode($selectedCollectionData->content, true);
                                    $decodeSelectedFields   = json_decode($selectedCollection->fields, true);

                                    $nl = 0;
                                    $collDataArray = [];
                                    foreach ($decodeSelectedContent as $key => $value) {
                                        $decodeField = json_decode($decodeSelectedFields[$nl], true);
                                        $collDataArray[$decodeField['slug']] = $value;
                                        $nl++;
                                    }

                                    $content[$selectedCollection->slug] = [
                                        'collection' => [
                                            'name' => $selectedCollection->name,
                                            'slug' => $selectedCollection->slug
                                        ]
                                    ] + ['data' => $collDataArray + ['slug' => $selectedCollectionData->slug, 'created' => $selectedCollectionData->created, 'modified' => $selectedCollectionData->modified]];

                                    
                                }
                                else {
                                    $content[$uidSlugArray[$key]] = $value;
                                }

                            }

                            array_push($return['data'], $content + $moreArray);
                        }
                    }
                    else {
                        $return = [
                            'status'      => 'ok',
                            'collection'  => [
                                'name' => $selectedCollection->name,
                                'slug' => $selectedCollection->slug
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
                        'error'  => 'Collection is not exist'
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
    public function dataByConnecting($slug, $connectingSlug, $connectingValue)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            // Check if paging and limit query string is exist
            $paging = $this->request->getQuery('paging');
            $limit  = $this->request->getQuery('limit');
            if ($paging !== NULL && $limit !== NULL) {
                $this->loadComponent('Paginator');
            }

            $apiKey         = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $slug           = trim($slug);
            $connectingslug = trim($connectingSlug);
            $apiAccessKey   = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $collection               = $this->Collections->findBySlug($slug);
                $connectingCollection     = $this->Collections->findBySlug($connectingSlug);
                $connectingCollectionData = $this->CollectionDatas->findBySlug($connectingValue);

                if ($collection->count() > 0) {
                    $selectedCollection                 = $collection->first();
                    $selectedConnectingCollection       = $connectingCollection->first();
                    $selectedConnectingCollectionData   = $connectingCollectionData->first();
                    $selectedConnectingCollectionId     = $selectedConnectingCollection->id;
                    $selectedConnectingCollectionDataId = $selectedConnectingCollectionData->id;
                    // Query string for additional condition
                    $orderBy = $this->request->getQuery('order_by');
                    $order   = $this->request->getQuery('order');

                    $defaultSorting      = $selectedCollection->sorting;
                    $defaultSortingOrder = $selectedCollection->sorting_order;

                    if ($order !== NULL && $orderBy !== NULL) {
                        /**
                         * Order By : name, created, modified  default is created
                         * Order : ASC, DESC
                         */
                        $availableOrderBy = ['name', 'created', 'modified'];
                        $availableOrder   = ['asc', 'desc'];

                        if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                            $orderQuery = ['CollectionDatas.' . $orderBy => strtoupper($order)];
                        }
                        else {
                            $orderQuery = ['CollectionDatas.' . $defaultSorting => $defaultSortingOrder];
                            $error      = "Invalid query string. Please read the documentation for available query string.";
                        }
                    }
                    else {
                        $orderQuery = ['CollectionDatas.' . $defaultSorting => $defaultSortingOrder];
                    }

                    $likeCondition = '%"field_type":"connecting_' . $selectedConnectingCollectionId . '","value":"' . $selectedConnectingCollectionDataId . '"%';
                    $collectionData = $this->CollectionDatas->find('all', [
                        'order' => $orderQuery
                        ])
                        ->contain('Collections')
                        ->contain('Admins', function (Query $q) {
                            return $q
                                ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                        })
                        ->where(function (QueryExpression $exp, Query $q) use ($slug, $likeCondition)  {
                            return $exp
                                ->like('CollectionDatas.content', $likeCondition)
                                ->eq('Collections.status', '1')
                                ->eq('Collections.slug', $slug);
                        });

                    $this->log($collectionData, 'debug');
                    $this->log($likeCondition, 'debug');

                    $totalAllData = $collectionData->count();

                    if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                        $this->paginate = [
                            'limit' => $limit,
                            'page'  => $paging
                        ];
                        $collectionData = $this->paginate($collectionData);
                    }
    
                    if ($collectionData->count() > 0) {
                        $return = [
                            'status'      => 'ok',
                            'collection'  => [
                                'name' => $selectedCollection->name,
                                'slug' => $selectedCollection->slug
                            ],
                            'data'        => [],
                            'total'       => $totalAllData,
                            'error'       => $error
                        ];

                        if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                            $totalPage = ceil($totalAllData / $limit);
                            $return['page']       = $paging;
                            $return['total_page'] = $totalPage;
                            $return['limit']      = $limit;
                        }

                        $uidSlugArray = [];
                        $decodeFields = json_decode($selectedCollection->fields, true);
                        foreach ($decodeFields as $field) {
                            $decodeField = json_decode($field, true);


                            $uidSlugArray[$decodeField['uid']] = $decodeField['slug'];
                        }

                        foreach ($collectionData as $data) {
                            $decodeContent = json_decode($data->content, true);

                            $moreArray = [
                                'slug'     => $data->slug,
                                'created'  => $data->created,
                                'modified' => $data->modified,
                            ];

                            $content = [];
                            foreach ($decodeContent as $key => $value) {
                                if (strpos($value['field_type'], 'connecting_') !== false) {
                                    $collectionId     = (int)str_replace('connecting_', '', $value['field_type']);
                                    $collectionDataId = $value['value'];

                                    $selectedCollection     = $this->Collections->get($collectionId);
                                    $selectedCollectionData = $this->CollectionDatas->get($collectionDataId);
                                    $decodeSelectedContent  = json_decode($selectedCollectionData->content, true);
                                    $decodeSelectedFields   = json_decode($selectedCollection->fields, true);

                                    $nl = 0;
                                    $collDataArray = [];
                                    foreach ($decodeSelectedContent as $key => $value) {
                                        $decodeField = json_decode($decodeSelectedFields[$nl], true);
                                        $collDataArray[$decodeField['slug']] = $value;
                                        $nl++;
                                    }

                                    $content[$selectedCollection->slug] = [
                                        'collection' => [
                                            'name' => $selectedCollection->name,
                                            'slug' => $selectedCollection->slug
                                        ]
                                    ] + ['data' => $collDataArray + ['slug' => $selectedCollectionData->slug, 'created' => $selectedCollectionData->created, 'modified' => $selectedCollectionData->modified]];

                                    
                                }
                                else {
                                    $content[$uidSlugArray[$key]] = $value;
                                }

                            }

                            array_push($return['data'], $content + $moreArray);
                        }
                    }
                    else {
                        $return = [
                            'status'      => 'ok',
                            'collection'  => [
                                'name' => $selectedCollection->name,
                                'slug' => $selectedCollection->slug
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
                        'error'  => 'Collection is not exist'
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
    public function detail($slug, $dataSlug)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $slug   = trim($slug);
            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $collection     = $this->Collections->findBySlug($slug);
                $collectionData = $this->CollectionDatas->findBySlug($dataSlug);

                if ($collection->count() > 0) {
                    if ($collectionData->count() > 0) {
                        $selectedCollection     = $collection->first();
                        $selectedCollectionData = $collectionData->first();

                        $return = [
                            'status'      => 'ok',
                            'collection'  => [
                                'name' => $selectedCollection->name,
                                'slug' => $selectedCollection->slug
                            ],
                            'data'        => NULL,
                            'total'       => $collectionData->count(),
                            'error'       => $error
                        ];

                        $uidSlugArray = [];
                        $decodeFields = json_decode($selectedCollection->fields, true);
                        foreach ($decodeFields as $field) {
                            $decodeField = json_decode($field, true);

                            $uidSlugArray[$decodeField['uid']] = $decodeField['slug'];
                        }

                        $decodeContent = json_decode($selectedCollectionData->content, true);

                        $moreArray = [
                            'slug'     => $selectedCollectionData->slug,
                            'created'  => $selectedCollectionData->created,
                            'modified' => $selectedCollectionData->modified,
                        ];

                        $content = [];
                        foreach ($decodeContent as $key => $value) {
                            $content[$uidSlugArray[$key]] = $value;
                        }

                        $return['data'] = $content + $moreArray;
                    }
                    else {
                        $return = [
                            'status' => 'error',
                            'error'  => 'Collection data is not exist'
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => 'Collection is not exist'
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