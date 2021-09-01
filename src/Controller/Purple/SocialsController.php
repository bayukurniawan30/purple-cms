<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\SocialAddForm;
use App\Form\Purple\SocialEditForm;
use App\Form\Purple\SocialDeleteForm;
use App\Form\Purple\SocialSharingButtonsForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectPlugins;
use Particle\Filter\Filter;

class SocialsController extends AppController
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
                    'title'             => 'Account and Sharing | Purple CMS',
                    'pageTitle'         => 'Account and Sharing',
                    'pageTitleIcon'     => 'mdi-share-variant',
                    'pageBreadcrumb'    => 'Socials::Account and Sharing',
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
                ['controller' => 'Authenticate', 'action' => 'login', '?' => ['ref' => Router::url($this->getRequest()->getRequestTarget(), true)]]
            );
        }
	}
	public function index()
    {
        $socialAdd     = new SocialAddForm();
        $socialEdit    = new SocialEditForm();
        $socialDelete  = new SocialDeleteForm();
        $socialButtons = new SocialSharingButtonsForm();

        $querySocialButtonsShare    = $this->Settings->fetch('socialshare');
        $querySocialButtonsTheme    = $this->Settings->fetch('socialtheme');
        $querySocialButtonsFontSize = $this->Settings->fetch('socialfontsize');
        $querySocialButtonsLabel    = $this->Settings->fetch('sociallabel');
        $querySocialButtonsCount    = $this->Settings->fetch('socialcount');

        $socials = $this->Socials->find('all')->order(['ordering' => 'ASC']);
        $this->set(compact('socials'));
        
        $instagramAccount = false;
        $instagramUrl     = false;
        $igMedias         = false;
        if ($socials->count() > 0) {
            foreach ($socials as $social) {
                if ($social->name == 'instagram' && $social->link != '') {
                    $instagramAccount = true;
                    $instagramUrl     = $social->link;
                }
            }

            if ($instagramAccount == true) {
                $instagramApi     = new \InstagramScraper\Instagram();
                $instagramLink    = parse_url($instagramUrl);
                $instagramAccount = str_replace('/', '', $instagramLink['path']);
                $igMedias         = $instagramApi->getMedias($instagramAccount, 12);
            }
        }
        
        $data = [
            'socialAdd'             => $socialAdd,
            'socialEdit'            => $socialEdit,
            'socialDelete'          => $socialDelete,
            'socialButtons'         => $socialButtons,
            'socialButtonsShare'    => str_replace('\\', '', $querySocialButtonsShare->value),
            'socialButtonsTheme'    => $querySocialButtonsTheme->value,
            'socialButtonsFontSize' => $querySocialButtonsFontSize->value,
            'socialButtonsLabel'    => $querySocialButtonsLabel->value,
            'socialButtonsCount'    => $querySocialButtonsCount->value,
            'igMedias'              => $igMedias 
		];

		$this->set($data);
	}
	public function ajaxAdd() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $socialAdd = new SocialAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($socialAdd->execute($this->request->getData())) {
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['name', 'link'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
                $requestArray = json_decode(json_encode($filterResult), TRUE);
                
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
                $social = $this->Socials->newEntity();
                $social = $this->Socials->patchEntity($social, $requestArray);
			
                if ($this->Socials->save($social)) {
                	$recordId = $social->id;

					$social = $this->Socials->get($recordId);
	                $social->ordering = $recordId;
                    $result = $this->Socials->save($social);
                    
                    // Tell system for new event
                    $event = new Event('Model.Social.afterSave', $this, ['social' => $requestData->name, 'admin' => $admin, 'save' => 'new']);
                    $this->getEventManager()->dispatch($event);

                    $json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

                    $this->Flash->set($requestData->name . ' has been added.', [
                        'element' => 'Flash/Purple/success'
                    ]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
                }
            }
            else {
            	$errors = $socialAdd->getErrors();
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

        $socialEdit = new SocialEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($socialEdit->execute($this->request->getData())) {
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['name', 'link'])->trim()->stripHtml();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
                $requestArray = json_decode(json_encode($filterResult), TRUE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

            	$social = $this->Socials->get($requestData->id);
				$this->Socials->patchEntity($social, $requestArray);

                if ($this->Socials->save($social)) {
                    // Tell system for new event
                    $event = new Event('Model.Social.afterSave', $this, ['social' => $requestData->name, 'admin' => $admin, 'save' => 'update']);
                    $this->getEventManager()->dispatch($event);

                    $json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

                    $this->Flash->set($requestData->name . ' has been updated.', [
                        'element' => 'Flash/Purple/success'
                    ]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
                }
            }
            else {
            	$errors = $socialEdit->getErrors();
                $json = json_encode(['status' => 'error', 'error' => "Make sure you don't enter the same username or email and please fill all field."]);
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

        $socialDelete = new SocialDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($socialDelete->execute($this->request->getData())) {
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
                $social = $this->Socials->get($requestData->id);
				$name   = $social->name;

				$result = $this->Socials->delete($social);

                if ($result) {
                    // Tell system for new event
                    $event = new Event('Model.Social.afterDelete', $this, ['social' => $name, 'admin' => $admin]);
                    $this->getEventManager()->dispatch($event);

                    $json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

                    $this->Flash->set($name . ' has been deleted.', [
                        'element' => 'Flash/Purple/success'
                    ]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $socialDelete->getErrors();
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
				$social = $this->Socials->get($newOrder);
				$social->ordering = $count;
                if ($this->Socials->save($social)) {
					array_push($resultArray, 1);
				}
				else {
					array_push($resultArray, 0);
                }
                
				$count++;
            }
            
			if (!in_array(0, $resultArray)) {
                // Tell system for new event
                $event = new Event('Model.Social.afterReorder', $this, ['admin' => $admin]);
                $this->getEventManager()->dispatch($event);

                $json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
            }
			else {
				$json = json_encode(['status' => 'error',]);
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
    public function ajaxSharingButtons()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $socialButtons = new SocialSharingButtonsForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($socialButtons->execute($this->request->getData())) {
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['theme', 'label', 'count'])->trim()->stripHtml();
				$filter->values(['fontsize'])->int();
				$filterResult = $filter->filter($this->request->getData());
                $requestData  = json_decode(json_encode($filterResult), FALSE);
                
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                $admin     = $this->Admins->get($sessionID);

                $theme    = $requestData->theme;
                $fontSize = $requestData->fontsize;
                $label    = $requestData->label;
                $count    = $requestData->count;

                $querySocialButtonsTheme    = $this->Settings->fetch('socialtheme');
                $querySocialButtonsFontSize = $this->Settings->fetch('socialfontsize');
                $querySocialButtonsLabel    = $this->Settings->fetch('sociallabel');
                $querySocialButtonsCount    = $this->Settings->fetch('socialcount');

                $settingSocialButtonsTheme  = $this->Settings->get($querySocialButtonsTheme->id);
                $settingSocialButtonsTheme->value = $theme;

                $settingSocialButtonsFontSize  = $this->Settings->get($querySocialButtonsFontSize->id);
                $settingSocialButtonsFontSize->value = $fontSize;

                $settingSocialButtonsLabel  = $this->Settings->get($querySocialButtonsLabel->id);
                $settingSocialButtonsLabel->value = $label;

                $settingSocialButtonsCount  = $this->Settings->get($querySocialButtonsCount->id);
                $settingSocialButtonsCount->value = $count;

                if ($this->Settings->save($settingSocialButtonsTheme) && $this->Settings->save($settingSocialButtonsFontSize) && $this->Settings->save($settingSocialButtonsLabel) && $this->Settings->save($settingSocialButtonsCount)) {
                    // Tell system for new event
                    $event = new Event('Model.Social.afterUpdateSharingButtons', $this, ['admin' => $admin, 'data' => ['theme' => $theme, 'fontSize' => $fontSize, 'label' => $label, 'count' => $count]]);
                    $this->getEventManager()->dispatch($event);

                    $json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

                    $this->Flash->set('Sharing buttons has been updated.', [
                        'element' => 'Flash/Purple/success'
                    ]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't edit content sharing buttons. Please try again."]);
                }
            }
            else {
                $errors = $socialButtons->getErrors();
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