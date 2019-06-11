<?php
namespace App\Controller;

use App\Form\ProductionUserVerificationForm;
use App\Form\ProductionVerifyCodeForm;
use App\Form\SetupDatabaseForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectApi;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Filesystem\File;

class ProductionController extends AppController
{
	public function initialize()
	{
        parent::initialize();
    }
    public function userVerification()
	{
		$purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo != 'default') {
			$userVerification = new ProductionUserVerificationForm();

	        if ($this->request->is('get')) {
		    	$this->viewBuilder()->setLayout('setup');
	        	$this->set('userVerification', $userVerification);
	        }
		}
		else {
			return $this->redirect(['controller' => 'Setup', 'action' => 'index']);
	    }
	}
	public function codeVerification()
	{
		$purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo != 'default') {
			$codeVerification = new ProductionVerifyCodeForm();

			$session     = $this->getRequest()->getSession();
			$sessionUser = $session->read('User.Email');

			if (!$session->check('User.Email')) {
				return $this->setAction('userVerification');
			}
			else {
				$this->viewBuilder()->setLayout('setup');
	        	$this->set('codeVerification', $codeVerification);
			}
		}
		else {
			return $this->redirect(['controller' => 'Setup', 'action' => 'index']);
	    }
	}
	public function databaseMigration()
	{
		$purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo != 'default') {
			
			$session = $this->getRequest()->getSession();
			$session->write([
				'User.Status' => 1,
			]);
			$sessionUser   = $session->read('User.Email');
			$sessionStatus = $session->read('User.Status');

			if ($session->check('User.Email') && $session->check('User.Status')) {
				if ($session->read('User.Status') != 1) {
					return $this->setAction('userVerification');
				}
				else {
					$setupDatabase = new SetupDatabaseForm();
					$this->viewBuilder()->setLayout('setup');
					$this->set('setupDatabase', $setupDatabase);
				}
			}
			else {
				return $this->setAction('userVerification');
			}
		}
		else {
			return $this->redirect(['controller' => 'Setup', 'action' => 'index']);
	    }
    }
    public function ajaxUserVerification()
	{
		$userVerification = new ProductionUserVerificationForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($userVerification->execute($this->request->getData())) {
				$email = trim($this->request->getData('email'));
				$key   = trim($this->request->getData('key'));

				$keyFile = new File(__DIR__ . DS . '..' . DS . '..' . DS . 'config' . DS . 'production_key.php');
				$content = $keyFile->read();

				if ($key == $content) {
					// Generate 6 digits code
					$code = rand(100000, 999999);

					$session = $this->getRequest()->getSession();
					$session->write([
						'User.Email'  => $email,
						'User.Status' => 0,
						'User.Code'   => $code
					]);

					// Send Email to User to Notify user
					$hasher = new DefaultPasswordHasher();
					$key    = $hasher->hash('public-purple is awesome');
					$userData      = array(
						'sitename'    => 'Purple CMS',
						'email'       => $email,
						'code'        => $code
					);
					$senderData   = array(
						'domain' => $this->request->domain()
					);
					$purpleApi = new PurpleProjectApi();
					$notifyUser = $purpleApi->sendEmailUserVerification($key, json_encode($userData), json_encode($senderData));

					if ($notifyUser == true) {
						$emailNotification = true;
					}
					else {
						$emailNotification = false;
					}

					$json = json_encode(['status' => 'ok', 'email' => [$email => $emailNotification]]);
				}
				else {
					$json = json_encode(['status' => 'error', 'error' => 'Wrong production key. Please use correct production key in the local machine.', 'error_type' => 'key']);
				}
			}
			else {
				$errors = $userVerification->errors();
				$json = json_encode(['status' => 'error', 'error' => $errors, 'error_type' => 'form']);
			}
			$this->set(['json' => $json]);
		}
		else {
			throw new NotFoundException(__('Page not found'));
		}
    }
    public function ajaxCodeVerification()
    {
        $codeVerification = new ProductionVerifyCodeForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($codeVerification->execute($this->request->getData())) {
                $code = trim($this->request->getData('code'));
                $session     = $this->getRequest()->getSession();
                $sessionCode = $session->read('User.Code');

                if ($code == $sessionCode) {
					$session->write([
						'User.Status' => 1,
					]);
                    $json = json_encode(['status' => 'ok']);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => 'Wrong verification code.']);
                }
            }
            else {
                $errors = $codeVerification->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors, 'error_type' => 'form']);
            }
            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
	public function ajaxDatabaseMigration()
	{
		$setupDatabase = new SetupDatabaseForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($setupDatabase->execute($this->request->getData())) {
				$name     = trim(strtolower($this->request->getData('name')));
				$username = trim($this->request->getData('username'));
				$password = trim($this->request->getData('password'));

				if (strpos($name, ' ') !== false) {
	                $json = json_encode(['status' => 'error', 'error' => "Invalid database name. Please try again."]);
				}
				else {
					$databaseInfo = $name . ',' . $username .',' . $password;

					$file      = new File(__DIR__ . DS . '..' . DS . '..' . DS . 'config' . DS . 'database.php');
					$encrypted = \Dcrypt\Aes::encrypt($databaseInfo, CIPHER);

	            	if ($file->write($encrypted)) {
		                $json = json_encode(['status' => 'ok']);
		            }
		            else {
		                $json = json_encode(['status' => 'error', 'error' => "Can't save database configuration."]);
		            }
		        }
			}
			else {
            	$errors = $setupDatabase->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors, 'error_type' => 'form']);
            }
            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}