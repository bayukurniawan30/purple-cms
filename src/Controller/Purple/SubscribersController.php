<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\SubscriberAddForm;
use App\Form\Purple\SubscriberEditForm;
use App\Form\Purple\SubscriberDeleteForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;

class SubscribersController extends AppController
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

            if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
                $cakeDebug = 'on';
            } 
            else {
                $cakeDebug = 'off';
            }

			$queryAdmin      = $this->Admins->find()->where(['id' => $sessionID, 'password' => $sessionPassword])->limit(1);
			$queryFavicon    = $this->Settings->find()->where(['name' => 'favicon'])->first();
			$queryDateFormat = $this->Settings->find()->where(['name' => 'dateformat'])->first();
			$queryTimeFormat = $this->Settings->find()->where(['name' => 'timeformat'])->first();

			$rowCount = $queryAdmin->count();
			if ($rowCount > 0) {
				$adminData = $queryAdmin->first();
				
                $dashboardSearch = new SearchForm();
                
                if ($adminData->level == 1) {
					$data = [
						'sessionHost'        => $sessionHost,
						'sessionID'          => $sessionID,
						'sessionPassword'    => $sessionPassword,
						'cakeDebug'          => $cakeDebug,
						'adminName'          => ucwords($adminData->display_name),
						'adminLevel'         => $adminData->level,
						'adminEmail'         => $adminData->email,
						'adminPhoto'         => $adminData->photo,
						'greeting'           => '',
						'dashboardSearch'    => $dashboardSearch,
						'title'              => 'Subscibers | Purple CMS',
						'pageTitle'          => 'Subscibers',
						'pageTitleIcon'      => 'mdi-book-open',
						'pageBreadcrumb'     => 'Socials::Subscibers',
						'appearanceFavicon'  => $queryFavicon,
						'settingsDateFormat' => $queryDateFormat->value,
						'settingsTimeFormat' => $queryTimeFormat->value,
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
	public function index()
	{
		$subscriberAdd    = new SubscriberAddForm();
		$subscriberEdit   = new SubscriberEditForm();
		$subscriberDelete = new SubscriberDeleteForm();

		$subscribers = $this->Subscribers->find()->order(['id' => 'DESC']);

		$data = [
			'subscriberAdd'    => $subscriberAdd,
			'subscriberEdit'   => $subscriberEdit,
			'subscriberDelete' => $subscriberDelete,
		];

    	$this->set(compact('subscribers'));
		$this->set($data);
	}
	public function download()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');

		if ($this->request->getEnv('HTTP_HOST') != $sessionHost || !$session->check('Admin.id')) {
	        throw new NotFoundException(__('Page not found'));
		}
		else {
			$subscribers = $this->Subscribers->find()->order(['id' => 'DESC']);

			$content = '';
			if ($subscribers->count() > 0) {
				foreach ($subscribers as $subscriber) {
					$content .= $subscriber->email . "\n";
				}
			}

			$response = $this->response->withStringBody($content);
			$response = $this->response->withFile(WWW_ROOT . 'exports' . DS . 'subscribers.txt' ,
				array('download'=> true, 'name'=> 'subscribers.txt'));
			return $response;
		}
	}
	public function ajaxAdd()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$subscriberAdd = new SubscriberAddForm();
        if ($this->request->is('ajax')) {
            if ($subscriberAdd->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                if ($verifyEmail == true) {
                	$findDuplicate = $this->Subscribers->find()->where(['email' => $this->request->getData('email')]);

                    if ($findDuplicate->count() >= 1) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another email."]);
                    }
                    else {
                    	$subscriber = $this->Subscribers->newEntity();
                        $subscriber = $this->Subscribers->patchEntity($subscriber, $this->request->getData());
                        $subscriber->status = 'active';
    				
    	                if ($this->Subscribers->save($subscriber)) {
    	                	$record_id = $subscriber->id;

							$subscriber = $this->Subscribers->get($record_id);
    	                	/**
							 * Save user activity to histories table
							 * array $options => title, detail, admin_id
							 */

							$options = [
								'title'    => 'Addition of New Subscriber',
								'detail'   => ' add '.$subscriber->email.' as a new subscriber.',
								'admin_id' => $sessionID
							];

							$this->loadModel('Histories');
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
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
                }
            }
            else {
            	$errors = $subscriberAdd->errors();
                $json = json_encode(['status' => 'error', 'error' => "Make sure you don't enter the same username or email and please fill all field."]);
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

		$subscriberEdit = new SubscriberEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($subscriberEdit->execute($this->request->getData())) {
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                if ($verifyEmail == true) {
	                $findDuplicate = $this->Subscribers->find()->where(['email' => $this->request->getData('email')]);

	                if ($findDuplicate->count() >= 1) {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another email."]);
	                }
	                else {
		                $subscriber = $this->Subscribers->get($this->request->getData('id'));
						$this->Subscribers->patchEntity($subscriber, $this->request->getData());

						if ($this->Subscribers->save($subscriber)) {
							$record_id = $subscriber->id;

							$subscriber = $this->Subscribers->get($record_id);

							/**
							 * Save user activity to histories table
							 * array $options => title, detail, admin_id
							 */

							$options = [
								'title'    => 'Data Change of a Subscriber',
								'detail'   => ' update '.$subscriber->email.' data from subscriber.',
								'admin_id' => $sessionID
							];

							$this->loadModel('Histories');
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
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
                }
			}
			else {
				$errors = $subscriberEdit->errors();
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

		$subscriberDelete = new SubscriberDeleteForm();
        if ($this->request->is('ajax')) {
            if ($subscriberDelete->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
				$subscriber = $this->Subscribers->get($this->request->getData('id'));
				$email      = $subscriber->email;

				$result = $this->Subscribers->delete($subscriber);

                if ($result) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a Subscriber',
                        'detail'   => ' delete '.$email.' from subscriber.',
                        'admin_id' => $sessionID
                    ];

                    $this->loadModel('Histories');
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
            	$errors = $subscriberDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxExport()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax')) {
			$file = new File(WWW_ROOT . 'exports' . DS . 'subscribers.txt', true);

			$subscribers = $this->Subscribers->find()->order(['id' => 'DESC']);

			if ($subscribers->count() > 0) {
				$json = json_encode(['status' => 'ok', 'url' => $this->request->getAttribute('webroot').'exports/subscribers.txt']);
			}
			else {
				$json = json_encode(['status' => 'error', 'error' => "Can't export subscribers data. Empty data."]);
			}
			
            $this->set(['json' => $json]);
		}
		else {
			throw new NotFoundException(__('Page not found'));
		}
	}
}