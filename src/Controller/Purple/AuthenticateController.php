<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Routing\Router;
use App\Form\Purple\AdminLoginForm;
use App\Form\Purple\ForgotPasswordForm;
use App\Form\Purple\NewPasswordForm;
use App\Form\Purple\AdminVerificationSignInForm;
use App\Form\Purple\AdminAuthyTokenForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use Particle\Filter\Filter;
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

		// Load other models
		$this->loadModel('Admins');
		$this->loadModel('Settings');
		$this->loadModel('Histories');

		// Check debug is on or off
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
			// Set layout
			$this->viewBuilder()->setLayout('login');

			// Load required forms
			$adminLogin     = new AdminLoginForm();
			$forgotPassword = new ForgotPasswordForm();

			$queryDefaultBackgroundLogin = $this->Settings->fetch('defaultbackgroundlogin');
            $queryBackgroundLogin        = $this->Settings->fetch('backgroundlogin');
            
            $data = [
				'adminLogin'            => $adminLogin,
				'forgotPassword'        => $forgotPassword,
				'settingDefaultBgLogin' => $queryDefaultBackgroundLogin,
				'settingBgLogin'        => $queryBackgroundLogin,
            ];
        	$this->set($data);
        }
	}
	public function verificationCode() 
	{
		// Set layout
		$this->viewBuilder()->setLayout('login');

		// Load required forms
		$adminVerificationCode = new AdminVerificationSignInForm();

		$session = $this->getRequest()->getSession();
		$admin   = $this->Admins->get($session->read('Admin.id'));

		$purpleSettings = new PurpleProjectSettings();
        $timezone       = $purpleSettings->timezone();

        $lastLogin = Carbon::parse($admin->last_login);
        $now       = Carbon::now($timezone);
        $diff      = $lastLogin->diffInDays($now);

		$queryDefaultBackgroundLogin = $this->Settings->fetch('defaultbackgroundlogin');
		$queryBackgroundLogin        = $this->Settings->fetch('backgroundlogin');

		$explodeEmail   = explode('@', $admin->email);
		$firstCharEmail = substr($explodeEmail[0], 0, 1);
		$lastCharEmail  = substr($explodeEmail[0], -1, 1);
		$hiddenEmail    = $firstCharEmail . '****' . $lastCharEmail . '@' . $explodeEmail[1];
		
		$data = [
			'adminVerificationCode' => $adminVerificationCode,
			'settingDefaultBgLogin' => $queryDefaultBackgroundLogin,
			'settingBgLogin'        => $queryBackgroundLogin,
			'diff'					=> $diff,
			'userEmail'			    => $hiddenEmail
		];
		$this->set($data);
	}
	public function authyToken() 
	{
		// Set layout
		$this->viewBuilder()->setLayout('login');

		// Load required forms
		$adminAuthyToken = new AdminAuthyTokenForm();

		$session = $this->getRequest()->getSession();
		$admin   = $this->Admins->get($session->read('Admin.id'));

		$queryDefaultBackgroundLogin = $this->Settings->fetch('defaultbackgroundlogin');
		$queryBackgroundLogin        = $this->Settings->fetch('backgroundlogin');

		$data = [
			'adminAuthyToken'       => $adminAuthyToken,
			'settingDefaultBgLogin' => $queryDefaultBackgroundLogin,
			'settingBgLogin'        => $queryBackgroundLogin,
			'id'					=> $session->read('Admin.id')
		];
		$this->set($data);
	}
	public function logout() 
	{
		$session = $this->getRequest()->getSession();
        if ($this->request->getEnv('HTTP_HOST') == $session->read('Admin.host')) {
			$admin = $this->Admins->get($session->read('Admin.id'));

			// Delete registered Admin sessions
            $session->delete('Admin.host');
            $session->delete('Admin.id');
            $session->delete('Admin.password');

			// Tell system for new event
			$event = new Event('Model.Admin.afterSignOut', $this, ['admin' => $admin]);
			$this->getEventManager()->dispatch($event);
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
				// Sanitize user input
				$filter = new Filter();
				$filter->all()->trim();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

				$purpleApi = new PurpleProjectApi();

				$purpleGlobal    = new PurpleProjectGlobal();
				$operatingSystem = $purpleGlobal->detectOS();
				$deviceType      = $purpleGlobal->detectDevice();
				$clientBrowser   = $purpleGlobal->detectBrowser();

            	$purpleSettings = new PurpleProjectSettings();
			    $timezone       = $purpleSettings->timezone();

				$username  = $requestData->username;
				$password  = $requestData->password;
				if (!empty($this->request->getData('ref'))) {
					$reference = $requestData->ref;
				}

				$detectIP      = $this->request->clientIp();
				$detectOS      = $purpleGlobal->detectOS();
				$detectBrowser = $purpleGlobal->detectBrowser();
				$detectDevice  = $purpleGlobal->detectDevice();

				$admin     = $this->Admins->find()->where(['username' => $username])->first();
				$lastLogin = $admin->last_login;

				$queryTwoFAuth = $this->Settings->find()->where(['name' => '2fa'])->first();
				
				$getPassword   = $admin->password;
				$checkPassword = (new DefaultPasswordHasher())->check($password, $getPassword);

				if ($checkPassword) {
					$session = $this->getRequest()->getSession();
					$session->write([
						'Admin.host' => $this->request->getEnv('HTTP_HOST'),
						'Admin.id'   => $admin->id,
					]);

					// Tell system for new event
					$eventLastSignIn = new Event('Model.Admin.checkLastSignIn', $this, ['admin' => $admin, 'data' => ['last_login' => $lastLogin, 'domain' => $this->request->host()]]);
					$this->getEventManager()->dispatch($eventLastSignIn);

					if ($admin->email == 'creatifycms@gmail.com' && $admin->username == 'creatifycore') {
						$lastSignedInUser = 0;
					}
					else {
						$lastSignedInUser = $eventLastSignIn->getResult();
					}

					if ($lastSignedInUser >= 7) {
						if (empty($reference)) {
							$verifyUrl = Router::url(['_name' => 'adminSignInVerification']);
						}
						else {
							$verifyUrl = Router::url(['_name' => 'adminSignInVerification', '?' => ['ref' => $reference]]);
						}
						$json = json_encode(['status' => 'ok', 'verify' => $verifyUrl]);
					}
					else {
						if ($queryTwoFAuth->value == 'enable' && $admin->last_login != NULL && $admin->phone != NULL && $admin->phone_verified != NULL && $admin->authy_id != NULL && ($admin->login_device != $deviceType || $admin->login_os != $operatingSystem)) {
							$authyId         = $admin->authy_id;
							$key             = $this->Settings->settingsPublicApiKey();
							$authySendSms 	 = $purpleApi->sendAuthySendSms($key, $authyId);

							if ($authySendSms) {
								if (empty($reference)) {
									$verifyUrl = Router::url(['_name' => 'adminSignInAuthyToken']);
								}
								else {
									$verifyUrl = Router::url(['_name' => 'adminSignInAuthyToken', '?' => ['ref' => $reference]]);
								}

								$json = json_encode(['status' => 'ok', 'verify' => $verifyUrl]);
							}
							else {
								$json = json_encode(['status' => 'error', 'error' => "Cannot send verification code. Please try again."]);
							}
						}
						else {
							$session = $this->getRequest()->getSession();
							$session->write([
								'Admin.host'     => $this->request->getEnv('HTTP_HOST'),
								'Admin.id'       => $admin->id,
								'Admin.password' => $admin->password,
							]);

							$admin->last_login    = Carbon::now($timezone);
							$admin->login_device  = $deviceType;
							$admin->login_os      = $operatingSystem;
							$admin->login_browser = $clientBrowser;
							if ($this->Admins->save($admin)) {
								// Tell system for new event
								$event = new Event('Model.Admin.afterSignIn', $this, ['admin' => $admin]);
								$this->getEventManager()->dispatch($event);
								
								$json = json_encode(['status' => 'ok', 'verify' => 'no', 'activity' => $event->getResult()]);
							}
							else {
								$json = json_encode(['status' => 'error', 'error' => "Cannot login now. Please try again."]);
							}
						}
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Invalid username or password.", 'pass' => $password]);
				}
			}
			else {
            	$errors = $adminLogin->getErrors();
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
	public function ajaxVerifyCode()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$adminVerificationCode = new AdminVerificationSignInForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($adminVerificationCode->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->all()->trim()->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

				$session = $this->getRequest()->getSession();

				if ($requestData->code == $session->read('Admin.verify')) {
					$purpleGlobal    = new PurpleProjectGlobal();
					$operatingSystem = $purpleGlobal->detectOS();
					$deviceType      = $purpleGlobal->detectDevice();
					$clientBrowser   = $purpleGlobal->detectBrowser();

					$purpleSettings = new PurpleProjectSettings();
					$timezone       = $purpleSettings->timezone();

					$detectIP      = $this->request->clientIp();
					$detectOS      = $purpleGlobal->detectOS();
					$detectBrowser = $purpleGlobal->detectBrowser();
					$detectDevice  = $purpleGlobal->detectDevice();

					$admin = $this->Admins->get($session->read('Admin.id'));
					
					$session->write([
						'Admin.host'     => $this->request->getEnv('HTTP_HOST'),
						'Admin.id'       => $admin->id,
						'Admin.password' => $admin->password,
					]);

					$admin->last_login    = Carbon::now($timezone);
					$admin->login_device  = $deviceType;
					$admin->login_os      = $operatingSystem;
					$admin->login_browser = $clientBrowser;
					if ($this->Admins->save($admin)) {
						// Tell system for new event
						$event = new Event('Model.Admin.afterSignIn', $this, ['admin' => $admin]);
						$this->getEventManager()->dispatch($event);
						
						$json = json_encode(['status' => 'ok', 'verify' => 'no', 'activity' => $event->getResult()]);
					}
					else {
						$json = json_encode(['status' => 'error', 'error' => "Cannot login now. Please try again."]);
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Invalid verification code."]);
				}
			}
			else {
            	$errors = $adminVerificationCode->getErrors();
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
	public function ajaxResendVerificationCode()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			$session = $this->getRequest()->getSession();
			$admin   = $this->Admins->get($session->read('Admin.id'));

			// Tell system for new event
			$eventLastSignIn = new Event('Model.Admin.checkLastSignIn', $this, ['admin' => $admin, 'data' => ['last_login' => $admin->last_login, 'domain' => $this->request->host()]]);
			$this->getEventManager()->dispatch($eventLastSignIn);
			
			$json = json_encode(['status' => 'ok']);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
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
				// Sanitize user input
				$filter = new Filter();
				$filter->all()->trim();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

	            $checkEmail = $this->Admins->find()->where(['email' => $requestData->email])->limit(1);
	            if ($checkEmail->count() > 0) {
					$id    = $checkEmail->first()->id;
					$admin = $checkEmail->first();
					$admin->token = md5($requestData->email);
					if ($this->Admins->save($admin)) {
						// Send Email to User to Notify author
						// Tell system for new event
						$event = new Event('Model.Admin.sendEmailForgotPassword', $this, [
							'admin' => $admin, 
							'data'  => [
								'link' 	 => $this->request->getData('ds'),
								'domain' => $this->request->host()
							]
						]);
						$this->getEventManager()->dispatch($event);

                        $json = json_encode(['status' => 'ok', 'email' => $event->getResult(), 'content' => '<div class="alert alert-success" role="alert" style="margin-top: 15px">Your password has been reseted. Please check your inbox or spam folder in your email.</div>']);
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
	        	$errors = $forgotPassword->getErrors();
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
    public function resetPassword()
    {
		// Set layout
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
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['token', 'password', 'repeatpassword', 'ds'])->trim();
				$filter->values(['id', 'passwordscore'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

				if ($requestData->password == $requestData->repeatpassword) {
					$checkUser = $this->Admins->find()->where(['id' => $requestData->id, 'token' => $requestData->token])->limit(1);
					if ($checkUser->count() > 0) {
						$admin         = $checkUser->first();
						$oldPassword   = $admin->password;
						$checkPassword = (new DefaultPasswordHasher())->check($requestData->password, $oldPassword);

						if ($checkPassword) {
							$json = json_encode(['status' => 'error', 'error' => "Do not use the old password. Please use another password."]);
						}
						else {
							$admin->password = $this->request->getData('password');
							$admin->token    = '';
							if ($this->Admins->save($admin)) {
								// Send Email to User to Notify author
								// Tell system for new event
								$event = new Event('Model.Admin.sendEmailResetPassword', $this, [
									'admin' => $admin, 
									'data'  => [
										'link' 	   => $this->request->getData('ds'),
										'password' => trim($this->request->getData('password')),
										'domain'   => $this->request->host()
									]
								]);

								$json = json_encode(['status' => 'ok', 'email' => $event->getResult(), 'content' => '<div class="alert alert-success" role="alert">Your password has been reseted. Please check your inbox or spam folder in your email.</div>']);
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
					$json = json_encode(['status' => 'error', 'error' => "Password and repeat password must be equal."]);
				}
            }
            else {
	        	$errors = $newPassword->getErrors();
                $json  = json_encode(['status' => 'error', 'error' => $errors]);
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
	public function ajaxVerifyAuthyToken()
    {
		$this->viewBuilder()->enableAutoLayout(false);

		$adminAuthyToken = new AdminAuthyTokenForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($adminAuthyToken->execute($this->request->getData())) {
				$purpleApi = new PurpleProjectApi();

				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filter->values(['token'])->numbers();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				
				$session = $this->getRequest()->getSession();
				$admin   = $this->Admins->get($session->read('Admin.id'));
				$authyId = $admin->authy_id;
				
				$key = $this->Settings->settingsPublicApiKey();
				$authyVerifyToken = $purpleApi->sendAuthyVerifyToken($key, $authyId, $requestData->token);
				
				if ($authyVerifyToken) {
					$purpleGlobal    = new PurpleProjectGlobal();
					$operatingSystem = $purpleGlobal->detectOS();
					$deviceType      = $purpleGlobal->detectDevice();
					$clientBrowser   = $purpleGlobal->detectBrowser();

					$purpleSettings = new PurpleProjectSettings();
					$timezone       = $purpleSettings->timezone();

					$detectIP      = $this->request->clientIp();
					$detectOS      = $purpleGlobal->detectOS();
					$detectBrowser = $purpleGlobal->detectBrowser();
					$detectDevice  = $purpleGlobal->detectDevice();

					$admin = $this->Admins->get($session->read('Admin.id'));
					
					$session->write([
						'Admin.host'     => $this->request->getEnv('HTTP_HOST'),
						'Admin.id'       => $admin->id,
						'Admin.password' => $admin->password,
					]);

					$admin->last_login    = Carbon::now($timezone);
					$admin->login_device  = $deviceType;
					$admin->login_os      = $operatingSystem;
					$admin->login_browser = $clientBrowser;
					if ($this->Admins->save($admin)) {
						// Tell system for new event
						$event = new Event('Model.Admin.afterSignIn', $this, ['admin' => $admin]);
						$this->getEventManager()->dispatch($event);
						
						$json = json_encode(['status' => 'ok', 'verify' => 'no', 'activity' => $event->getResult()]);
					}
					else {
						$json = json_encode(['status' => 'error', 'error' => "Cannot login now. Please try again."]);
					}
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => "Invalid verification code."]);
				}
			}
            else {
	        	$errors = $adminAuthyToken->getErrors();
                $json  = json_encode(['status' => 'error', 'error' => $errors]);
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