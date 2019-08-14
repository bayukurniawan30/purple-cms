<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\AdminAddForm;
use App\Form\Purple\AdminEditForm;
use App\Form\Purple\AdminEditPasswordForm;
use App\Form\Purple\AdminDeleteForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use App\Purple\PurpleProjectPlugins;
use Carbon\Carbon;
use Bulletproof;
use \Gumlet\ImageResize;

class AdminsController extends AppController
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
					'title'              => 'Users | Purple CMS',
					'pageTitle'          => 'Users',
					'pageTitleIcon'      => 'mdi-account-multiple',
					'pageBreadcrumb'     => 'Users',
					'appearanceFavicon'  => $queryFavicon,
					'settingsDateFormat' => $queryDateFormat->value,
					'settingsTimeFormat' => $queryTimeFormat->value
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
        $adminDelete = new AdminDeleteForm();

		$data = [
            'pageTitle'      => 'Users',
            'pageBreadcrumb' => 'Users',
            'adminDelete'    => $adminDelete
		];

        $users = $this->Admins->find()->where(['username <> ' => 'creatifycore'])->order(['id' => 'ASC']);
    	$this->set(compact('users'));
		$this->set($data);
	}
	public function add()
	{
        $adminAdd = new AdminAddForm();

		$data = [
            'pageTitle'      => 'Users',
            'pageBreadcrumb' => 'Users::Add',
            'adminAdd'       => $adminAdd
		];

		$this->set($data);
	}
	public function edit($id)
	{
        $adminEdit = new AdminEditForm();

        $query = $this->Admins->find()->where(['id' => $id]);

        if ($query->count() == 1) {
        	$adminData = $query->first();

        	$data = [
                'pageTitle'      => 'Users',
                'pageBreadcrumb' => 'Users::Edit',
                'adminEdit'      => $adminEdit,
                'adminData'      => $adminData
			];

			$this->set($data);
        }
        else {
        	$this->setAction('index');
        }
	}
    public function changePassword($id)
    {
        $adminEditPassword = new AdminEditPasswordForm();

        $query = $this->Admins->find()->where(['id' => $id]);

        if ($query->count() == 1) {
            $adminData = $query->first();

            $data = [
                'pageTitle'         => 'Users',
                'pageBreadcrumb'    => 'Users::Change Password',
                'adminEditPassword' => $adminEditPassword,
                'adminData'         => $adminData
            ];

            $this->set($data);
        }
        else {
            $this->setAction('index');
        }
    }
	public function ajaxSaveProfilePicture() 
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        if ($this->request->is('ajax') || $this->request->is('post')) {
            $base64 = $this->request->getData('base64');

            if (strpos($base64, 'png') !== false) {
                $sanitizeString = str_replace('data:image/png;base64,', '', $base64);
            }
            elseif (strpos($base64, 'jpeg') !== false) {
                $sanitizeString = str_replace('data:image/jpeg;base64,', '', $base64);
            }
            
            $fullSizeImage = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
            $image = ImageResize::createFromString(base64_decode($sanitizeString));
            $name  = md5(time());

            if ($image->save($fullSizeImage. $name .'.png', IMAGETYPE_PNG)) {
                $json = json_encode(['status' => 'ok', 'image' => $name.'.png']);
            }
            else {
                $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
            }
            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxAdd() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $adminAdd = new AdminAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($adminAdd->execute($this->request->getData())) {
                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                if ($verifyEmail == true) {
                    $session    = $this->getRequest()->getSession();
                    $sessionID  = $session->read('Admin.id');
                    $queryAdmin = $this->Admins->find()->where(['id' => $sessionID])->limit(1);

                    $findDuplicate = $this->Admins->find()->where([
                    	'OR' => [['username' => $this->request->getData('username')], ['email' => $this->request->getData('email')]]
                    ]);

                    if ($findDuplicate->count() >= 1) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another username or email."]);
                    }
                    else {
                        if(preg_match('/[A-Z]/', $this->request->getData('username'))){
                            $json = json_encode(['status' => 'error', 'error' => "Please use lowercase letter for username."]);
                        }
                        else {
                            $admin = $this->Admins->newEntity();
                            $admin = $this->Admins->patchEntity($admin, $this->request->getData());
        				
        	                if ($this->Admins->save($admin)) {
                                $record_id = $admin->id;
                                $admin     = $this->Admins->get($record_id);

                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */
                                
                                $options = [
                                    'title'    => 'Addition of a New User',
                                    'detail'   => ' add '.$this->request->getData('username').'('.$this->request->getData('email').') as a new user.',
                                    'admin_id' => $sessionID
                                ];

                                $saveActivity   = $this->Histories->saveActivity($options);

                                // Send Email to User to Notify author
                                $key    = $this->Settings->settingsPublicApiKey();
                                $dashboardLink = $this->request->getData('ds').'/edit/'.$record_id;
                                $userData      = array(
                                    'sitename'    => $this->Settings->settingsSiteName(),
                                    'username'    => $admin->username,
                                    'password'    => $this->request->getData('password'),
                                    'email'       => $admin->email,
                                    'displayName' => $admin->display_name,
                                    'level'       => $admin->level
                                );
                                $senderData   = array(
                                    'name'   => $queryAdmin->first()->display_name,
                                    'domain' => $this->request->domain()
                                );
                                $notifyUser = $purpleApi->sendEmailNewUser($key, $dashboardLink, json_encode($userData), json_encode($senderData));

                                if ($notifyUser == true) {
                                    $emailNotification = true;
                                }
                                else {
                                    $emailNotification = false;
                                }

                                if ($saveActivity == true) {
                                    $json = json_encode(['status' => 'ok', 'activity' => true, 'email' => [$admin->email => $emailNotification]]);
                                }
                                else {
                                    $json = json_encode(['status' => 'ok', 'activity' => false, 'email' => [$admin->email => $emailNotification]]);
                                }
        	                }
        	                else {
        	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
        	                }
                        }
    				}
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
                }
            }
            else {
            	$errors = $adminAdd->errors();
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

        $adminEdit = new AdminEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($adminEdit->execute($this->request->getData())) {
                $admin = $this->Admins->get($this->request->getData('id'));
                if ($this->request->getData('email') == $admin->email) {
                    $verifyEmail = true;
                }
                else {
                    $purpleApi = new PurpleProjectApi();
                    $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));
                }

                if ($verifyEmail == true) {
                    $session   = $this->getRequest()->getSession();
                    $sessionID = $session->read('Admin.id');

                    $findDuplicate = $this->Admins->find()->where([
                    	'id <> ' => $this->request->getData('id'),
                    	'OR' => [['username' => $this->request->getData('username')], ['email' => $this->request->getData('email')]]
                    ]);

                    if ($findDuplicate->count() >= 1) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another username or email."]);
                    }
                    else {
                        if(preg_match('/[A-Z]/', $this->request->getData('username'))){
                            $json = json_encode(['status' => 'error', 'error' => "Please use lowercase letter for username."]);
                        }
                        else {
        					$this->Admins->patchEntity($admin, $this->request->getData());

                            if ($this->Admins->save($admin)) {
                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */
                                
                                $options = [
                                    'title'    => 'Data Change of a User',
                                    'detail'   => ' change '.$this->request->getData('username').'('.$this->request->getData('email').') data.',
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
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
                }
            }
            else {
            	$errors = $adminEdit->errors();
                $json = json_encode(['status' => 'error', 'error' => "Make sure you don't enter the same username or email and please fill all field."]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxUpdatePassword() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $adminEditPassword = new AdminEditPasswordForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($adminEditPassword->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $admin        = $this->Admins->get($this->request->getData('id'));
                $this->Admins->patchEntity($admin, $this->request->getData());

                if ($this->Admins->save($admin)) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Password Change of a User',
                        'detail'   => ' change '.$this->request->getData('username').'('.$this->request->getData('email').') password.',
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
                $errors = $adminEditPassword->errors();
                $json = json_encode(['status' => 'error', 'error' => "Make sure your password and repeat password is same and please fill all field."]);
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

        $adminDelete = new AdminDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($adminDelete->execute($this->request->getData())) {
                $session    = $this->getRequest()->getSession();
                $sessionID  = $session->read('Admin.id');
                $queryAdmin = $this->Admins->find()->where(['id' => $sessionID])->limit(1);
                
                $admin = $this->Admins->get($this->request->getData('id'));
				$name  = $admin->display_name;

				$result = $this->Admins->delete($admin);

                if ($result) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a User',
                        'detail'   => ' delete '.$name.' from user.',
                        'admin_id' => $sessionID
                    ];

                    $saveActivity   = $this->Histories->saveActivity($options);

                    // Send Email to User to Notify user
                    $key    = $this->Settings->settingsPublicApiKey();
                    $userData      = array(
                        'sitename'    => $this->Settings->settingsSiteName(),
                        'email'       => $admin->email,
                        'displayName' => $admin->display_name,
                        'level'       => $admin->level
                    );
                    $senderData   = array(
                        'name'   => $queryAdmin->first()->display_name,
                        'domain' => $this->request->domain()
                    );

                    $purpleApi  = new PurpleProjectApi();
                    $notifyUser = $purpleApi->sendEmailDeleteUser($key, json_encode($userData), json_encode($senderData));

                    if ($notifyUser == true) {
                        $emailNotification = true;
                    }
                    else {
                        $emailNotification = false;
                    }

                    if ($saveActivity == true) {
                        $json = json_encode(['status' => 'ok', 'activity' => true, 'email' => $emailNotification]);
                    }
                    else {
                        $json = json_encode(['status' => 'ok', 'activity' => false, 'email' => $emailNotification]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $adminDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}