<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Routing\Router;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\CollectionAddForm;
use App\Form\Purple\CollectionEditForm;
use App\Form\Purple\CollectionDeleteForm;
use App\Form\Purple\CollectionDataDeleteForm;
use App\Form\Purple\SearchForm;
use Cake\Utility\Text;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectPlugins;
use App\Purple\PurpleProjectComponents;
use Particle\Filter\Filter;

class CollectionsController extends AppController
{
    public $imagesLimit = 30;

    public function beforeFilter(Event $event)
	{
		parent::beforeFilter($event);
		
		/**
		 * Check if Purple CMS has been setup or not
		 * If not, redirect to Purple Setup
		 */
	    $purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo == 'default') {
			return $this->redirect(
	            ['prefix' => false, 'controller' => 'Setup', 'action' => 'index']
	        );
		}

		/**
		 * Check if user is signed in
		 * If not, redirect to login page
		 */
		$session     = $this->getRequest()->getSession();
		$sessionHost = $session->read('Admin.host');

		if ($this->request->getEnv('HTTP_HOST') != $sessionHost || !$session->check('Admin.id')) {
			return $this->redirect(
				['_name' => 'adminLogin', '?' => ['ref' => Router::url($this->getRequest()->getRequestTarget(), true)]]
			);
		}
	}
	public function initialize()
	{
		parent::initialize();

		$this->loadComponent('Flash');

		// Get Admin Session data
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		// Set layout
		$this->viewBuilder()->setLayout('dashboard');

		$this->loadComponent('Image');

		// Load other models
		$this->loadModel('Admins');
		$this->loadModel('Medias');
		$this->loadModel('Collections');
		$this->loadModel('CollectionDatas');
		$this->loadModel('Settings');

		// Check debug is on or off
		if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
			$cakeDebug = 'on';
		} 
		else {
			$cakeDebug = 'off';
		}

		$queryAdmin      = $this->Admins->signedInUser($sessionID, $sessionPassword);
		$queryFavicon    = $this->Settings->fetch('favicon');
		$queryDateFormat = $this->Settings->fetch('dateformat');
		$queryTimeFormat = $this->Settings->fetch('timeformat');
		$medias          = $this->Medias->find('all', [
			'order' => ['Medias.id' => 'DESC']])->contain('Admins');

		$rowCount = $queryAdmin->count();
		if ($rowCount > 0) {
			$adminData = $queryAdmin->first();

			// Dashboard search form
			$dashboardSearch = new SearchForm();
			
			// Plugins List
			$purplePlugins 	= new PurpleProjectPlugins();
			$plugins		= $purplePlugins->purplePlugins();
			$this->set('plugins', $plugins);

			$this->PurpleProjectComponents = new PurpleProjectComponents();

			$data = [
				'sessionHost'        => $sessionHost,
				'sessionID'          => $sessionID,
				'sessionPassword'    => $sessionPassword,
				'cakeDebug'          => $cakeDebug,
				'adminName' 	     => ucwords($adminData->display_name),
				'adminLevel' 	     => $adminData->level,
				'adminEmail' 	     => $adminData->email,
				'adminPhoto' 	     => $adminData->photo,
				'greeting'           => '',
				'dashboardSearch'	 => $dashboardSearch,
				'title'              => 'Collections | Purple CMS',
				'pageTitle'          => 'Collections',
				'pageTitleIcon'      => 'mdi-checkbox-multiple-blank-outline',
				'pageBreadcrumb'     => 'Collections',
				'appearanceFavicon'  => $queryFavicon,
				'settingsDateFormat' => $queryDateFormat->value,
				'settingsTimeFormat' => $queryTimeFormat->value,
				'mediaImageTotal'    => $medias->count(),
				'mediaImageLimit'    => $this->imagesLimit
			];
			$this->set($data);

			$this->paginate = [
				'limit' => $this->imagesLimit,
				'page'  => 1
			];
			$browseMedias = $this->paginate($medias);
			$this->set('browseMedias', $browseMedias);
		}
		else {
			return $this->redirect(
				['controller' => 'Authenticate', 'action' => 'login', '?' => ['ref' => Router::url($this->getRequest()->getRequestTarget(), true)]]
			);
		}
	}
	public function index() 
	{
		$status = $this->request->getQuery('status', 'all');
		$availableArray = ['all', 'publish', 'draft', 'deleted'];

		if (!in_array($status, $availableArray)) {
	        throw new NotFoundException(__('Page not found'));
		}

		$collectionDelete = new CollectionDeleteForm();

		$collections = $this->Collections->find()->contain('Admins');

		if ($status === NULL || $status == 'all') {
			$collections->where(['OR' => [['Collections.status' => '0'], ['Collections.status' => '1']]]);
			$setStatus = 'All';
		}
		else {
			if ($status == 'publish') {
				$getStatus = '1';
				$setStatus = 'Publish';
			}
			elseif ($status == 'draft') {
				$getStatus = '0';
				$setStatus = 'Draft';
			}
			elseif ($status == 'deleted') {
				$getStatus = '2';
				$setStatus = 'Deleted';
			}

			$collections->where(['Collections.status' => $getStatus]);
		}
		
        $this->set(compact('collections'));

		$data = [
			'collectionDelete' => $collectionDelete,
			'setStatus'        => $setStatus
		];

		$this->set($data);
    }
	public function data() 
    {
		$collection = $this->Collections->find()->where(['slug' => $this->request->getParam('data')]);
		if ($collection->count() > 0) {
			$collectionData = $collection->first();

			$medias = $this->Medias->find('all', [
				'order' => ['Medias.id' => 'DESC']])->contain('Admins');

			$data = [
				'pageTitle'      => $collectionData->name,
				'pageBreadcrumb' => 'Collections::' . $collectionData->name,
				'collection'     => $collectionData,
				'mediasForMd'    => $medias        
			];

			$this->set($data);
		}
		else {
	        throw new NotFoundException(__('Page not found'));
		}
    }
	public function viewData() 
    {
		$collectionDataDelete = new CollectionDataDeleteForm();

		$collectionSlug = $this->request->getParam('data');
		$collection     = $this->Collections->findBySlug($collectionSlug)->first();

		$collectionDatas = $this->CollectionDatas->find()->where(['collection_id' => $collection->id]);
		$this->set(compact('collectionDatas'));

		// API Response
		$apiResult = NULL;
		/**
		 *  If your host can't resolve domain, comment the code below, but try it in Postman
		 *
		 */
		$endpointUrl = Router::url([
			'_name' => 'apiv1ViewCollectionDatas',
			'slug'  => $collection->slug
		], true);

        $apiAccessKey = $this->Settings->find()->where(['name' => 'apiaccesskey'])->first();

		$http = new Client();
		$response = $http->get($endpointUrl, NULL, [
			'headers' => ['X-Purple-Api-Key' => $apiAccessKey->value]
		]);
		$apiResult = $response->getStringBody();
		// End of API Response

		$data = [
			'pageTitle'            => $collection->name,
			'pageBreadcrumb'       => 'Collections::' . $collection->name . '::View',
			'collection'           => $collection,
			'collectionDataDelete' => $collectionDataDelete,
			'apiResult'            => $apiResult
		];

		$this->set($data);
    }
    public function add() 
    {
		$collectionAdd = new CollectionAddForm();

		$fieldTypes = $this->PurpleProjectComponents->fieldTypes();

		$selectboxFieldTypes = [];

		$connectingCollections = $this->Collections->find()->where(['status' => '1', 'connecting' => '1'])->order(['name' => 'ASC']);

		foreach ($fieldTypes as $key => $value) {
			if ($connectingCollections->count() > 0) {
				$selectboxFieldTypes['Default Fields'][$key] = $value['text'];
			}
			else {
				$selectboxFieldTypes[$key] = $value['text'];
			}
		}

		if ($connectingCollections->count() > 0) {
			foreach ($connectingCollections as $cn) {
				$selectboxFieldTypes['Connecting Collections']['connecting_' . $cn->id] = $cn->name;
			}
		}

		$data = [
			'pageBreadcrumb'      => 'Collections::Add',
			'fieldTypes'          => $fieldTypes,
			'selectboxFieldTypes' => $selectboxFieldTypes,
			'collectionAdd'       => $collectionAdd
		];

		$this->set($data);
    }
    public function edit() 
    {
		$collectionEdit = new CollectionEditForm();

		$fieldTypes = $this->PurpleProjectComponents->fieldTypes();

		$selectboxFieldTypes = [];

		$connectingCollections = $this->Collections->find()->where(['status' => '1', 'connecting' => '1'])->order(['name' => 'ASC']);

		foreach ($fieldTypes as $key => $value) {
			if ($connectingCollections->count() > 0) {
				$selectboxFieldTypes['Default Fields'][$key] = $value['text'];
			}
			else {
				$selectboxFieldTypes[$key] = $value['text'];
			}
		}

		if ($connectingCollections->count() > 0) {
			foreach ($connectingCollections as $cn) {
				$selectboxFieldTypes['Connecting Collections']['connecting_' . $cn->id] = $cn->name;
			}
		}

		$collection = $this->Collections->get($this->request->getParam('id'));

		$data = [
			'pageBreadcrumb'      => 'Collections::Add',
			'fieldTypes'          => $fieldTypes,
			'selectboxFieldTypes' => $selectboxFieldTypes,
			'collectionEdit'      => $collectionEdit,
			'collection'          => $collection
		];

		$this->set($data);
    }
	public function editData() 
    {
		$collection = $this->Collections->find()->where(['slug' => $this->request->getParam('data')]);
		if ($collection->count() > 0) {
			$collectionData = $collection->first();
			$savedData      = $this->CollectionDatas->get($this->request->getParam('id'));

			$medias = $this->Medias->find('all', [
				'order' => ['Medias.id' => 'DESC']])->contain('Admins');

			if ($savedData->slug == NULL || $savedData->slug == '') {}
			else {
				// API Response
				$apiResult = NULL;
				/**
				 *  If your host can't resolve domain, comment the code below, but try it in Postman
				 *
				 */
				$endpointUrl = Router::url([
					'_name'    => 'apiv1ViewCollectionDataDetails',
					'slug'     => $collectionData->slug,
					'dataSlug' => $savedData->slug
				], true);

				$apiAccessKey = $this->Settings->find()->where(['name' => 'apiaccesskey'])->first();

				$http = new Client();
				$response = $http->get($endpointUrl, NULL, [
					'headers' => ['X-Purple-Api-Key' => $apiAccessKey->value]
				]);
				$apiResult = $response->getStringBody();
				// End of API Response
			}

			$data = [
				'pageTitle'      => $collectionData->name,
				'pageBreadcrumb' => 'Collections::' . $collectionData->name . '::Edit Data',
				'collection'     => $collectionData,
				'collectionData' => $savedData,
				'mediasForMd'    => $medias,
				'apiResult'      => $apiResult      
			];

			$this->set($data);
		}
		else {
	        throw new NotFoundException(__('Page not found'));
		}

		$this->set($data);
    }
    public function ajaxAdd() 
    {
		$this->viewBuilder()->enableAutoLayout(false);

		$collectionAdd = new CollectionAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($collectionAdd->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['name', 'sorting', 'sorting_order'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				if (is_array($this->request->getData('fields'))) {
					$session   = $this->getRequest()->getSession();
					$sessionID = $session->read('Admin.id');
					$admin     = $this->Admins->get($sessionID);

					$slug = Text::slug(strtolower($requestData->name));
					$findDuplicate = $this->Collections->find()->where(['slug' => $slug]);
					if ($findDuplicate->count() >= 1) {
						$json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
					}
					else {
						$collection = $this->Collections->newEntity();
						$collection = $this->Collections->patchEntity($collection, $requestArray);
						$collection->fields = json_encode($this->request->getData('fields'));
						$collection->admin_id = $sessionID;

						if ($this->Collections->save($collection)) {
							$recordId = $collection->id;

							$collection = $this->Collections->get($recordId);

							// Tell system for new event
							$event = new Event('Model.BlogCategory.afterSave', $this, ['collection' => $collection, 'admin' => $admin, 'save' => 'new']);
							$this->getEventManager()->dispatch($event);

							$json = json_encode(['status' => 'ok', 'id' => $recordId, 'activity' => $event->getResult()]);

							$this->Flash->set($collection->name . ' has been added in Collections.', [
								'element' => 'Flash/Purple/success'
							]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
						}
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please add collection field."]);
				}
			}
			else {
				$errors = $collectionAdd->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
			}

			$this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxUpdate() 
    {
		$this->viewBuilder()->enableAutoLayout(false);

		$collectionEdit = new CollectionEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($collectionEdit->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['name', 'sorting', 'sorting_order'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				if (is_array($this->request->getData('fields'))) {
					$session   = $this->getRequest()->getSession();
					$sessionID = $session->read('Admin.id');
					$admin     = $this->Admins->get($sessionID);

					$slug = Text::slug(strtolower($requestData->name));
					$findDuplicate = $this->Collections->find()->where(['slug' => $slug, 'id <>' => $requestData->id]);
					if ($findDuplicate->count() >= 1) {
						$json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
					}
					else {
						$collection = $this->Collections->get($requestData->id);
						$collection = $this->Collections->patchEntity($collection, $requestArray);
						$collection->fields = json_encode($this->request->getData('fields'));
						$collection->admin_id = $sessionID;

						if ($this->Collections->save($collection)) {
							$recordId = $collection->id;

							// Remove content in datas
							$listUid = [];
							foreach ($this->request->getData('fields') as $field) {
								$decodeField = json_decode($field, true);
								$uid = $decodeField['uid'];

								array_push($listUid, $uid);
							}

							$collectionDatas = $this->CollectionDatas->find()->where(['collection_id' => $recordId]);

							if ($collectionDatas->count() > 0) {
								foreach ($collectionDatas as $collData) {
									$newContent = [];
									$content    = json_decode($collData->content, true);
									foreach ($content as $key => $value) {
										if (in_array($key, $listUid)) {
											$newContent[$key] = $value;
										}
									}

									$collectionData = $this->CollectionDatas->get($collData->id);
									$collectionData->content = json_encode($newContent);

									$this->CollectionDatas->save($collectionData);
								}
							}

							$collection = $this->Collections->get($recordId);

							// Tell system for new event
							$event = new Event('Model.BlogCategory.afterSave', $this, ['collection' => $collection, 'admin' => $admin, 'save' => 'update']);
							$this->getEventManager()->dispatch($event);

							$json = json_encode(['status' => 'ok', 'id' => $recordId, 'activity' => $event->getResult()]);

							$this->Flash->set($collection->name . ' has been updated.', [
								'element' => 'Flash/Purple/success'
							]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
						}
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please add collection field."]);
				}
			}
			else {
				$errors = $collectionEdit->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
			}

			$this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxDelete() 
    {
		$this->viewBuilder()->enableAutoLayout(false);

        $collectionDelete  = new CollectionDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($collectionDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$collection  = $this->Collections->get($requestData->id);
				$name      = $collection->name;
				$collection->slug   = $collection->slug . '-deleted';
				$collection->status = '2';

                if ($this->Collections->save($collection)) {
					// Tell system for new event
					$event = new Event('Model.Collection.afterDelete', $this, ['collection' => $name, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set($name . ' has been deleted from Collections.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $collectionDelete->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
	public function ajaxGetOptions()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			// Sanitize user input
			$filter = new Filter();
			$filter->values(['key'])->trim()->stripHtml();
			$filterResult = $filter->filter($this->request->getData());
			$requestData  = json_decode(json_encode($filterResult), FALSE);
			$requestArray = json_decode(json_encode($filterResult), TRUE);

			$fieldTypes = $this->PurpleProjectComponents->fieldTypes();

			if (array_key_exists($requestData->key, $fieldTypes) || strpos($requestData->key, 'connecting_') !== false) {
				if (strpos($requestData->key, 'connecting_') !== false) {
					$collectionId = (int)str_replace('connecting_', '', $requestData->key);
					

					$collectionDetail = $this->Collections->get($collectionId);
					$fields = $collectionDetail->fields;
					$decodeFields = json_decode($fields, true);

					$labelArray  = [];
					$fieldArray  = [];
					$uidArray    = [];
					foreach ($decodeFields as $field) {
						$decodeValue = json_decode($field);
						array_push($fieldArray, '<a href="#" class="connecting-collection-field-to-show" data-uid="' . $decodeValue->uid . '" data-label="' . $decodeValue->label . '"><span class="uk-label" style="text-transform: none">' . $decodeValue->label . '</span></a>');
						array_push($uidArray, $decodeValue->uid);
						array_push($labelArray, $decodeValue->label);
					}

					if (count($decodeFields) > 1) {
						$helper = 'Choose which field to show between ' . Text::toList($fieldArray);
						$result = [
							'showFieldUid'   => '',
							'showFieldLabel' => ''
						];
					}
					else {
						$helper = NULL;
						$result = [
							'showFieldUid'   => $uidArray[0],
							'showFieldLabel' => $labelArray[0]
						];
					}
				}
				else {
					$options = $fieldTypes[$requestData->key]['options'];
					if ($options == NULL) {
						$result = [];
					}
					else {
						$result = $options;
					}

					$helper = NULL;
				}

				$json = json_encode(['status' => 'ok', 'options' => $result, 'helper' => $helper]);
			}
			else {
				$json = json_encode(['status' => 'error', 'error' => "Can't find options for that value."]);
			}

			$this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxGetConnectingFields()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			// Sanitize user input
			$filter = new Filter();
			$filter->values(['key'])->trim()->stripHtml();
			$filterResult = $filter->filter($this->request->getData());
			$requestData  = json_decode(json_encode($filterResult), FALSE);

			$collectionId = (int)str_replace('connecting_', '', $requestData->key);

			$collectionDetail = $this->Collections->get($collectionId);
			$fields = $collectionDetail->fields;
			$decodeFields = json_decode($fields, true);

			$fieldArray = [];
			$uidArray   = [];
			foreach ($decodeFields as $field) {
				$decodeValue = json_decode($field);
				array_push($fieldArray, '<a href="#" class="connecting-collection-field-to-show" data-uid="' . $decodeValue->uid . '" data-label="' . $decodeValue->label . '"><span class="uk-label" style="text-transform: none">' . $decodeValue->label . '</span></a>');
				array_push($uidArray, $decodeValue->uid);
			}

			if (count($decodeFields) > 1) {
				$helper = 'Choose which field to show between ' . Text::toList($fieldArray);
			}
			else {
				$helper = NULL;
			}

			$json = json_encode(['status' => 'ok', 'helper' => $helper]);

			$this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxAddField()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			// Sanitize user input
			$filter = new Filter();
			$filter->values(['field_type', 'label', 'info', 'required', 'options'])->trim()->stripHtml();
			$filterResult = $filter->filter($this->request->getData());
			$requestData  = json_decode(json_encode($filterResult), FALSE);
			$requestArray = json_decode(json_encode($filterResult), TRUE);

			$fieldTypes = $this->PurpleProjectComponents->fieldTypes();

			if (array_key_exists($requestData->field_type, $fieldTypes) || strpos($requestData->field_type, 'connecting_') !== false) {
				$result = [
					'uid'        => Text::uuid(),
					'field_type' => $requestData->field_type,
					'slug'       => $requestData->field_slug,
					'label'      => $requestData->label,
					'info'       => $requestData->info,
					'required'   => $requestData->required,
					'options'    => json_decode($requestData->options, true)
				];

				$json = json_encode(['status' => 'ok', 'result' => $result]);
			}
			else {
				$json = json_encode(['status' => 'error', 'error' => "Can't find options for that value."]);
			}

			$this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxAddData()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$data    = $this->request->getData();
			$setData = $data;
			unset($setData['collection_id']);
			unset($setData['create_slug']);
			unset($setData['slug_target']);
			$collectionId = $this->request->getData('collection_id');
			
			$session   = $this->getRequest()->getSession();
			$sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

			$baseUrl = Router::url([
				'_name' => 'home'
			], true);

			$newData = [];
			foreach ($setData as $key => $value) {
				if ($value['field_type'] == 'text' || $value['field_type'] == 'textarea' || $value['field_type'] == 'html' || $value['field_type'] == 'markdown') {
					$newValue = htmlentities($value['value']);
				}
				elseif ($value['field_type'] == 'number') {
					$newValue = intval($value['value']);
				}
				elseif ($value['field_type'] == 'image') {
					$originalPath        = $baseUrl . 'uploads/images/original/' . $value['value'];
					$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $value['value'];
					$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $value['value'];

					$newValue = [
						'full_path' => $originalPath,
						'thumbnail' => [
							'300x300' => $thumbnailSquarePath,
							'480x270' => $thumbnailLscapePath
						],
						'color_palette' => $this->Image->getColorPalette($value['value'])
					];
				}
				elseif ($value['field_type'] == 'gallery') {
					if (strpos($value['value'], ',') !== false) {
						$newValue = [];
						$explodeValue = explode(',', $value['value']);
						foreach ($explodeValue as $gallery) {
							$originalPath        = $baseUrl . 'uploads/images/original/' . $gallery;
							$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $gallery;
							$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $gallery;

							array_push($newValue, [
								'full_path' => $originalPath,
								'thumbnail' => [
									'300x300' => $thumbnailSquarePath,
									'480x270' => $thumbnailLscapePath
								],
								'color_palette' => $this->Image->getColorPalette($gallery)
							]);
						}
					}
					else {
						$originalPath        = $baseUrl . 'uploads/images/original/' . $value['value'];
						$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $value['value'];
						$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $value['value'];

						$newValue = [
							'full_path' => $originalPath,
							'thumbnail' => [
								'300x300' => $thumbnailSquarePath,
								'480x270' => $thumbnailLscapePath
							],
							'color_palette' => $this->Image->getColorPalette($value['value'])
						];
					}
				}
				else {
					$newValue = $value['value'];
				}

				$newData[$key] = [
					'field_type' => $value['field_type'],
					'value'      => $newValue
				];
			}

			if ($this->request->getData('create_slug') == '1' && !empty($this->request->getData('slug_target'))) {
				$createSlug = Text::slug(strtolower($this->request->getData($this->request->getData('slug_target'))['value']));
				$slugTarget = trim($this->request->getData('slug_target'));
			}
			else {
				$createSlug = NULL;
				$slugTarget = NULL;
			}

			$collectionData = $this->CollectionDatas->newEntity();
			$collectionData->content       = json_encode($newData);
			$collectionData->slug          = $createSlug;
			$collectionData->slug_target   = $slugTarget;
			$collectionData->collection_id = $collectionId;
			$collectionData->admin_id      = $sessionID;

			if ($this->CollectionDatas->save($collectionData)) {
				$colls = $this->Collections->get($collectionId);

				// Tell system for new event
				$event = new Event('Model.Collection.afterSaveData', $this, ['collection' => $colls, 'admin' => $admin, 'save' => 'new']);
				$this->getEventManager()->dispatch($event);

				$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

				$this->Flash->set('Data has been added in ' . $colls->name . ' Collection.', [
					'element' => 'Flash/Purple/success'
				]);
			}
			else {
				$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
			}

			$json = json_encode(['status' => 'ok']);

			$this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
		}
		else {
			throw new NotFoundException(__('Page not found'));
		}
	}
	public function ajaxUpdateData()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$data    = $this->request->getData();
			$setData = $data;
			unset($setData['collection_id']);
			unset($setData['id']);
			unset($setData['create_slug']);
			unset($setData['slug_target']);
			$collectionId = $this->request->getData('collection_id');
			$dataId       = $this->request->getData('id');
			
			$session   = $this->getRequest()->getSession();
			$sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

			$baseUrl = Router::url([
				'_name' => 'home'
			], true);

			$newData = [];

			foreach ($setData as $key => $value) {
				if ($value['field_type'] == 'text' || $value['field_type'] == 'textarea' || $value['field_type'] == 'html' || $value['field_type'] == 'markdown') {
					$newValue = htmlentities($value['value']);
				}
				elseif ($value['field_type'] == 'number') {
					$newValue = intval($value['value']);
				}
				elseif ($value['field_type'] == 'image') {
					$originalPath        = $baseUrl . 'uploads/images/original/' . $value['value'];
					$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $value['value'];
					$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $value['value'];

					$newValue = [
						'full_path' => $originalPath,
						'thumbnail' => [
							'300x300' => $thumbnailSquarePath,
							'480x270' => $thumbnailLscapePath
						],
						'color_palette' => $this->Image->getColorPalette($value['value'])
					];
				}
				elseif ($value['field_type'] == 'gallery') {
					if (strpos($value['value'], ',') !== false) {
						$newValue = [];
						$explodeValue = explode(',', $value['value']);
						foreach ($explodeValue as $gallery) {
							$originalPath        = $baseUrl . 'uploads/images/original/' . $gallery;
							$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $gallery;
							$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $gallery;

							array_push($newValue, [
								'full_path' => $originalPath,
								'thumbnail' => [
									'300x300' => $thumbnailSquarePath,
									'480x270' => $thumbnailLscapePath
								],
								'color_palette' => $this->Image->getColorPalette($gallery)
							]);
						}
					}
					else {
						$originalPath        = $baseUrl . 'uploads/images/original/' . $value['value'];
						$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $value['value'];
						$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $value['value'];

						$newValue = [
							'full_path' => $originalPath,
							'thumbnail' => [
								'300x300' => $thumbnailSquarePath,
								'480x270' => $thumbnailLscapePath
							],
							'color_palette' => $this->Image->getColorPalette($value['value'])
						];
					}
				}
				else {
					$newValue = $value['value'];
				}

				$newData[$key] = [
					'field_type' => $value['field_type'],
					'value'      => $newValue
				];
			}

			if ($this->request->getData('create_slug') == '1' && !empty($this->request->getData('slug_target'))) {
				$createSlug = Text::slug(strtolower($this->request->getData($this->request->getData('slug_target'))['value']));
				$slugTarget = trim($this->request->getData('slug_target'));
			}
			else {
				$createSlug = NULL;
				$slugTarget = NULL;
			}

			$collectionData = $this->CollectionDatas->get($dataId);
			$collectionData->content       = json_encode($newData);
			$collectionData->slug          = $createSlug;
			$collectionData->slug_target   = $slugTarget;
			$collectionData->collection_id = $collectionId;
			$collectionData->admin_id      = $sessionID;

			if ($this->CollectionDatas->save($collectionData)) {
				$colls = $this->Collections->get($collectionId);

				// Tell system for new event
				$event = new Event('Model.Collection.afterSaveData', $this, ['collection' => $colls, 'admin' => $admin, 'save' => 'update']);
				$this->getEventManager()->dispatch($event);

				$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

				$this->Flash->set('Data has been updated in ' . $colls->name . ' Collection.', [
					'element' => 'Flash/Purple/success'
				]);
			}
			else {
				$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
			}

			$json = json_encode(['status' => 'ok']);

			$this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
		}
		else {
			throw new NotFoundException(__('Page not found'));
		}
	}
	public function ajaxDeleteData()
    {
		$this->viewBuilder()->enableAutoLayout(false);

        $collectionDataDelete  = new CollectionDataDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($collectionDataDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$collectionData  = $this->CollectionDatas->get($requestData->id);
				$collectionId    = $collectionData->collection_id;
				$collection      = $this->Collections->get($collectionId);

                if ($this->CollectionDatas->delete($collectionData)) {
					// Tell system for new event
					$event = new Event('Model.Collection.afterDeleteData', $this, ['collection' => ['collection_name' => $collection->name], 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set('Data has been deleted from ' . $collection->name . ' Collection.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $collectionDataDelete->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

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