<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;

class NotificationsController extends AppController
{
	public $notificationsLimit = 10;

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
					'dashboardSearch'   => $dashboardSearch,
					'title'             => 'Notifications | Purple CMS',
					'pageTitle'         => 'Notifications',
					'pageTitleIcon'     => 'mdi-bell-outline',
					'pageBreadcrumb'    => 'Notifications',
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
	public function index($id = 1)
	{
        $notifications = $this->Notifications->find()->order(['id' => 'DESC']);

        $data = [
			'notificationsTotal' => $notifications->count(),
			'notificationsLimit' => $this->notificationsLimit
		];

		$this->paginate = [
			'limit' => $this->notificationsLimit,
			'page'  => $id
		];
		$notificationsList = $this->paginate($notifications);
	    $this->set('notifications', $notificationsList);
    	$this->set($data);
	}
	public function filter($filter, $id = 1)
	{
		if ($filter == 'unread') {
	        $notifications = $this->Notifications->find()->where(['is_read' => '0'])->order(['id' => 'DESC']);
	    }
	    elseif($filter == 'read') {
	        $notifications = $this->Notifications->find()->where(['is_read' => '1'])->order(['id' => 'DESC']);
	    }
	    else {
	        throw new NotFoundException(__('Page not found'));
	    }

        $data = [
			'notificationsTotal' => $notifications->count(),
			'notificationsLimit' => $this->notificationsLimit
		];

		$this->paginate = [
			'limit' => $this->notificationsLimit,
			'page'  => $id
		];
		$notificationsList = $this->paginate($notifications);
	    $this->set('notifications', $notificationsList);
    	$this->set($data);
	}
	public function ajaxLoadHeaderNotifications()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
        	$unreadNotifications = $this->Notifications->find()->where(['is_read' => '0'])->order(['id' => 'DESC']);

	        $data = [
				'notificationsTotal' => $unreadNotifications->count(),
			];

	        $notifications = $this->Notifications->find()->where(['is_read' => '0'])->order(['id' => 'DESC'])->limit(3);
	    	$this->set(compact('notifications'));
	    	$this->set($data);
	        $this->render();
	    }
	    else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
	public function ajaxReadNotification()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');

            $notification = $this->Notifications->get($this->request->getData('id'));
            $notification->is_read = '1';

			if ($this->Notifications->save($notification)) {
				$record_id = $notification->id;

				$notification = $this->Notifications->get($record_id);

				/**
				 * Save user activity to histories table
				 * array $options => title, detail, admin_id
				 */

				$options = [
					'title'    => 'Read a Notification',
					'detail'   => ' read the notification message.',
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

			$this->set(['json' => $json]);
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}