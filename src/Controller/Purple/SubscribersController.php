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
use App\Form\Purple\SubscriberMailchimpSettingsForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use App\Purple\PurpleProjectPlugins;
use \DrewM\MailChimp\MailChimp;
use \DrewM\MailChimp\Batch;

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
			$this->loadModel('Histories');

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
				
				// Plugins List
				$purplePlugins 	= new PurpleProjectPlugins();
				$plugins		= $purplePlugins->purplePlugins();
	        	$this->set('plugins', $plugins);
                
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
		$subscriberAdd     = new SubscriberAddForm();
		$subscriberEdit    = new SubscriberEditForm();
		$subscriberDelete  = new SubscriberDeleteForm();
		$mailchimpSettings = new SubscriberMailchimpSettingsForm();

		$subscribers = $this->Subscribers->find()->order(['id' => 'DESC']);

		$mailchimpApiId  = $this->Settings->find()->where(['name' => 'mailchimpapikey'])->first();
		$mailchimpListId = $this->Settings->find()->where(['name' => 'mailchimplistid'])->first();

		if ($mailchimpApiId->value != '' && $mailchimpListId->value != '') {
			$mailchimp       = new MailChimp($mailchimpApiId->value);
			$mailchimpList   = $mailchimp->get('/lists/'.$mailchimpListId->value.'/members');

			$members = [];
			foreach ($mailchimpList['members'] as $member) {
				$members[] = $member['email_address'];
			}
		}
		else {
			$members = false;
		}

		$data = [
			'subscriberAdd'     => $subscriberAdd,
			'subscriberEdit'    => $subscriberEdit,
			'subscriberDelete'  => $subscriberDelete,
			'mailchimpSettings' => $mailchimpSettings,
			'mailchimpApiId'    => $mailchimpApiId,
			'mailchimpListId'   => $mailchimpListId,
			'mailchimpList'     => $members
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
        if ($this->request->is('ajax') || $this->request->is('post')) {
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
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($subscriberDelete->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
				$subscriber = $this->Subscribers->get($this->request->getData('id'));
				$email      = $subscriber->email;

				$result = $this->Subscribers->delete($subscriber);

                if ($result) {
					// If also delete email in Mailchimp Account
					$mailchimpApiId  = $this->Settings->find()->where(['name' => 'mailchimpapikey'])->first();
					$mailchimpListId = $this->Settings->find()->where(['name' => 'mailchimplistid'])->first();

					$mailchimp = new MailChimp($mailchimpApiId->value);

					$mailchimpList   = $mailchimp->get('/lists/'.$mailchimpListId->value.'/members');

					$members = [];
					foreach ($mailchimpList['members'] as $member) {
						$members[] = $member['email_address'];
					}

					if (in_array($email, $members)) {
						$subscriberHash = $mailchimp->subscriberHash($email);

						$deleteEmailMailchimp = $mailchimp->delete("lists/$mailchimpListId->value/members/$subscriberHash");

						if ($mailchimp->success()) {
							$checkDeletedMailchimp = true;
						}
						else {
							$checkDeletedMailchimp = $mailchimp->getLastError();
						}
					}
					else {
						$checkDeletedMailchimp = false;
					}
					
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a Subscriber',
                        'detail'   => ' delete '.$email.' from subscriber.',
                        'admin_id' => $sessionID
                    ];

                    $saveActivity   = $this->Histories->saveActivity($options);

                    if ($saveActivity == true) {
                        $json = json_encode(['status' => 'ok', 'activity' => true, 'mailchimp' => $checkDeletedMailchimp]);
                    }
                    else {
                        $json = json_encode(['status' => 'ok', 'activity' => false, 'mailchimp' => $checkDeletedMailchimp]);
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

        if ($this->request->is('ajax') || $this->request->is('post')) {
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
	public function ajaxUpdateMailchimpSettings() 
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$mailchimpSettings = new SubscriberMailchimpSettingsForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($mailchimpSettings->execute($this->request->getData())) {
				$apiKey = trim($this->request->getData('key'));
				$listId = trim($this->request->getData('list'));

				$mailchimpApiId  = $this->Settings->find()->where(['name' => 'mailchimpapikey'])->first();
				$mailchimpListId = $this->Settings->find()->where(['name' => 'mailchimplistid'])->first();

				$mailchimpApi = $this->Settings->get($mailchimpApiId->id);
				$mailchimpApi->value = $apiKey;

				$mailchimpList = $this->Settings->get($mailchimpListId->id);
				$mailchimpList->value = $listId;

				if ($this->Settings->save($mailchimpApi) && $this->Settings->save($mailchimpList)) {
					/**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Addition of Mailchimp API Key and Audience ID',
                        'detail'   => ' add Mailchimp API key and Audience ID.',
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
            	$errors = $mailchimpSettings->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxExportToMailchimp()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {

			$mailchimpApiId  = $this->Settings->find()->where(['name' => 'mailchimpapikey'])->first();
			$mailchimpListId = $this->Settings->find()->where(['name' => 'mailchimplistid'])->first();

			if ($mailchimpApiId->value == '' || $mailchimpListId->value == '') {
                $json = json_encode(['status' => 'error', 'error' => 'Please provide Mailchimp API Key and Audience ID.']);
			}
			else {
				$subscribers = $this->Subscribers->find()->order(['id' => 'DESC']);

				if ($subscribers->count() > 0) {
					$mailchimp = new MailChimp($mailchimpApiId->value);
					$batch 	   = $mailchimp->new_batch();

					$mailchimpList   = $mailchimp->get('/lists/'.$mailchimpListId->value.'/members');

					$members = [];
					foreach ($mailchimpList['members'] as $member) {
						$members[] = $member['email_address'];
					}

					$i = 1;
					foreach ($subscribers as $subscriber) {
						if (!in_array($subscriber->email, $members)) {
							$batch->post("op" . $i, "lists/$mailchimpListId->value/members", [
								'email_address' => $subscriber->email,
								'status'        => 'subscribed'
							]);
						}

						$i++;
					}

					$result = $batch->execute();

					if ($result) {
						$json = json_encode(['status' => 'ok']);
					}
					else {
						$json = json_encode(['status' => 'error', 'error' => "Can't add subscribers to Mailchimp. Please try again."]);
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => 'Subscriber data is empty.']);
				}
			}

			$this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}