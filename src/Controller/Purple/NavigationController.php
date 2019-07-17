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
use Cake\I18n\Time;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;

class NavigationController extends AppController
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
			$this->loadModel('Menus');
			$this->loadModel('Submenus');
			$this->loadModel('Pages');
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
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

				if (empty($this->request->getData('parent'))) {
					$menu = $this->Menus->newEntity();
	                $menu = $this->Menus->patchEntity($menu, $this->request->getData());
	                $menu->admin_id = $sessionID;
					if ($this->request->getData('point') == 'pages') {
						$menu->page_id = $this->request->getData('target');
					}
					elseif ($this->request->getData('point') == 'customlink') {
						$menu->target  = $this->request->getData('target');
						$menu->page_id = NULL;
					}

					if ($this->Menus->save($menu)) {
						$record_id = $menu->id;

						$menu = $this->Menus->get($record_id);
		                $menu->ordering = $record_id;
						$result = $this->Menus->save($menu);
						$menu = $this->Menus->get($record_id);

						/**
						 * Save user activity to histories table
						 * array $options => title, detail, admin_id
						 */

						$options = [
							'title'    => 'Addition of New Navigation',
							'detail'   => ' add '.$menu->title.' as a new navigation.',
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
				else {
					$submenu = $this->Submenus->newEntity();
	                $submenu = $this->Submenus->patchEntity($submenu, $this->request->getData());
					$submenu->menu_id  = $this->request->getData('parent');
	                $submenu->admin_id = $sessionID;
					if ($this->request->getData('point') == 'pages') {
						$submenu->page_id = $this->request->getData('target');
					}
					elseif ($this->request->getData('point') == 'customlink') {
						$submenu->target  = $this->request->getData('target');
						$submenu->page_id = NULL;
					}

					if ($this->Submenus->save($submenu)) {
						$record_id = $submenu->id;

						$submenu = $this->Submenus->get($record_id);
		                $submenu->ordering = $record_id;
						$result  = $this->Submenus->save($submenu);

						$menu = $this->Menus->get($this->request->getData('parent'));
		                $menu->has_sub = '1';
						$result  = $this->Menus->save($menu);
						$submenu = $this->Submenus->get($record_id);

						/**
						 * Save user activity to histories table
						 * array $options => title, detail, admin_id
						 */
						

						$options = [
							'title'    => 'Addition of New Navigation',
							'detail'   => ' add '.$submenu->title.' as a new navigation.',
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
				$errors = $menuAdd->errors();
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
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                if ($this->request->getData('navtype') == 'menu') {
	                $navTable = TableRegistry::get('Menus');

                }
                elseif ($this->request->getData('navtype') == 'submenu') {
	                $navTable = TableRegistry::get('Submenus');
                }

                $navigation       = $navTable->get($this->request->getData('id'));

                $navigation->title   = $this->request->getData('title');
                $navigation->status  = $this->request->getData('status');
                if ($this->request->getData('point') == 'pages') {
					$navigation->page_id = $this->request->getData('target');
				}
				elseif ($this->request->getData('point') == 'customlink') {
					$navigation->target  = $this->request->getData('target');
					$navigation->page_id = NULL;
				}

                if ($navTable->save($navigation)) {
                	/**
					 * Save user activity to histories table
					 * array $options => title, detail, admin_id
					 */
					
					$options = [
						'title'    => 'Data Change of a Navigation',
						'detail'   => ' change a navigation data.',
						'admin_id' => $sessionID
					];

					$historiesTable = TableRegistry::get('Histories');
					$saveActivity   = $historiesTable->saveActivity($options);

                    if ($saveActivity == true) {
	                    $json = json_encode(['status' => 'ok', 'activity' => true]);
	                }
	                else {
	                    $json = json_encode(['status' => 'ok', 'activity' => false]);
	                }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
                }
			}
			else {
				$errors = $menuEdit->errors();
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
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                if ($this->request->getData('navtype') == 'menu') {
	                $navTable = TableRegistry::get('Menus');

                }
                elseif ($this->request->getData('navtype') == 'submenu') {
					$navTable = TableRegistry::get('Submenus');
					$submenu  = $navTable->get($this->request->getData('id'));
					$parent   = $submenu->menu_id;
                }

                $query = $navTable->query()
                    ->delete()
                    ->where(['id' => $this->request->getData('id')]);
                $result = $query->execute();

                if ($result) {
                	if ($this->request->getData('navtype') == 'submenu') {
                		// Check total submenu for related parent
						$submenus = $navTable->find()->where(['menu_id' => $parent]);
                		if ($submenus->count() == 0) {
							$menusTable    = TableRegistry::get('Menus');
							$menu          = $menusTable->get($parent);
							$menu->has_sub = '0';
							if($menusTable->save($menu)) {
								/**
								 * Save user activity to histories table
								 * array $options => title, detail, admin_id
								 */
								
								$options = [
									'title'    => 'Deletion of a Navigation',
									'detail'   => ' delete a navigation.',
									'admin_id' => $sessionID
								];

								$historiesTable = TableRegistry::get('Histories');
								$saveActivity   = $historiesTable->saveActivity($options);

			                    if ($saveActivity == true) {
				                    $json = json_encode(['status' => 'ok', 'activity' => true]);
				                }
				                else {
				                    $json = json_encode(['status' => 'ok', 'activity' => false]);
				                }
							}
							else {
			                    $json = json_encode(['status' => 'ok', 'note' => "Can't update parent status."]);
							}
                		}
                		else {
                			/**
							 * Save user activity to histories table
							 * array $options => title, detail, admin_id
							 */
							
							$options = [
								'title'    => 'Deletion of a Navigation',
								'detail'   => ' delete a navigation.',
								'admin_id' => $sessionID
							];

							$historiesTable = TableRegistry::get('Histories');
							$saveActivity   = $historiesTable->saveActivity($options);

		                    if ($saveActivity == true) {
			                    $json = json_encode(['status' => 'ok', 'activity' => true]);
			                }
			                else {
			                    $json = json_encode(['status' => 'ok', 'activity' => false]);
			                }
                		}
                	}
                	else {
	                    /**
						 * Save user activity to histories table
						 * array $options => title, detail, admin_id
						 */
						
						$options = [
							'title'    => 'Deletion of a Navigation',
							'detail'   => ' delete a navigation.',
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
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $menuDelete->errors();
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
			$order = $this->request->getData('order');
			$explodeOrder = explode(',', $order);

			$count = 1;
			foreach ($explodeOrder as $newOrder) {
				$menu = $this->Menus->get($newOrder);
				$menu->ordering = $count;
				$result = $this->Menus->save($menu);
				$count++;
			}

			$json = json_encode(['status' => 'ok']);
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
			$order = $this->request->getData('order');
			$explodeOrder = explode(',', $order);

			$count = 1;
			foreach ($explodeOrder as $newOrder) {
				$submenu = $this->Submenus->get($newOrder);
				$submenu->ordering = $count;
				$result = $this->Submenus->save($submenu);
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