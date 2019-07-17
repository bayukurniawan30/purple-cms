<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\SocialAddForm;
use App\Form\Purple\SocialEditForm;
use App\Form\Purple\SocialDeleteForm;
use App\Form\Purple\SocialSharingButtonsForm;
use App\Form\Purple\SearchForm;
use Cake\I18n\Time;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;
use Carbon\Carbon;

class SocialsController extends AppController
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
		            ['controller' => 'Authenticate', 'action' => 'login']
		        );
			}
	    }
	}
	public function index()
    {
        $socialAdd     = new SocialAddForm();
        $socialEdit    = new SocialEditForm();
        $socialDelete  = new SocialDeleteForm();
        $socialButtons = new SocialSharingButtonsForm();

        $querySocialButtonsShare    = $this->Settings->find()->where(['name' => 'socialshare'])->first();
        $querySocialButtonsTheme    = $this->Settings->find()->where(['name' => 'socialtheme'])->first();
        $querySocialButtonsFontSize = $this->Settings->find()->where(['name' => 'socialfontsize'])->first();
        $querySocialButtonsLabel    = $this->Settings->find()->where(['name' => 'sociallabel'])->first();
        $querySocialButtonsCount    = $this->Settings->find()->where(['name' => 'socialcount'])->first();

        $data = [
            'socialAdd'             => $socialAdd,
            'socialEdit'            => $socialEdit,
            'socialDelete'          => $socialDelete,
            'socialButtons'         => $socialButtons,
            'socialButtonsShare'    => str_replace('\\', '', $querySocialButtonsShare->value),
            'socialButtonsTheme'    => $querySocialButtonsTheme->value,
            'socialButtonsFontSize' => $querySocialButtonsFontSize->value,
            'socialButtonsLabel'    => $querySocialButtonsLabel->value,
            'socialButtonsCount'    => $querySocialButtonsCount->value
		];

        $socials = $this->Socials->find('all')->order(['ordering' => 'ASC']);
    	$this->set(compact('socials'));

		$this->set($data);
	}
	public function ajaxAdd() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $socialAdd = new SocialAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($socialAdd->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
                $social = $this->Socials->newEntity();
                $social = $this->Socials->patchEntity($social, $this->request->getData());
			
                if ($this->Socials->save($social)) {
                	$record_id = $social->id;

					$social = $this->Socials->get($record_id);
	                $social->ordering = $record_id;
					$result = $this->Socials->save($social);

                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Addition of a New Social Media',
                        'detail'   => ' add '.$this->request->getData('name').' as a new social media.',
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
            	$errors = $socialAdd->errors();
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

        $socialEdit = new SocialEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($socialEdit->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

            	$social = $this->Socials->get($this->request->getData('id'));
				$this->Socials->patchEntity($social, $this->request->getData());

                if ($this->Socials->save($social)) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Data Change of a Social Media',
                        'detail'   => ' change '.$this->request->getData('name').' url.',
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
            	$errors = $socialEdit->errors();
                $json = json_encode(['status' => 'error', 'error' => "Make sure you don't enter the same username or email and please fill all field."]);
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

        $socialDelete = new SocialDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($socialDelete->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
                $social = $this->Socials->get($this->request->getData('id'));
				$name   = $social->name;

				$result = $this->Socials->delete($social);

                if ($result) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a Social Media',
                        'detail'   => ' delete '.$name.' from social media.',
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
            	$errors = $socialDelete->errors();
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
				$social = $this->Socials->get($newOrder);
				$social->ordering = $count;
				$result = $this->Socials->save($social);
				$count++;
			}

			$json = json_encode(['status' => 'ok']);
			$this->set(['json' => $json]);
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
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $theme    = $this->request->getData('theme');
                $fontSize = $this->request->getData('fontsize');
                $label    = $this->request->getData('label');
                $count    = $this->request->getData('count');

                $querySocialButtonsTheme    = $this->Settings->find()->where(['name' => 'socialtheme'])->first();
                $querySocialButtonsFontSize = $this->Settings->find()->where(['name' => 'socialfontsize'])->first();
                $querySocialButtonsLabel    = $this->Settings->find()->where(['name' => 'sociallabel'])->first();
                $querySocialButtonsCount    = $this->Settings->find()->where(['name' => 'socialcount'])->first();

                $settingSocialButtonsTheme  = $this->Settings->get($querySocialButtonsTheme->id);
                $settingSocialButtonsTheme->value = $theme;

                $settingSocialButtonsFontSize  = $this->Settings->get($querySocialButtonsFontSize->id);
                $settingSocialButtonsFontSize->value = $fontSize;

                $settingSocialButtonsLabel  = $this->Settings->get($querySocialButtonsLabel->id);
                $settingSocialButtonsLabel->value = $label;

                $settingSocialButtonsCount  = $this->Settings->get($querySocialButtonsCount->id);
                $settingSocialButtonsCount->value = $count;

                if ($this->Settings->save($settingSocialButtonsTheme) && $this->Settings->save($settingSocialButtonsFontSize) && $this->Settings->save($settingSocialButtonsLabel) && $this->Settings->save($settingSocialButtonsCount)) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Setting Change for Content Sharing Buttons',
                        'detail'   => ' change content sharing buttons theme: '.$theme.', font size: '.$fontSize.', label: '.$label.', count: '.$count.'.',
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
                    $json = json_encode(['status' => 'error', 'error' => "Can't edit content sharing buttons. Please try again."]);
                }
            }
            else {
                $errors = $socialButtons->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
}