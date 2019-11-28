<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
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
use Particle\Filter\Filter;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class AdminsController extends AppController
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
				['_name' => 'adminLogin']
			);
		}
	}
	public function initialize()
	{
        parent::initialize();
        
		// Get Admin Session data
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

        $this->viewBuilder()->setLayout('dashboard');
        $this->loadModel('Settings');

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

            list($type, $base64) = explode(';', $base64);
            list(, $base64)      = explode(',', $base64);
            $base64Decode		 = base64_decode($base64);
            $name                = md5(time());

            if (file_put_contents($fullSizeImage . $name.'.png', $base64Decode)) {
                // Check for media storage
				$mediaStorage   = $this->Settings->fetch('mediastorage');
				
				// If media storage is Amazon AWS S3
				if ($mediaStorage->value == 'awss3') {
					$awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
					$awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
					$awsS3Region    = $this->Settings->fetch('awss3region');
					$awsS3Bucket    = $this->Settings->fetch('awss3bucket');

					$s3Client = new S3Client([
						'region'  => $awsS3Region->value,
						'version' => 'latest',
						'credentials' => [
							'key'      => $awsS3AccessKey->value,
							'secret'   => $awsS3SecretKey->value,
						]
					]);

					// Path
					$originalFilePath = $fullSizeImage . $name . '.png';

					// Key
					$originalKey = 'images/original/' . basename($originalFilePath);
					
					// Send original image
					try {
						$result = $s3Client->putObject([
							'Bucket'     => $awsS3Bucket->value,
							'Key'        => $originalKey,
							'SourceFile' => $originalFilePath,
							'ACL'        => 'public-read',
						]);

						$readImageFile = new File($fullSizeImage . $saveType . '.png');
						$readImageFile->delete();
					} 
					catch (AwsException $e) {
						$this->log($e->getMessage(), 'debug');
					}

					$path = $s3Client->getObjectUrl($awsS3Bucket->value, $originalKey);;
				}
				else {
					$baseUrl = Router::url([
						'_name' => 'home'
					], true);

					$path = $baseUrl . 'uploads/images/original/' . $generatedName;
                }
                
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
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['email', 'username', 'password', 'repeatpassword', 'about', 'photo', 'ds'])->trim()->stripHtml();
				$filter->values(['display_name'])->trim()->alnum()->stripHtml();
				$filter->values(['level'])->int();
				$filterResult = $filter->filter($this->request->getData());
                $requestData  = json_decode(json_encode($filterResult), FALSE);
                $requestArray = json_decode(json_encode($filterResult), TRUE);
                
                if ($requestData->password == $requestData->repeatpassword) {
                    $purpleApi = new PurpleProjectApi();
                    $verifyEmail = $purpleApi->verifyEmail($requestData->email);

                    if ($verifyEmail == true) {
                        $session       = $this->getRequest()->getSession();
                        $sessionID     = $session->read('Admin.id');
                        $signedInadmin = $this->Admins->get($sessionID);
                        $queryAdmin    = $this->Admins->find()->where(['id' => $sessionID])->limit(1);

                        $findDuplicate = $this->Admins->find()->where([
                            'OR' => [['username' => $requestData->username], ['email' => $requestData->email]]
                        ]);

                        if ($findDuplicate->count() >= 1) {
                            $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another username or email."]);
                        }
                        else {
                            if(preg_match('/[A-Z]/', $requestData->username)){
                                $json = json_encode(['status' => 'error', 'error' => "Please use lowercase letter for username."]);
                            }
                            else {
                                $admin = $this->Admins->newEntity();
                                $admin = $this->Admins->patchEntity($admin, $requestArray);
                            
                                if ($this->Admins->save($admin)) {
                                    $recordId = $admin->id;
                                    $admin     = $this->Admins->get($recordId);

                                    // Tell system for new event
                                    $event = new Event('Model.Admin.afterSave', $this, ['user' => $admin, 'admin' => $signedInadmin, 'save' => 'new']);
                                    $this->getEventManager()->dispatch($event);

                                    // Send Email to User to Notify author
                                    $key    = $this->Settings->settingsPublicApiKey();
                                    $dashboardLink = $this->request->getData('ds').'/edit/'.$recordId;
                                    $userData      = array(
                                        'sitename'    => $this->Settings->settingsSiteName(),
                                        'username'    => $admin->username,
                                        'password'    => $requestData->password,
                                        'email'       => $admin->email,
                                        'displayName' => $admin->display_name,
                                        'level'       => $admin->level
                                    );
                                    $senderData   = array(
                                        'name'   => $queryAdmin->first()->display_name,
                                        'domain' => $this->request->host()
                                    );
                                    $notifyUser = $purpleApi->sendEmailNewUser($key, $dashboardLink, json_encode($userData), json_encode($senderData));

                                    if ($notifyUser == true) {
                                        $emailNotification = true;
                                    }
                                    else {
                                        $emailNotification = false;
                                    }

                                    if ($event->getResult()) {
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
                    $json = json_encode(['status' => 'error', 'error' => "Password and repeat password is not equal. Please try again."]);
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
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['email', 'username', 'about', 'photo'])->trim()->stripHtml();
				$filter->values(['display_name'])->trim()->alnum()->stripHtml();
				$filter->values(['level', 'id'])->int();
				$filterResult = $filter->filter($this->request->getData());
                $requestData  = json_decode(json_encode($filterResult), FALSE);
                $requestArray = json_decode(json_encode($filterResult), TRUE);

                $admin = $this->Admins->get($requestData->id);
                if ($requestData->email == $admin->email) {
                    $verifyEmail = true;
                }
                else {
                    $purpleApi = new PurpleProjectApi();
                    $verifyEmail = $purpleApi->verifyEmail($requestData->email);
                }

                if ($verifyEmail == true) {
                    $session       = $this->getRequest()->getSession();
                    $sessionID     = $session->read('Admin.id');
                    $signedInadmin = $this->Admins->get($sessionID);

                    $findDuplicate = $this->Admins->find()->where([
                    	'id <> ' => $this->request->getData('id'),
                    	'OR' => [['username' => $requestData->username], ['email' => $requestData->email]]
                    ]);

                    if ($findDuplicate->count() >= 1) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another username or email."]);
                    }
                    else {
                        if(preg_match('/[A-Z]/', $requestData->username)){
                            $json = json_encode(['status' => 'error', 'error' => "Please use lowercase letter for username."]);
                        }
                        else {
        					$this->Admins->patchEntity($admin, $requestArray);

                            if ($this->Admins->save($admin)) {
                                // Tell system for new event
                                $event = new Event('Model.Admin.afterSave', $this, ['user' => $admin, 'admin' => $signedInadmin, 'save' => 'update']);
                                $this->getEventManager()->dispatch($event);

                                if ($event->getResult()) {
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
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['email', 'username', 'password', 'repeatpassword'])->trim()->stripHtml();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
                $requestData  = json_decode(json_encode($filterResult), FALSE);
                $requestArray = json_decode(json_encode($filterResult), TRUE);

                $session       = $this->getRequest()->getSession();
                $sessionID     = $session->read('Admin.id');
                $signedInadmin = $this->Admins->get($sessionID);

                if ($requestData->password == $requestData->repeatpassword) {
                    $admin        = $this->Admins->get($requestData->id);
                    $this->Admins->patchEntity($admin, $requestArray);

                    if ($this->Admins->save($admin)) {
                        // Tell system for new event
                        $event = new Event('Model.Admin.afterUpdatePassword', $this, ['user' => $admin, 'admin' => $signedInadmin]);
                        $this->getEventManager()->dispatch($event);

                        if ($event->getResult()) {
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
                    $json = json_encode(['status' => 'error', 'error' => "Password and repeat password is not equal. Please try again."]);
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
                // Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
                $requestData  = json_decode(json_encode($filterResult), FALSE);

                $session       = $this->getRequest()->getSession();
                $sessionID     = $session->read('Admin.id');
                $signedInadmin = $this->Admins->get($sessionID);
                $queryAdmin    = $this->Admins->find()->where(['id' => $sessionID])->limit(1);
                
                $admin = $this->Admins->get($requestData->id);
				$name  = $admin->display_name;

				$result = $this->Admins->delete($admin);

                if ($result) {
                    // Tell system for new event
                    $event = new Event('Model.Admin.afterDelete', $this, ['user' => $admin, 'admin' => $signedInadmin]);
                    $this->getEventManager()->dispatch($event);

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
                        'domain' => $this->request->host()
                    );

                    $purpleApi  = new PurpleProjectApi();
                    $notifyUser = $purpleApi->sendEmailDeleteUser($key, json_encode($userData), json_encode($senderData));

                    if ($notifyUser == true) {
                        $emailNotification = true;
                    }
                    else {
                        $emailNotification = false;
                    }

                    if ($event->getResult()) {
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