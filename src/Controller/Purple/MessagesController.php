<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;
use App\Form\Purple\MessageDeleteForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;

class MessagesController extends AppController
{
	public $messagesLimit = 10;

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
					'title'             => 'Messages | Purple CMS',
					'pageTitle'         => 'Messages',
					'pageTitleIcon'     => 'mdi-email-outline',
					'pageBreadcrumb'    => 'Messages',
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
        $messages = $this->Messages->find()->where(['folder' => 'inbox'])->order(['id' => 'DESC']);

        $data = [
			'messagesTotal' => $messages->count(),
			'messagesLimit' => $this->messagesLimit
		];

		$this->paginate = [
			'limit' => $this->messagesLimit,
			'page'  => $id
		];
		$messagesList = $this->paginate($messages);
	    $this->set('messages', $messagesList);
    	$this->set($data);
	}
	public function folder($folder, $id = 1)
	{
		if ($folder == 'inbox') {
	        $messages = $this->Messages->find()->where(['folder' => 'inbox'])->order(['id' => 'DESC']);
	    }
	    elseif($folder == 'trash') {
	        $messages = $this->Messages->find()->where(['folder' => 'trash'])->order(['id' => 'DESC']);
	    }
	    else {
	        throw new NotFoundException(__('Page not found'));
	    }

        $data = [
			'messagesTotal'  => $messages->count(),
			'messagesLimit'  => $this->messagesLimit,
			'pageBreadcrumb' => 'Messages::'.ucwords($folder),
		];

		$this->paginate = [
			'limit' => $this->messagesLimit,
			'page'  => $id
		];
		$messagesList = $this->paginate($messages);
	    $this->set('messages', $messagesList);
    	$this->set($data);
	}
	public function detail($id)
	{
        $message = $this->Messages->find()->where(['id' => $id]);
        if ($message->count() > 0) {
			$messageDelete = new MessageDeleteForm();

        	$data = [
				'pageBreadcrumb' => 'Messages::Detail',
				'messageDelete'  => $messageDelete
	    	];
        	$this->set($data);
		    $this->set('message', $message->first());
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
	}
	public function ajaxMessagesCounter()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax')) {
	        $messages = $this->Messages->find()->where(['folder' => 'inbox', 'is_read' => '0'])->order(['id' => 'DESC']);
		    $this->set('counter', $messages->count());
        }
	    else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxReadMessage()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');

            $message = $this->Messages->get($this->request->getData('id'));
            $message->is_read = '1';

			if ($this->Messages->save($message)) {
				$record_id = $message->id;

				$message = $this->Messages->get($record_id);

				/**
				 * Save user activity to histories table
				 * array $options => title, detail, admin_id
				 */

				$options = [
					'title'    => 'Read a Message',
					'detail'   => ' read the contact message.',
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
	public function ajaxMoveToTrash()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$messageDelete = new MessageDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($messageDelete->execute($this->request->getData())) {
				$session   = $this->getRequest()->getSession();
	            $sessionID = $session->read('Admin.id');

	            $message = $this->Messages->get($this->request->getData('id'));
	            $message->folder  = 'trash';
	            $message->is_read = '1';

				if ($this->Messages->save($message)) {
					$record_id    = $message->id;
					$record_from  = $message->name;
					$record_email = $message->email;

					$message = $this->Messages->get($record_id);

					/**
					 * Save user activity to histories table
					 * array $options => title, detail, admin_id
					 */

					$options = [
						'title'    => 'Delete a Message',
						'detail'   => ' move a contact message from '.$record_from.' ('.$record_email.') to trash.',
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
            	$errors = $messageDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}