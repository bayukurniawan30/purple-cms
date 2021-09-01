<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\SubscriberAddForm;
use App\Form\Purple\SubscriberEditForm;
use App\Form\Purple\SubscriberDeleteForm;
use App\Form\Purple\SubscriberMailchimpSettingsForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectApi;
use App\Purple\PurpleProjectPlugins;
use \DrewM\MailChimp\MailChimp;
use \DrewM\MailChimp\Batch;
use Particle\Filter\Filter;

class SubscribersController extends AppController
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

		$queryAdmin      = $this->Admins->signedInUser($sessionID, $sessionPassword);
		$queryFavicon    = $this->Settings->fetch('favicon');
		$queryDateFormat = $this->Settings->fetch('dateformat');
		$queryTimeFormat = $this->Settings->fetch('timeformat');

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
				['controller' => 'Authenticate', 'action' => 'login', '?' => ['ref' => Router::url($this->getRequest()->getRequestTarget(), true)]]
			);
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
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['email'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);
				
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($requestData->email);

                if ($verifyEmail == true) {
                	$findDuplicate = $this->Subscribers->find()->where(['email' => $requestData->email]);

                    if ($findDuplicate->count() >= 1) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another email."]);
                    }
                    else {
                    	$subscriber = $this->Subscribers->newEntity();
                        $subscriber = $this->Subscribers->patchEntity($subscriber, $requestArray);
                        $subscriber->status = 'active';
    				
    	                if ($this->Subscribers->save($subscriber)) {
    	                	$recordId = $subscriber->id;

							$subscriber = $this->Subscribers->get($recordId);

							// Tell system for new event
							$event = new Event('Model.Subscriber.afterSave', $this, ['subscriber' => $subscriber->email, 'admin' => $admin, 'save' => 'new']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'id' => $recordId, 'activity' => $event->getResult()]);

							$this->Flash->set($subscriber->email . ' has been added.', [
								'element' => 'Flash/Purple/success'
							]);
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
            	$errors = $subscriberAdd->getErrors();
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

		$subscriberEdit = new SubscriberEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($subscriberEdit->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['email'])->trim()->stripHtml();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($requestData->email);

                if ($verifyEmail == true) {
	                $findDuplicate = $this->Subscribers->find()->where(['email' => $requestData->email]);

	                if ($findDuplicate->count() >= 1) {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another email."]);
	                }
	                else {
		                $subscriber = $this->Subscribers->get($requestData->id);
						$this->Subscribers->patchEntity($subscriber, $requestArray);

						if ($this->Subscribers->save($subscriber)) {
							$recordId = $subscriber->id;

							$subscriber = $this->Subscribers->get($recordId);

							// Tell system for new event
							$event = new Event('Model.Subscriber.afterSave', $this, ['subscriber' => $subscriber->email, 'admin' => $admin, 'save' => 'update']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

							$this->Flash->set($subscriber->email . ' has been updated.', [
								'element' => 'Flash/Purple/success'
							]);
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
				$errors = $subscriberEdit->getErrors();
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

		$subscriberDelete = new SubscriberDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($subscriberDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$subscriber = $this->Subscribers->get($requestData->id);
				$email      = $subscriber->email;

				$result = $this->Subscribers->delete($subscriber);

                if ($result) {
					// If also delete email in Mailchimp Account
					$mailchimpApiId  = $this->Settings->fetch('mailchimpapikey');
					$mailchimpListId = $this->Settings->fetch('mailchimplistid');

					if ($mailchimpApiId->value != '' && $mailchimpListId->value != '') {
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
					}
					else {
						$checkDeletedMailchimp = false;
					}

					// Tell system for new event
					$event = new Event('Model.Subscriber.afterDelete', $this, ['subscriber' => $email, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult(), 'mailchimp' => $checkDeletedMailchimp]);

					$this->Flash->set($email . ' has been deleted.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $subscriberDelete->getErrors();
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
			
            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
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
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['key', 'list'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

				$apiKey = $requestData->key;
				$listId = $requestData->list;

				$mailchimpApiId  = $this->Settings->fetch('mailchimpapikey');
				$mailchimpListId = $this->Settings->fetch('mailchimplistid');

				$mailchimpApi = $this->Settings->get($mailchimpApiId->id);
				$mailchimpApi->value = $apiKey;

				$mailchimpList = $this->Settings->get($mailchimpListId->id);
				$mailchimpList->value = $listId;

				if ($this->Settings->save($mailchimpApi) && $this->Settings->save($mailchimpList)) {
					// Tell system for new event
					$event = new Event('Model.Subscriber.afterUpdateMailchimpSettings', $this, ['admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set('Mailchimp settings has been updated.', [
						'element' => 'Flash/Purple/success'
					]);
				}
			}
			else {
            	$errors = $mailchimpSettings->getErrors();
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
	public function ajaxExportToMailchimp()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$mailchimpApiId  = $this->Settings->fetch('mailchimpapikey');
			$mailchimpListId = $this->Settings->fetch('mailchimplistid');
			
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

						$this->Flash->set('Data has been exported to Mailchimp.', [
							'element' => 'Flash/Purple/success'
						]);
					}
					else {
						$json = json_encode(['status' => 'error', 'error' => "Can't add subscribers to Mailchimp. Please try again."]);
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => 'Subscriber data is empty.']);
				}
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