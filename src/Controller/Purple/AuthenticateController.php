<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use App\Form\Purple\AdminLoginForm;
use App\Form\Purple\ForgotPasswordForm;
use App\Form\Purple\NewPasswordForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use Carbon\Carbon;

class AuthenticateController extends AppController
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

		$this->loadModel('Admins');
		$this->loadModel('Settings');
		$this->loadModel('Histories');

		if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
		  	$cakeDebug = 'on';
		} 
		else {
		  	$cakeDebug = 'off';
		}

		$data = [
			'sessionHost' => $this->request->getEnv('HTTP_HOST'),
			'cakeDebug'	  => $cakeDebug
		];

    	$this->set($data);	
	}
	public function login() 
	{
        if ($this->request->is('get')) {
			$adminLogin     = new AdminLoginForm();
			$forgotPassword = new ForgotPasswordForm();

            $queryDefaultBackgroundLogin = $this->Settings->find()->where(['name' => 'defaultbackgroundlogin'])->first();
            $queryBackgroundLogin        = $this->Settings->find()->where(['name' => 'backgroundlogin'])->first();
            
            $data = [
				'adminLogin'            => $adminLogin,
				'forgotPassword'        => $forgotPassword,
				'settingDefaultBgLogin' => $queryDefaultBackgroundLogin,
                'settingBgLogin'        => $queryBackgroundLogin,
            ];
	    	$this->viewBuilder()->setLayout('login');
        	$this->set($data);
        }
	}
	public function logout() 
	{
		$session = $this->getRequest()->getSession();
        if ($this->request->getEnv('HTTP_HOST') == $session->read('Admin.host')) {
            $session->delete('Admin.host');
            $session->delete('Admin.id');
            $session->delete('Admin.password');

            $sessionID = $session->read('Admin.id');

            /**
             * Save user activity to histories table
             * array $options => title, detail, admin_id
             */
            
            $options = [
                'title'    => 'User Logout',
                'detail'   => ' logout from Purple.',
                'admin_id' => $sessionID
            ];

            $saveActivity   = $this->Histories->saveActivity($options);
        }
        return $this->setAction('login');
	}
	public function loginApi()
	{
		$user = $this->Auth->identify();
		if ($user && $user['level'] == '1') {
			$this->Auth->setUser($user);
		} 
		else {
	        throw new UnauthorizedException(__('Unauthorized'));
        }
	}
	public function ajaxLogin() 
	{
        $this->viewBuilder()->enableAutoLayout(false);

		$adminLogin   = new AdminLoginForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($adminLogin->execute($this->request->getData())) {
				$purpleGlobal    = new PurpleProjectGlobal();
				$operatingSystem = $purpleGlobal->detectOS();
				$deviceType      = $purpleGlobal->detectDevice();
				$clientBrowser   = $purpleGlobal->detectBrowser();

            	$purpleSettings = new PurpleProjectSettings();
			    $timezone       = $purpleSettings->timezone();

				$username = trim($this->request->getData('username'));
				$password = trim($this->request->getData('password'));

				$detectIP      = $this->request->clientIp();
				$detectOS      = $purpleGlobal->detectOS();
				$detectBrowser = $purpleGlobal->detectBrowser();
				$detectDevice  = $purpleGlobal->detectDevice();

				$admin       = $this->Admins->find()->where(['username' => $username])->first();
				
				$getPassword   = $admin->password;
				$checkPassword = (new DefaultPasswordHasher())->check($password, $getPassword);

				if ($checkPassword) {
					$session = $this->getRequest()->getSession();
					$session->write([
						'Admin.host'     => $this->request->getEnv('HTTP_HOST'),
					  	'Admin.id'       => $admin->id,
					  	'Admin.password' => $admin->password,
					]);

					$admin->last_login    = Carbon::now($timezone);;
					$admin->login_device  = $deviceType;
					$admin->login_os      = $operatingSystem;
					$admin->login_browser = $clientBrowser;
					if ($this->Admins->save($admin)) {
						/**
		                 * Save user activity to histories table
		                 * array $options => title, detail, admin_id
		                 */
		                
		                $sessionID = $session->read('Admin.id');

		                $options = [
		                    'title'    => 'User Login',
		                    'detail'   => ' login to Purple.',
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
		            	$json = json_encode(['status' => 'error', 'error' => "Cannot login now. Please try again."]);
		            }
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Invalid username or password.", 'pass' => $password]);
				}
			}
			else {
            	$errors = $adminLogin->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxForgotPassword() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

		$forgotPassword = new ForgotPasswordForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($forgotPassword->execute($this->request->getData())) {
	            $checkEmail = $this->Admins->find()->where(['email' => $this->request->getData('email')])->limit(1);
	            if ($checkEmail->count() > 0) {
					$id    = $checkEmail->first()->id;
					$admin = $checkEmail->first();
					$admin->token = md5(trim($this->request->getData('email')));
					if ($this->Admins->save($admin)) {
						// Send Email to User to Notify author
						$key       = $this->Settings->settingsPublicApiKey();
						$resetLink = $this->request->getData('ds').'/reset-password/token/'.md5(trim($this->request->getData('email')));
                        $userData      = array(
                            'sitename'    => $this->Settings->settingsSiteName(),
                            'username'    => $admin->username,
                            'email'       => $admin->email,
                            'displayName' => $admin->display_name,
                            'level'       => $admin->level
                        );
                        $senderData   = array(
                            'name'   => $admin->display_name,
                            'domain' => $this->request->domain()
                        );

						$purpleApi  = new PurpleProjectApi();
						$notifyUser = $purpleApi->sendEmailForgotPassword($key, $resetLink, json_encode($userData), json_encode($senderData));

                        if ($notifyUser == true) {
                            $emailNotification = true;
                        }
                        else {
                            $emailNotification = false;
                        }

                        $json = json_encode(['status' => 'ok', 'email' => [$admin->email => $emailNotification], 'content' => '<div class="alert alert-success" role="alert" style="margin-top: 15px">Your password has been reseted. Please check your inbox or spam folder in your email.</div>']);
					}
		            else {
		            	$json = json_encode(['status' => 'error', 'error' => "Cannot reset your password. Please try again."]);
		            }
	            }
	            else {
	            	$json = json_encode(['status' => 'error', 'error' => "User not found. You are not part of Purple CMS."]);
	            }
	        }
	        else {
	        	$errors = $forgotPassword->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
	        }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function resetPassword()
    {
    	$this->viewBuilder()->setLayout('password');
    	$token = $this->request->getParam('token');
    	if (!empty($token)) {
            $checkUserl = $this->Admins->find()->where(['token' => $token])->limit(1);
            if ($checkUserl->count() > 0) {
				$user  = $checkUserl->first();
				$id    = $user->id;
				$email = $user->email;
				if (md5($email) == $token) {
					$newPassword = new NewPasswordForm();

					$data = [
						'id'          => $id,
						'newPassword' => $newPassword,
						'token'		  => $this->request->getData('token')
					];

					$this->set($data);
				}
				else {
			        throw new NotFoundException(__('Page not found'));
			    }
            }
    	}
		else {
	        throw new NotFoundException(__('Page not found'));
	    }

    }
    public function ajaxResetPassword() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

		$newPassword = new NewPasswordForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($newPassword->execute($this->request->getData())) {

	            $checkUser = $this->Admins->find()->where(['id' => $this->request->getData('id'), 'token' => $this->request->getData('token')])->limit(1);
	            if ($checkUser->count() > 0) {
					$admin         = $checkUser->first();
					$oldPassword   = $admin->password;
					$checkPassword = (new DefaultPasswordHasher())->check($this->request->getData('password'), $oldPassword);

					if ($checkPassword) {
		            	$json = json_encode(['status' => 'error', 'error' => "Do not use the old password. Please use another password."]);
					}
					else {
						$admin->password = $this->request->getData('password');
						$admin->token    = '';
						if ($this->Admins->save($admin)) {
							// Send Email to User to Notify author
							$key       = $this->Settings->settingsPublicApiKey();
							$dashboardLink = $this->request->getData('ds');
	                        $userData      = array(
	                            'sitename'    => $this->Settings->settingsSiteName(),
	                            'username'    => $admin->username,
	                            'password'    => $this->request->getData('password'),
	                            'email'       => $admin->email,
	                            'displayName' => $admin->display_name,
	                            'level'       => $admin->level
	                        );
	                        $senderData   = array(
	                            'name'   => $admin->display_name,
	                            'domain' => $this->request->domain()
	                        );

							$purpleApi  = new PurpleProjectApi();
							$notifyUser = $purpleApi->sendEmailNewPassword($key, $dashboardLink, json_encode($userData), json_encode($senderData));

	                        if ($notifyUser == true) {
	                            $emailNotification = true;
	                        }
	                        else {
	                            $emailNotification = false;
	                        }

	                        $json = json_encode(['status' => 'ok', 'email' => [$admin->email => $emailNotification], 'content' => '<div class="alert alert-success" role="alert">Your password has been reseted. Please check your inbox or spam folder in your email.</div>']);
	                    }
	                    else {
			            	$json = json_encode(['status' => 'error', 'error' => "Cannot reset your password. Please try again."]);
			            }
					}
	            }
	            else {
	            	$json = json_encode(['status' => 'error', 'error' => "User not found. You are not part of Purple CMS."]);
	            }
            }
            else {
	        	$errors = $newPassword->errors();
                $json  = json_encode(['status' => 'error', 'error' => $errors]);
	        }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}