<?php
namespace App\Controller;

use App\Form\SetupDatabaseForm;
use App\Form\SetupAdministrativeForm;
use App\Form\ProductionUserVerificationForm;
use App\Form\ProductionVerifyCodeForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSetup;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Text;
use Cake\Utility\Security;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use Carbon\Carbon;

class SetupController extends AppController
{
	public function initialize()
	{
	    parent::initialize();
	    // Temporary images folder
	    $dir = new Folder(TMP . 'exports', true, 0777);
	    $dir = new Folder(TMP . 'uploads', true, 0777);
		$dir = new Folder(TMP . 'uploads' . DS . 'images', true, 0777);
		$dir = new Folder(TMP . 'uploads' . DS . 'themes', true, 0777);

		// Storing images and thumbnails
		$dir = new Folder(WWW_ROOT . 'exports', true, 0777);
		$dir = new Folder(WWW_ROOT . 'uploads', true, 0777);
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'images', true, 0777);
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'images' . DS .'thumbnails', true, 0777);
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'images' . DS .'thumbnails' . DS . '300x300', true, 0777);
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'images' . DS .'thumbnails' . DS . '480x270', true, 0777);

		// Storing documents
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'documents', true, 0777);

		// Storing videos
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'videos', true, 0777);

		// Storing custom pages file
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'custom-pages', true, 0777);

		// Storing themes
		$dir = new Folder(WWW_ROOT . 'uploads' . DS . 'themes', true, 0777);

	}
	public function index()
	{	    
		$purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo == 'default') {
			$setupDatabase = new SetupDatabaseForm();

	        if ($this->request->is('get')) {
		    	$this->viewBuilder()->setLayout('setup');
	        	$this->set('setupDatabase', $setupDatabase);
	        }
		}
		else {
			return $this->redirect('/');
	    }
	}
	public function administrative()
	{
		$connection = ConnectionManager::get('default');
        $connected = $connection->connect();

        if ($connected) {
			$purpleGlobal = new PurpleProjectGlobal();
			$databaseInfo   = $purpleGlobal->databaseInfo();
			if ($databaseInfo == 'default') {
				return $this->redirect(
		            ['controller' => 'Setup', 'action' => 'index']
		        );
			}
			else {
				$setupAdministrative = new SetupAdministrativeForm();
                $purpleSetup         = new PurpleProjectSetup();
                $timezoneList        = $purpleSetup->generateTimezoneList();
                
                $data = [
                    'setupAdministrative' => $setupAdministrative,
                    'timezoneList'        => $timezoneList
                ];

		        if ($this->request->is('get')) {
			    	$this->viewBuilder()->setLayout('setup');
		        	$this->set($data);
		        }
			}
		}
		else {
			return $this->redirect(
	            ['controller' => 'Setup', 'action' => 'index']
	        );
		}
	}
	public function finish()
	{
		$connection = ConnectionManager::get('default');
        $connected = $connection->connect();

        if ($connected) {
			$purpleGlobal = new PurpleProjectGlobal();
			$databaseInfo   = $purpleGlobal->databaseInfo();
			if ($databaseInfo == 'default') {
			    return $this->redirect(
		            ['controller' => 'Setup', 'action' => 'index']
		        );
			}
			else {
			    $this->viewBuilder()->setLayout('setup');
			    $results = $connection
				    ->execute('SELECT * FROM admins WHERE id = :id', ['id' => 2])
				    ->fetch('assoc');
	        	$this->set('admin', $results);
			}
		}
		else {
			return $this->redirect(
	            ['controller' => 'Setup', 'action' => 'index']
	        );
		}
	}

	// Ajax Proccessing
	public function ajaxDatabase()
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
	public function ajaxAdministrative()
	{
		$setupAdministrative = new SetupAdministrativeForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($setupAdministrative->execute($this->request->getData())) {
            	$purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                if ($verifyEmail == true) {
	            	$connection = ConnectionManager::get('default');
	                
	                $purpleSetup = new PurpleProjectSetup();
	                $createTable   = $purpleSetup->createTable();

					$admin = TableRegistry::get('Admins')->newEntity();
	                
	                $purpleSettings = new PurpleProjectSettings();
				    $timezone       = $purpleSettings->timezone();
	                
	                $hasher        = new DefaultPasswordHasher();
	                $passwordInput = trim($this->request->getData('password'));
					$hashPassword  = $hasher->hash(trim($this->request->getData('password')));
					
					// Generate an API 'token'
					$apiKeyPlain = Security::hash(Security::randomBytes(32), 'sha256', false);

					// Bcrypt the token so BasicAuthenticate can check
					// it during login.
					$apiKey = $hasher->hash($apiKeyPlain);

					$admin->username      = trim($this->request->getData('username'));
					$admin->password      = $passwordInput;
					$admin->api_key_plain = $apiKeyPlain;
					$admin->api_key       = $apiKey;
					$admin->email         = trim($this->request->getData('email'));
					$admin->level         = '1';
					$admin->created       = Carbon::now($timezone);
					$admin->display_name  = trim($this->request->getData('username'));
					$admin->first_login   = 'yes';

					$updateSitename = $connection->update('settings', ['value' => trim($this->request->getData('sitename'))], ['name' => 'sitename']);
					$updateSiteurl  = $connection->update('settings', ['value' => $this->request->getData('siteurl')], ['name' => 'siteurl']);
					$updateFolder   = $connection->update('settings', ['value' => $this->request->getData('foldername')], ['name' => 'foldername']);
					$updateEmail    = $connection->update('settings', ['value' => $this->request->getData('email')], ['name' => 'email']);
	                $updateTimezone = $connection->update('settings', ['value' => $this->request->getData('timezone')], ['name' => 'timezone']);
					$updateFooter   = $connection->update('settings', ['value' => '&amp;copy; '.date('Y').' '.trim($this->request->getData('sitename')).'::Created with &#60;a href=http://purple-cms.com&#62;Purple&#60;/a&#62;'], ['name' => 'secondaryfooter']);
					
					if ($this->request->getData('foldername') == '') {
						$bindFolder = '';
					}
					else {
						$bindFolder = '/'.$this->request->getData('foldername');
					}
					$updateHomepage = $connection->update('settings', ['value' => '&lt;section id=&quot;fdb-71781&quot; class=&quot;fdb-block uk-flex uk-flex-middle&quot; data-fdb-id=&quot;71781&quot; style=&quot;background-image: linear-gradient(120deg, rgb(213, 126, 235) 0%, rgb(252, 203, 144) 100%); box-sizing: border-box; min-height: calc(100vh); height: 626px;&quot; uk-height-viewport=&quot;&quot;&gt;    &lt;div class=&quot;container&quot;&gt;&lt;div class=&quot;row uk-flex uk-flex-middle&quot;&gt;&lt;div class=&quot;col-12 col-md-6 col-lg-6 text-left fdb-editor royal-theme&quot;&gt;&lt;h1 class=&quot;fdb-heading&quot;&gt;&lt;span style=&quot;font-size: 42px;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;color: rgb(255, 255, 255);&quot;&gt;Welcome to Purple CMS&lt;/span&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/h1&gt;&lt;p class=&quot;text-h3&quot;&gt;&lt;span style=&quot;font-size: 24px; color: rgb(255, 255, 255);&quot;&gt;A Content Management System build with CakePHP 3. Aiming to make website developer easier and faster to make a website, whether simple or complex.&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;div class=&quot;col-12 col-md-6 col-lg-6&quot;&gt;&lt;img src=&quot;'.$bindFolder.'/master-assets/img/purple-dashboard.png&quot; class=&quot;img-fluid uk-border-rounded&quot;&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;'], ['name' => 'homepagestyle']);

					if (TableRegistry::get('Admins')->save($admin) && $updateSitename && $updateSiteurl && $updateFolder && $updateEmail && $updateTimezone) {
                        $record_id = $admin->id;
                        $admin     = TableRegistry::get('Admins')->get($record_id);

                        $blogCategory = TableRegistry::get('BlogCategories')->newEntity();
						$blogCategory->name     = 'Uncategorised';
						$blogCategory->slug     = 'uncategorised';
						$blogCategory->created  = Carbon::now($timezone);
						$blogCategory->ordering = '1';
						$blogCategory->admin_id = $record_id;

						TableRegistry::get('BlogCategories')->save($blogCategory);

						// Write production key
						$keyFile  = new File(__DIR__ . DS . '..' . DS . '..' . DS . 'config' . DS . 'production_key.php');
						$productionKey = $hasher->hash(time());
						$writeKey      = $keyFile->write($productionKey);

						// Send Email to User to Notify user
                        $key    = TableRegistry::get('Settings')->settingsPublicApiKey();
                        $dashboardLink = $this->request->getData('ds');
                        $userData      = array(
                            'sitename'    => 'Purple CMS',
                            'username'    => $admin->username,
                            'password'    => trim($this->request->getData('password')),
                            'email'       => $admin->email,
                            'displayName' => $admin->display_name,
                            'level'       => $admin->level,
                            'key'         => $productionKey
                        );
                        $senderData   = array(
                            'domain' => $this->request->domain()
                        );
                        $notifyUser = $purpleApi->sendEmailAdministrativeSetup($key, $dashboardLink, json_encode($userData), json_encode($senderData));

                        if ($notifyUser == true) {
                            $emailNotification = true;
                        }
                        else {
                            $emailNotification = false;
                        }

						$json = json_encode(['status' => 'ok', 'email' => [$admin->email => $emailNotification]]);
		            }
		            else {
		            	$json = json_encode(['status' => 'error', 'error' => "Can't save administrative configuration to database."]);
		            }
		        }
		        else {
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
		        }
            }
            else {
            	$errors = $setupAdministrative->errors();
                $json   = json_encode(['status' => 'error', 'error' => $errors, 'error_type' => 'form']);
            }
            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}