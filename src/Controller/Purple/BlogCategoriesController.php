<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\BlogCategoryAddForm;
use App\Form\Purple\BlogCategoryEditForm;
use App\Form\Purple\BlogCategoryDeleteForm;
use App\Form\Purple\SearchForm;
use Cake\Utility\Text;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;
use Particle\Filter\Filter;

class BlogCategoriesController extends AppController
{
    public $categoriesLimit  = 10;

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
				['_name' => 'adminLogin']
			);
		}
	}
	public function initialize()
	{
		parent::initialize();

		// Get Admin Session data
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		// Set layout
		$this->viewBuilder()->setLayout('dashboard');

		// Load other models
		$this->loadModel('Admins');
		$this->loadModel('Blogs');
		$this->loadModel('Settings');

		// Check debug is on or off
		if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
			$cakeDebug = 'on';
		} 
		else {
			$cakeDebug = 'off';
		}

		$queryAdmin   = $this->Admins->signedInUser($sessionID, $sessionPassword);
		$queryFavicon = $this->Settings->fetch('favicon');

		$rowCount = $queryAdmin->count();
		if ($rowCount > 0) {
			$adminData = $queryAdmin->first();

			// Dashboard search form
			$dashboardSearch = new SearchForm();
			
			// Plugins List
			$purplePlugins 	= new PurpleProjectPlugins();
			$plugins		= $purplePlugins->purplePlugins();
			$this->set('plugins', $plugins);

			$data = [
				'sessionHost'       => $sessionHost,
				'sessionID'         => $sessionID,
				'sessionPassword'   => $sessionPassword,
				'cakeDebug'         => $cakeDebug,
				'adminName' 	    => ucwords($adminData->display_name),
				'adminLevel' 	    => $adminData->level,
				'adminEmail' 	    => $adminData->email,
				'adminPhoto' 	    => $adminData->photo,
				'greeting'          => '',
				'dashboardSearch'	=> $dashboardSearch,
				'title'             => 'Post Categories | Purple CMS',
				'pageTitle'         => 'Post Categories',
				'pageTitleIcon'     => 'mdi-folder-multiple-outline',
				'pageBreadcrumb'    => 'Post Categories',
				'appearanceFavicon' => $queryFavicon
			];
			$this->set($data);
		}
		else {
			return $this->redirect(
				['controller' => 'Authenticate', 'action' => 'login']
			);
		}
	}
	public function index() 
	{
		$blogCategoryAdd     = new BlogCategoryAddForm();
        $blogCategoryEdit    = new BlogCategoryEditForm();
        $blogCategoryDelete  = new BlogCategoryDeleteForm();

		if ($this->request->getParam('id') == NULL) {
            $blogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.page_id IS' => NULL])->order(['BlogCategories.ordering' => 'ASC']);
		}
		else {
            $blogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.page_id' => $this->request->getParam('id')])->order(['BlogCategories.ordering' => 'ASC']);
		}
		$blogs = $this->Blogs->find();

		$data = [
			'blogCategoryAdd'    => $blogCategoryAdd,
			'blogCategoryEdit'   => $blogCategoryEdit,
			'blogCategoryDelete' => $blogCategoryDelete,
			'blogs'				 => $blogs
		];

        $this->set(compact('blogCategories'));
		$this->set($data);
	}
	public function ajaxLoadSelectbox()
	{
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			// Sanitize user input
			$filter = new Filter();
			if ($this->request->getData('page') == 'NULL') {
				$filter->values(['page'])->trim()->stripHtml();
			}
			else {
				$filter->values(['page'])->int();
			}
			$filterResult = $filter->filter($this->request->getData());
			$requestData  = json_decode(json_encode($filterResult), FALSE);
			
        	$page = $requestData->page;

        	if ($page == 'NULL') {
	        	$blogCategoriesArray = $this->BlogCategories->find('list')->select(['id','name'])->where(['page_id IS' => NULL])->order(['ordering' => 'ASC'])->toArray();
        	}
        	else {
	        	$blogCategoriesArray = $this->BlogCategories->find('list')->select(['id','name'])->where(['page_id' => $page])->order(['ordering' => 'ASC'])->toArray();

        	}
	    	$this->set(compact('blogCategoriesArray'));
	    	$this->render();
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxAdd()
	{
        $this->viewBuilder()->enableAutoLayout(false);

		$blogCategoryAdd = new BlogCategoryAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($blogCategoryAdd->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['name'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $slug = Text::slug(strtolower($requestData->name));
                $findDuplicate = $this->BlogCategories->find()->where(['slug' => $slug]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
                }
                else {
					$blogCategory = $this->BlogCategories->newEntity();
	                $blogCategory = $this->BlogCategories->patchEntity($blogCategory, $requestArray);
	                $blogCategory->admin_id = $sessionID;

					if ($this->BlogCategories->save($blogCategory)) {
						$recordId = $blogCategory->id;

						$blogCategory = $this->BlogCategories->get($recordId);
		                $blogCategory->ordering = $recordId;
						$result       = $this->BlogCategories->save($blogCategory);
						$blogCategory = $this->BlogCategories->get($recordId);

						// Tell system for new event
						$event = new Event('Model.BlogCategory.afterSave', $this, ['category' => $blogCategory, 'admin' => $admin, 'save' => 'new']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'id' => $recordId, 'activity' => $event->getResult()]);
	                }
	                else {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
	                }
	            }
			}
			else {
				$errors = $blogCategoryAdd->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
			}

			$this->set(['json' => $json]);
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxUpdate()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$blogCategoryEdit = new BlogCategoryEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($blogCategoryEdit->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['name'])->trim()->stripHtml();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $slug = Text::slug(strtolower($requestData->name));
                $findDuplicate = $this->BlogCategories->find()->where(['slug' => $slug, 'id <>' => $requestData->id]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
                }
                else {
	                $category = $this->BlogCategories->get($requestData->id);
	                $category->admin_id = $sessionID;

					$this->BlogCategories->patchEntity($category, $requestArray);

					if ($this->BlogCategories->save($category)) {
						$recordId = $category->id;

						$category  = $this->BlogCategories->get($recordId);

						// Tell system for new event
						$event = new Event('Model.BlogCategory.afterSave', $this, ['category' => $category, 'admin' => $admin, 'save' => 'update']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'id' => $recordId, 'activity' => $event->getResult()]);
	                }
	                else {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
	                }
	            }
			}
			else {
				$errors = $blogCategoryEdit->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
			}

			$this->set(['json' => $json]);
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxDelete()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $blogCategoryDelete  = new BlogCategoryDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($blogCategoryDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$category  = $this->BlogCategories->get($requestData->id);
				$name      = $category->name;

				$result = $this->BlogCategories->delete($category);

                if ($result) {
					// Tell system for new event
					$event = new Event('Model.BlogCategory.afterDelete', $this, ['category' => $name, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $blogCategoryDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
	public function ajaxReorder() 
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$session   = $this->getRequest()->getSession();
			$sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

			$order = $this->request->getData('order');
			$explodeOrder = explode(',', $order);

			$count = 1;
			$resultArray = [];
			foreach ($explodeOrder as $newOrder) {
				$category = $this->BlogCategories->get($newOrder);
				$category->ordering = $count;
				if ($this->BlogCategories->save($category)) {
					array_push($resultArray, 1);
				}
				else {
					array_push($resultArray, 0);
				}

				$count++;
			}

			if (!in_array(0, $resultArray)) {
				// Tell system for new event
				$event = new Event('Model.BlogCategory.afterReorder', $this, ['admin' => $admin]);
				$this->getEventManager()->dispatch($event);

				$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
			}
			else {
				$json = json_encode(['status' => 'error',]);
			}

			$this->set(['json' => $json]);
		}
		else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}