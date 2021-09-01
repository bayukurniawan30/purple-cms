<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Http\Client;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\SingletonAddForm;
use App\Form\Purple\SingletonEditForm;
use App\Form\Purple\SingletonDeleteForm;
use App\Form\Purple\SingletonDataDeleteForm;
use App\Form\Purple\SearchForm;
use Cake\Utility\Text;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;
use App\Purple\PurpleProjectComponents;
use Particle\Filter\Filter;

class SingletonsController extends AppController
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

		// Load other models
		$this->loadModel('Admins');
		$this->loadModel('Medias');
		$this->loadModel('Singletons');
		$this->loadModel('SingletonDatas');
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
				'title'              => 'Singletons | Purple CMS',
				'pageTitle'          => 'Singletons',
				'pageTitleIcon'      => 'mdi-cube-outline',
				'pageBreadcrumb'     => 'Singletons',
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

		$singletonDelete = new SingletonDeleteForm();

		$singletons = $this->Singletons->find()->contain('Admins');

		if ($status === NULL || $status == 'all') {
			$singletons->where(['OR' => [['Singletons.status' => '0'], ['Singletons.status' => '1']]]);
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

			$singletons->where(['Singletons.status' => $getStatus]);
		}
		
        $this->set(compact('singletons'));

		$data = [
			'singletonDelete' => $singletonDelete,
			'setStatus'        => $setStatus
		];

		$this->set($data);
    }
	public function data() 
    {
		$singleton = $this->Singletons->find()->where(['slug' => $this->request->getParam('data')]);
		if ($singleton->count() > 0) {
			$singletonData = $singleton->first();

            $singletonDatas = $this->SingletonDatas->find()->where(['singleton_id' => $singletonData->id]); 
            if ($singletonDatas->count() >= 1) {
                throw new NotFoundException(__('Page not found'));
            }

			$medias = $this->Medias->find('all', [
				'order' => ['Medias.id' => 'DESC']])->contain('Admins');

			$data = [
				'pageTitle'      => $singletonData->name,
				'pageBreadcrumb' => 'Singletons::' . $singletonData->name,
				'singleton'     => $singletonData,
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
		$singletonDataDelete = new SingletonDataDeleteForm();

		$singletonSlug = $this->request->getParam('data');
		$singleton     = $this->Singletons->findBySlug($singletonSlug)->first();

		$singletonDatas = $this->SingletonDatas->find()->where(['singleton_id' => $singleton->id]);
		$this->set(compact('singletonDatas'));

		// API Response
		$endpointUrl = Router::url([
			'_name' => 'apiv1ViewSingletonnDatas',
			'slug'  => $singleton->slug
		], true);

        $apiAccessKey = $this->Settings->find()->where(['name' => 'apiaccesskey'])->first();

		$http = new Client();
		$response = $http->get($endpointUrl, NULL, [
			'headers' => ['X-Purple-Api-Key' => $apiAccessKey->value]
		]);
		$apiResult = $response->getStringBody();

		$data = [
			'pageTitle'           => $singleton->name,
			'pageBreadcrumb'      => 'Singletons::' . $singleton->name . '::View',
			'singleton'           => $singleton,
			'singletonDataDelete' => $singletonDataDelete,
			'apiResult'           => $apiResult  
		];

		$this->set($data);
    }
    public function add() 
    {
		$singletonAdd = new SingletonAddForm();

		$fieldTypes = $this->PurpleProjectComponents->fieldTypes();

		$selectboxFieldTypes = [];
		foreach ($fieldTypes as $key => $value) {
			$selectboxFieldTypes[$key] = $value['text'];
		}

		$data = [
			'pageBreadcrumb'      => 'Singletons::Add',
			'fieldTypes'          => $fieldTypes,
			'selectboxFieldTypes' => $selectboxFieldTypes,
			'singletonAdd'       => $singletonAdd
		];

		$this->set($data);
    }
    public function edit() 
    {
		$singletonEdit = new SingletonEditForm();

		$fieldTypes = $this->PurpleProjectComponents->fieldTypes();

		$selectboxFieldTypes = [];
		foreach ($fieldTypes as $key => $value) {
			$selectboxFieldTypes[$key] = $value['text'];
		}

		$singleton = $this->Singletons->get($this->request->getParam('id'));

		$data = [
			'pageBreadcrumb'      => 'Singletons::Add',
			'fieldTypes'          => $fieldTypes,
			'selectboxFieldTypes' => $selectboxFieldTypes,
			'singletonEdit'      => $singletonEdit,
			'singleton'          => $singleton
		];

		$this->set($data);
    }
	public function editData() 
    {
		$singleton = $this->Singletons->find()->where(['slug' => $this->request->getParam('data')]);
		if ($singleton->count() > 0) {
			$singletonData = $singleton->first();
			$savedData      = $this->SingletonDatas->get($this->request->getParam('id'));

			$medias = $this->Medias->find('all', [
				'order' => ['Medias.id' => 'DESC']])->contain('Admins');

			$data = [
				'pageTitle'      => $singletonData->name,
				'pageBreadcrumb' => 'Singletons::' . $singletonData->name . '::Edit Data',
				'singleton'     => $singletonData,
				'singletonData' => $savedData,
				'mediasForMd'    => $medias        
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

		$singletonAdd = new SingletonAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($singletonAdd->execute($this->request->getData())) {
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
					$findDuplicate = $this->Singletons->find()->where(['slug' => $slug]);
					if ($findDuplicate->count() >= 1) {
						$json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
					}
					else {
						$singleton = $this->Singletons->newEntity();
						$singleton = $this->Singletons->patchEntity($singleton, $requestArray);
						$singleton->fields = json_encode($this->request->getData('fields'));
						$singleton->admin_id = $sessionID;

						if ($this->Singletons->save($singleton)) {
							$recordId = $singleton->id;

							$singleton = $this->Singletons->get($recordId);

							// Tell system for new event
							$event = new Event('Model.BlogCategory.afterSave', $this, ['singleton' => $singleton, 'admin' => $admin, 'save' => 'new']);
							$this->getEventManager()->dispatch($event);

							$json = json_encode(['status' => 'ok', 'id' => $recordId, 'activity' => $event->getResult()]);

							$this->Flash->set($singleton->name . ' has been added in Singletons.', [
								'element' => 'Flash/Purple/success'
							]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
						}
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please add singleton field."]);
				}
			}
			else {
				$errors = $singletonAdd->getErrors();
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

		$singletonEdit = new SingletonEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($singletonEdit->execute($this->request->getData())) {
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
					$findDuplicate = $this->Singletons->find()->where(['slug' => $slug, 'id <>' => $requestData->id]);
					if ($findDuplicate->count() >= 1) {
						$json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
					}
					else {
						$singleton = $this->Singletons->get($requestData->id);
						$singleton = $this->Singletons->patchEntity($singleton, $requestArray);
						$singleton->fields = json_encode($this->request->getData('fields'));
						$singleton->admin_id = $sessionID;

						if ($this->Singletons->save($singleton)) {
							$recordId = $singleton->id;

							// Remove content in datas
							$listUid = [];
							foreach ($this->request->getData('fields') as $field) {
								$decodeField = json_decode($field, true);
								$uid = $decodeField['uid'];

								array_push($listUid, $uid);
							}

							$singletonDatas = $this->SingletonDatas->find()->where(['singleton_id' => $recordId]);

							if ($singletonDatas->count() > 0) {
								foreach ($singletonDatas as $collData) {
									$newContent = [];
									$content    = json_decode($collData->content, true);
									foreach ($content as $key => $value) {
										if (in_array($key, $listUid)) {
											$newContent[$key] = $value;
										}
									}

									$singletonData = $this->SingletonDatas->get($collData->id);
									$singletonData->content = json_encode($newContent);

									$this->SingletonDatas->save($singletonData);
								}
							}

							$singleton = $this->Singletons->get($recordId);

							// Tell system for new event
							$event = new Event('Model.BlogCategory.afterSave', $this, ['singleton' => $singleton, 'admin' => $admin, 'save' => 'update']);
							$this->getEventManager()->dispatch($event);

							$json = json_encode(['status' => 'ok', 'id' => $recordId, 'activity' => $event->getResult()]);

							$this->Flash->set($singleton->name . ' has been updated in Singletons.', [
								'element' => 'Flash/Purple/success'
							]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
						}
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Can't save data. Please add singleton field."]);
				}
			}
			else {
				$errors = $singletonEdit->getErrors();
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

        $singletonDelete  = new SingletonDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($singletonDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$singleton  = $this->Singletons->get($requestData->id);
				$name       = $singleton->name;
				$singleton->slug   = $singleton->slug . '-deleted';
				$singleton->status = '2';

                if ($this->Singletons->save($singleton)) {
					// Tell system for new event
					$event = new Event('Model.Singleton.afterDelete', $this, ['singleton' => $name, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set($singleton->name . ' has been deleted from Singletons.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $singletonDelete->getErrors();
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

			if (array_key_exists($requestData->key, $fieldTypes)) {
				$options = $fieldTypes[$requestData->key]['options'];
				if ($options == NULL) {
					$result = [];
				}
				else {
					$result = $options;
				}

				$json = json_encode(['status' => 'ok', 'options' => $result]);
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

			if (array_key_exists($requestData->field_type, $fieldTypes)) {
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
			unset($setData['singleton_id']);
			$singletonId = $this->request->getData('singleton_id');
			
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
				elseif ($value['field_type'] == 'image') {
					$originalPath        = $baseUrl . 'uploads/images/original/' . $value['value'];
					$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $value['value'];
					$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $value['value'];

					$newValue = [
						'full_path' => $originalPath,
						'thumbnail' => [
							'300x300' => $thumbnailSquarePath,
							'480x270' => $thumbnailLscapePath
						]
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
								]
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
							]
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

			$singletonData = $this->SingletonDatas->newEntity();
			$singletonData->content       = json_encode($newData);
			$singletonData->singleton_id  = $singletonId;
			$singletonData->admin_id      = $sessionID;

			if ($this->SingletonDatas->save($singletonData)) {
				$singlt = $this->Singletons->get($singletonId);

				// Tell system for new event
				$event = new Event('Model.Singleton.afterSaveData', $this, ['singleton' => $singlt, 'admin' => $admin, 'save' => 'new']);
				$this->getEventManager()->dispatch($event);

				$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

				$this->Flash->set('Data has been added in ' . $singlt->name . ' Singleton.', [
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
			unset($setData['singleton_id']);
			unset($setData['id']);
			$singletonId = $this->request->getData('singleton_id');
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
				elseif ($value['field_type'] == 'image') {
					$originalPath        = $baseUrl . 'uploads/images/original/' . $value['value'];
					$thumbnailSquarePath = $baseUrl . 'uploads/images/thumbnails/300x300/' . $value['value'];
					$thumbnailLscapePath = $baseUrl . 'uploads/images/thumbnails/480x270/' . $value['value'];

					$newValue = [
						'full_path' => $originalPath,
						'thumbnail' => [
							'300x300' => $thumbnailSquarePath,
							'480x270' => $thumbnailLscapePath
						]
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
								]
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
							]
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

			$singletonData = $this->SingletonDatas->get($dataId);
			$singletonData->content       = json_encode($newData);
			$singletonData->singleton_id = $singletonId;
			$singletonData->admin_id      = $sessionID;

			if ($this->SingletonDatas->save($singletonData)) {
				$singlt = $this->Singletons->get($singletonId);

				// Tell system for new event
				$event = new Event('Model.Singleton.afterSaveData', $this, ['singleton' => $singlt, 'admin' => $admin, 'save' => 'update']);
				$this->getEventManager()->dispatch($event);

				$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

				$this->Flash->set('Data has been updated in ' . $singlt->name . ' Singleton.', [
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

        $singletonDataDelete  = new SingletonDataDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($singletonDataDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$singletonData  = $this->SingletonDatas->get($requestData->id);
				$singletonId    = $singletonData->singleton_id;
				$singleton      = $this->Singletons->get($singletonId);

                if ($this->SingletonDatas->delete($singletonData)) {
					// Tell system for new event
					$event = new Event('Model.Singleton.afterDeleteData', $this, ['singleton' => ['singleton_name' => $singleton->name], 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set('Data has been deleted from ' . $singleton->name . ' Singleton.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $singletonDataDelete->getErrors();
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