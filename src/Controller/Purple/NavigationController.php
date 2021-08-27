<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\MenuAddForm;
use App\Form\Purple\MenuEditForm;
use App\Form\Purple\MenuDeleteForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectPlugins;
use Particle\Filter\Filter;

class NavigationController extends AppController
{
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
		
		$this->loadComponent('Flash');

		// Get Admin Session data
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		$this->viewBuilder()->setLayout('dashboard');
		$this->loadModel('Admins');
		$this->loadModel('Menus');
		$this->loadModel('Submenus');
		$this->loadModel('Pages');
		$this->loadModel('Settings');

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

			$dashboardSearch = new SearchForm();
			
			// Plugins List
			$purplePlugins 	= new PurpleProjectPlugins();
			$plugins		= $purplePlugins->purplePlugins();
			$this->set('plugins', $plugins);

			if ($adminData->level == 1) {
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
					'dashboardSearch'   => $dashboardSearch,
					'title'             => 'Navigation | Purple CMS',
					'pageTitle'         => 'Navigation',
					'pageTitleIcon'     => 'mdi-menu',
					'pageBreadcrumb'    => 'Navigation',
					'appearanceFavicon' => $queryFavicon
				];
				$this->set($data);
			}
			else {
				return $this->redirect(
					['controller' => 'Dashboard', 'action' => 'index']
				);
			}
		}
		else {
			return $this->redirect(
				['controller' => 'Authenticate', 'action' => 'login']
			);
		}
	}
    public function index($parent = NULL)
    {
        $menuAdd     = new MenuAddForm();
        $menuEdit    = new MenuEditForm();
        $menuDelete  = new MenuDeleteForm();

        $data = [
			'pageTitle'           => 'Navigation',
			'pageBreadcrumb'      => 'Navigation',
            'menuAdd'             => $menuAdd,
            'menuEdit'            => $menuEdit,
            'menuDelete'          => $menuDelete
		];

        $menus = $this->Menus->find('all', ['contain' => ['Admins', 'Pages']])->order(['Menus.ordering' => 'ASC']);
    	$this->set(compact('menus'));

        $submenus = $this->Submenus->find('all', ['contain' => ['Admins', 'Pages']])->where(['Submenus.menu_id' => $parent])->order(['Submenus.ordering' => 'ASC']);
    	$this->set(compact('submenus'));

		if ($parent != NULL) {
			$activeParent = $this->Menus->get($parent);
			$this->set('activeParent', $activeParent);
		}

        $pages = $this->Pages->find('all', ['contain' => ['PageTemplates']])->order(['Pages.id' => 'DESC']);
    	$this->set(compact('pages'));

		$this->set($data);
    }
    public function ajaxAdd()
    {
        $this->viewBuilder()->enableAutoLayout(false);

		$menuAdd = new MenuAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($menuAdd->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['title', 'status', 'point', 'target'])->trim()->stripHtml();
				if (!empty($this->request->getData('parent'))) {
					$filter->values(['parent'])->int();
				}
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

				if (empty($this->request->getData('parent'))) {
					$menu = $this->Menus->newEntity();
	                $menu = $this->Menus->patchEntity($menu, $requestArray);
	                $menu->admin_id = $sessionID;
					if ($requestData->point == 'pages') {
						$menu->page_id = $requestData->target;
					}
					elseif ($requestData->point == 'customlink') {
						$menu->target  = $requestData->target;
						$menu->page_id = NULL;
					}

					if ($this->Menus->save($menu)) {
						$recordId = $menu->id;

						$menu           = $this->Menus->get($recordId);
		                $menu->ordering = $recordId;
						$result         = $this->Menus->save($menu);
						$menu           = $this->Menus->get($recordId);

						// Tell system for new event
						$event = new Event('Model.Navigation.afterSave', $this, ['navigation' => $menu, 'admin' => $admin, 'save' => 'new']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

						$this->Flash->set($menu->title . ' has been added.', [
							'element' => 'Flash/Purple/success'
						]);
	                }
	                else {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
	                }
				}
				else {
					$submenu = $this->Submenus->newEntity();
	                $submenu = $this->Submenus->patchEntity($submenu, $requestArray);
					$submenu->menu_id  = $this->request->getData('parent');
	                $submenu->admin_id = $sessionID;
					if ($requestData->point == 'pages') {
						$submenu->page_id = $requestData->target;
					}
					elseif ($requestData->point == 'customlink') {
						$submenu->target  = $requestData->target;
						$submenu->page_id = NULL;
					}

					if ($this->Submenus->save($submenu)) {
						$recordId = $submenu->id;

						$submenu 		   = $this->Submenus->get($recordId);
		                $submenu->ordering = $recordId;
						$result  		   = $this->Submenus->save($submenu);

						$menu 		   = $this->Menus->get($requestData->parent);
		                $menu->has_sub = '1';
						$result  	   = $this->Menus->save($menu);
						$submenu 	   = $this->Submenus->get($recordId);

						// Tell system for new event
						$event = new Event('Model.Navigation.afterSave', $this, ['navigation' => $submenu, 'admin' => $admin, 'save' => 'new']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

						$this->Flash->set('Submenu ' . $submenu->title . ' has been added.', [
							'element' => 'Flash/Purple/success'
						]);
	                }
	                else {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
	                }
				}
			}
			else {
				$errors = $menuAdd->getErrors();
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

		$menuEdit = new MenuEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
        	if ($menuEdit->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['title', 'status', 'point', 'target'])->trim()->stripHtml();
				$filter->values(['id'])->int();
				if (!empty($this->request->getData('parent'))) {
					$filter->values(['parent'])->int();
				}
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                if ($this->request->getData('navtype') == 'menu') {
	                $navTable = TableRegistry::getTableLocator()->get('Menus');

                }
                elseif ($this->request->getData('navtype') == 'submenu') {
	                $navTable = TableRegistry::getTableLocator()->get('Submenus');
                }

                $navigation = $navTable->get($this->request->getData('id'));

                $navigation->title   = $requestData->title;
                $navigation->status  = $requestData->status;
                if ($requestData->point == 'pages') {
					$navigation->page_id = $requestData->target;
				}
				elseif ($requestData->point == 'customlink') {
					$navigation->target  = $requestData->target;
					$navigation->page_id = NULL;
				}

                if ($navTable->save($navigation)) {
					// Tell system for new event
					$event = new Event('Model.Navigation.afterSave', $this, ['navigation' => $navigation, 'admin' => $admin, 'save' => 'update']);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set($navigation->title . ' has been updated.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
                }
			}
			else {
				$errors = $menuEdit->getErrors();
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

        $menuDelete = new MenuDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($menuDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['navtype'])->trim()->stripHtml();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                if ($requestData->navtype == 'menu') {
	                $navTable = TableRegistry::getTableLocator()->get('Menus');

                }
                elseif ($requestData->navtype == 'submenu') {
					$navTable = TableRegistry::getTableLocator()->get('Submenus');
					$submenu  = $navTable->get($requestData->id);
					$parent   = $submenu->menu_id;
				}
				
				$navData = $navTable->get($requestData->id);

                $query = $navTable->query()
                    ->delete()
                    ->where(['id' => $requestData->id]);
                $result = $query->execute();

                if ($result) {
                	if ($requestData->navtype == 'submenu') {
                		// Check total submenu for related parent
						$submenus = $navTable->find()->where(['menu_id' => $parent]);
                		if ($submenus->count() == 0) {
							$menusTable    = TableRegistry::getTableLocator()->get('Menus');
							$menu          = $menusTable->get($parent);
							$menu->has_sub = '0';
							if($menusTable->save($menu)) {
								// Tell system for new event
								$event = new Event('Model.Navigation.afterDelete', $this, ['navigation' => $navData, 'admin' => $admin]);
								$this->getEventManager()->dispatch($event);

								$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
							}
							else {
			                    $json = json_encode(['status' => 'ok', 'note' => "Can't update parent status."]);
							}
                		}
                		else {
                			// Tell system for new event
							$event = new Event('Model.Navigation.afterDelete', $this, ['navigation' => $navData, 'admin' => $admin]);
							$this->getEventManager()->dispatch($event);

							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
                		}
                	}
                	else {
	                    // Tell system for new event
						$event = new Event('Model.Navigation.afterDelete', $this, ['navigation' => $navData, 'admin' => $admin]);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
	                }

					$this->Flash->set($navData->title . ' has been deleted.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $menuDelete->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
	public function ajaxReorderMenu()
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
				$menu = $this->Menus->get($newOrder);
				$menu->ordering = $count;
				if ($this->Menus->save($menu)) {
					array_push($resultArray, 1);
				}
				else {
					array_push($resultArray, 0);
				}

				$count++;
			}

			if (!in_array(0, $resultArray)) {
				// Tell system for new event
				$event = new Event('Model.Navigation.afterReorder', $this, ['admin' => $admin, 'type' => 'menu']);
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
	public function ajaxReorderSubmenu()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$session   = $this->getRequest()->getSession();
			$sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

			$parent = $this->request->getData('parent');
			$order  = $this->request->getData('order');
			$explodeOrder = explode(',', $order);

			$parentMenu = $this->Menus->get($parent);

			$count = 1;
			$resultArray = [];
			foreach ($explodeOrder as $newOrder) {
				$submenu = $this->Submenus->get($newOrder);
				$submenu->ordering = $count;
				if ($this->Submenus->save($submenu)) {
					array_push($resultArray, 1);
				}
				else {
					array_push($resultArray, 0);
				}

				$count++;
			}

			if (!in_array(0, $resultArray)) {
				// Tell system for new event
				$event = new Event('Model.Navigation.afterReorder', $this, ['admin' => $admin, 'type' => 'submenu', 'parent' => $parentMenu]);
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