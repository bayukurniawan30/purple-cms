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
use Cake\I18n\Time;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;

class BlogCategoriesController extends AppController
{
	public function beforeFilter(Event $event)
	{
	    parent::beforeFilter($event);
	    $purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo == 'default') {
			return $this->redirect(
	            ['prefix' => false, 'controller' => 'Setup', 'action' => 'index']
	        );
		}
	}
	public function initialize()
	{
		parent::initialize();
        $this->loadComponent('RequestHandler');
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		if ($this->request->getEnv('HTTP_HOST') != $sessionHost || !$session->check('Admin.id')) {
			return $this->redirect(
	            ['controller' => 'Authenticate', 'action' => 'login']
	        );
		}
		else {
	    	$this->viewBuilder()->setLayout('dashboard');
	    	$this->loadModel('Admins');
			$this->loadModel('Blogs');
			$this->loadModel('Settings');
			$this->loadModel('Histories');

            if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
                $cakeDebug = 'on';
            } 
            else {
                $cakeDebug = 'off';
            }

			$queryAdmin   = $this->Admins->find()->where(['id' => $sessionID, 'password' => $sessionPassword])->limit(1);
            $queryFavicon = $this->Settings->find()->where(['name' => 'favicon'])->first();

			$rowCount = $queryAdmin->count();
			if ($rowCount > 0) {
				$adminData = $queryAdmin->first();

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
        	$page = $this->request->getData('page');

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
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $slug = Text::slug(strtolower($this->request->getData('name')));
                $findDuplicate = $this->BlogCategories->find()->where(['slug' => $slug]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
                }
                else {
					$blogCategory = $this->BlogCategories->newEntity();
	                $blogCategory = $this->BlogCategories->patchEntity($blogCategory, $this->request->getData());
	                $blogCategory->admin_id = $sessionID;

					if ($this->BlogCategories->save($blogCategory)) {
						$record_id = $blogCategory->id;

						$blogCategory = $this->BlogCategories->get($record_id);
		                $blogCategory->ordering = $record_id;
						$result       = $this->BlogCategories->save($blogCategory);
						$blogCategory = $this->BlogCategories->get($record_id);

						/**
						 * Save user activity to histories table
						 * array $options => title, detail, admin_id
						 */

						$options = [
							'title'    => 'Addition of New Post Category',
							'detail'   => ' add '.$blogCategory->name.' as a new post category.',
							'admin_id' => $sessionID
						];

	                    $saveActivity   = $this->Histories->saveActivity($options);

						if ($saveActivity == true) {
		                    $json = json_encode(['status' => 'ok', 'id' => $record_id, 'activity' => true]);
		                }
		                else {
		                    $json = json_encode(['status' => 'ok', 'id' => $record_id, 'activity' => false]);
		                }
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
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $slug = Text::slug(strtolower($this->request->getData('name')));
                $findDuplicate = $this->BlogCategories->find()->where(['slug' => $slug]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another name."]);
                }
                else {
	                $category = $this->BlogCategories->get($this->request->getData('id'));
	                $category->admin_id = $sessionID;

					$this->BlogCategories->patchEntity($category, $this->request->getData());

					if ($this->BlogCategories->save($category)) {
						$record_id = $category->id;

						$category  = $this->BlogCategories->get($record_id);

						/**
						 * Save user activity to histories table
						 * array $options => title, detail, admin_id
						 */

						$options = [
							'title'    => 'Data Change of a Post Category',
							'detail'   => ' update '.$category->name.' data from post category.',
							'admin_id' => $sessionID
						];

	                    $saveActivity   = $this->Histories->saveActivity($options);

						if ($saveActivity == true) {
		                    $json = json_encode(['status' => 'ok', 'activity' => true]);
		                }
		                else {
		                    $json = json_encode(['status' => 'ok', 'activity' => false]);
		                }
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
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
				$category  = $this->BlogCategories->get($this->request->getData('id'));
				$name      = $category->name;

				$result = $this->BlogCategories->delete($category);

                if ($result) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a Post Category',
                        'detail'   => ' delete '.$name.' from post category.',
                        'admin_id' => $sessionID
                    ];

                    $saveActivity   = $this->Histories->saveActivity($options);

                    if ($saveActivity == true) {
                        $json = json_encode(['status' => 'ok', 'activity' => true]);
                    }
                    else {
                        $json = json_encode(['status' => 'ok', 'activity' => false]);
                    }
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
			$order = $this->request->getData('order');
			$explodeOrder = explode(',', $order);

			$count = 1;
			foreach ($explodeOrder as $newOrder) {
				$menu = $this->BlogCategories->get($newOrder);
				$menu->ordering = $count;
				$result = $this->BlogCategories->save($menu);
				$count++;
			}

			$json = json_encode(['status' => 'ok']);
			$this->set(['json' => $json]);
		}
		else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}