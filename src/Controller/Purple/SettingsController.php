<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Http\Exception\NotFoundException;
use Cake\Filesystem\File;
use Cake\Mailer\Email;
use App\Form\Purple\SettingsStandardModalForm;
use App\Form\Purple\SettingsTestEmailForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use App\Purple\PurpleProjectPlugins;
use Melbahja\Seo\Factory;

class SettingsController extends AppController
{
    public $imagesLimit = 30;

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

        $this->loadComponent('Paginator');

        $this->viewBuilder()->setLayout('dashboard');
        $this->loadModel('Admins');
        $this->loadModel('Settings');
        $this->loadModel('Medias');

        if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
            $cakeDebug = 'on';
        } 
        else {
            $cakeDebug = 'off';
        }

        $queryAdmin   = $this->Admins->signedInUser($sessionID, $sessionPassword);
		$queryFavicon = $this->Settings->fetch('favicon');
        $medias       = $this->Medias->find('all', [
            'order' => ['Medias.id' => 'DESC']])->contain('Admins');

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
                    'adminName'         => ucwords($adminData->display_name),
                    'adminLevel'        => $adminData->level,
                    'adminEmail'        => $adminData->email,
                    'adminPhoto'        => $adminData->photo,
                    'greeting'          => '',
                    'dashboardSearch'   => $dashboardSearch,
                    'title'             => 'Settings | Purple CMS',
                    'pageTitle'         => 'Settings',
                    'pageTitleIcon'     => 'mdi-settings',
                    'pageBreadcrumb'    => 'Settings',
                    'appearanceFavicon' => $queryFavicon,
                    'mediaImageTotal'   => $medias->count(),
                    'mediaImageLimit'   => $this->imagesLimit
                ];
                $this->set($data);

                $this->paginate = [
                    'limit' => $this->imagesLimit,
                    'page'  => 1
                ];
                $browseMedias = $this->paginate($medias);
                $this->set('browseMedias', $browseMedias);
                // $this->set(compact('browseMedias'));
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
    public function apiKeyGenerator($length = 32)
	{
		$key = '';
		list($usec, $sec) = explode(' ', microtime());
		mt_srand((float) $sec + ((float) $usec * 100000));
		
		$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

		for ($i = 0; $i < $length; $i++)
		{
			$key .= $inputs[mt_rand(0,61)];
		}
		return $key;
	}
	public function general()
	{
        $querySiteName    = $this->Settings->find()->where(['name' => 'sitename'])->first();
        $queryTagLine     = $this->Settings->find()->where(['name' => 'tagline'])->first();
        $queryEmail       = $this->Settings->find()->where(['name' => 'email'])->first();
        $queryPhone       = $this->Settings->find()->where(['name' => 'phone'])->first();
        $queryAddress     = $this->Settings->find()->where(['name' => 'address'])->first();
        $queryDateFormat  = $this->Settings->find()->where(['name' => 'dateformat'])->first();
        $queryTimeFormat  = $this->Settings->find()->where(['name' => 'timeformat'])->first();
        $queryTimezone    = $this->Settings->find()->where(['name' => 'timezone'])->first();
        $queryHeadless    = $this->Settings->find()->where(['name' => 'headlessfront'])->first();
        $queryHeadlessWeb = $this->Settings->find()->where(['name' => 'headlessweb'])->first();

        $purpleSettings = new PurpleProjectSettings();
        $timezone       = $purpleSettings->timezone();

		$data = [
            'pageTitle'          => 'General',
            'pageBreadcrumb'     => 'Settings::General',
            'settingSiteName'    => $querySiteName,
            'settingTagLine'     => $queryTagLine,
            'settingEmail'       => $queryEmail,
            'settingPhone'       => $queryPhone,
            'settingAddress'     => $queryAddress,
            'settingDateFormat'  => $queryDateFormat,
            'settingTimeFormat'  => $queryTimeFormat,
            'settingTimezone'    => $queryTimezone,
            'settingHeadless'    => $queryHeadless,
            'settingHeadlessWeb' => $queryHeadlessWeb,
            'timezone'           => $timezone
        ];
    	$this->set($data);
	}
	public function security()
    {
        $queryRecaptchaSitekey = $this->Settings->find()->where(['name' => 'recaptchasitekey'])->first();
        $queryRecaptchaSecret  = $this->Settings->find()->where(['name' => 'recaptchasecret'])->first();
        $queryTwoFAuth         = $this->Settings->find()->where(['name' => '2fa'])->first();

        $keyFile = new File(__DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'config' . DS . 'production_key.php');
        if (empty($keyFile->read()) || $keyFile->read() == '') {
            $content = $this->Settings->fetch('productionkey', 'value');
        }
        else {
            $content = $keyFile->read();
        }
        
        $key = substr($content, 0, 20) . '...';

        $data = [
            'pageTitle'               => 'Security',
            'pageBreadcrumb'          => 'Settings::Security',
            'settingRecaptchaSitekey' => $queryRecaptchaSitekey,
            'settingRecaptchaSecret'  => $queryRecaptchaSecret,
            'settingTwoFAuth'         => $queryTwoFAuth,
            'productionKey'           => $key
        ];
        $this->set($data);
    }
    public function api()
    {
        $queryApiAccessKey = $this->Settings->find()->where(['name' => 'apiaccesskey'])->first();
        $queryApiSecretKey = $this->Settings->find()->where(['name' => 'apisecretkey'])->first();

        $data = [
            'pageTitle'           => 'API',
            'pageBreadcrumb'      => 'Settings::API',
            'settingApiAccessKey' => $queryApiAccessKey,
            'settingApiSecretKey' => $queryApiSecretKey
        ];
        $this->set($data);
    }
    public function posts()
    {
        $queryPostLimit     = $this->Settings->find()->where(['name' => 'postlimitperpage'])->first();
        $queryPostPermalink = $this->Settings->find()->where(['name' => 'postpermalink'])->first();

        $data = [
            'pageTitle'            => 'Posts',
            'pageBreadcrumb'       => 'Settings::Posts',
            'settingPostLimit'     => $queryPostLimit,
            'settingPostPermalink' => $queryPostPermalink,
        ];
        $this->set($data);
    }
    public function mediaStorage()
    {
        $queryMediaStorage   = $this->Settings->fetch('mediastorage');
        $queryAwsS3AccessKey = $this->Settings->fetch('awss3accesskey');
        $queryAwsS3SecretKey = $this->Settings->fetch('awss3secretkey');
        $queryAwsS3Region    = $this->Settings->fetch('awss3region');
        $queryAwsS3Bucket    = $this->Settings->fetch('awss3bucket');

        $data = [
            'pageTitle'             => 'Media Storage',
            'pageBreadcrumb'        => 'Settings::Media Storage',
            'settingMediaStorage'   => $queryMediaStorage,
            'settingAwsS3AccessKey' => $queryAwsS3AccessKey,
            'settingAwsS3SecretKey' => $queryAwsS3SecretKey,
            'settingAwsS3Region'    => $queryAwsS3Region,
            'settingAwsS3Bucket'    => $queryAwsS3Bucket,
        ];
        $this->set($data);
    }
    public function maintenance()
	{
        $queryComingSoon           = $this->Settings->find()->where(['name' => 'comingsoon'])->first();
        $queryBackgroundComingSoon = $this->Settings->find()->where(['name' => 'backgroundmaintenance'])->first();

		$data = [
            'pageTitle'           => 'Maintenance',
            'pageBreadcrumb'      => 'Settings::Maintenance',
            'settingComingSoon'   => $queryComingSoon,
            'settingBgComingSoon' => $queryBackgroundComingSoon,
		];
    	$this->set($data);
	}
    public function personalize()
	{
        $queryDefaultBackgroundLogin = $this->Settings->find()->where(['name' => 'defaultbackgroundlogin'])->first();
        $queryBackgroundLogin        = $this->Settings->find()->where(['name' => 'backgroundlogin'])->first();

		$data = [
			'pageTitle'              => 'Personalize',
			'pageBreadcrumb'         => 'Settings::Personalize',
            'settingDefaultBgLogin'  => $queryDefaultBackgroundLogin,
            'settingBgLogin'         => $queryBackgroundLogin
		];
    	$this->set($data);
	}
    public function seo()
	{
        $this->loadModel('Socials');
        $purpleGlobal = new PurpleProjectGlobal();
        
        $queryMetaKeywords    = $this->Settings->find()->where(['name' => 'metakeywords'])->first();
        $queryMetaDescription = $this->Settings->find()->where(['name' => 'metadescription'])->first();
        $queryGoogleAnalytics = $this->Settings->find()->where(['name' => 'googleanalyticscode'])->first();
        $queryLdJson          = $this->Settings->find()->where(['name' => 'ldjson'])->first();

        $querySiteName        = $this->Settings->find()->where(['name' => 'sitename'])->first();
		$logo                 = $this->Settings->find()->where(['name' => 'websitelogo'])->first();

        // Generate Schema.org ld+json
        $protocol = $purpleGlobal->protocol();
        $socials  = $this->Socials->find('all')->select(['link'])->order(['ordering' => 'ASC'])->toArray();

        $websiteSchema = Factory::schema('website')
                    ->name($querySiteName->value)
                    ->url($protocol.$this->request->host().$this->request->getAttribute("webroot"));
        
        $orgSchema     = Factory::schema('organization')
                    ->url($protocol.$this->request->host().$this->request->getAttribute("webroot"))          
                    ->name($querySiteName->value)
                    ->description($queryMetaDescription->value);

        if ($logo->value != '') {
            $orgSchema->logo($protocol.$this->request->host().$this->request->getAttribute("webroot").'uploads/images/original/'.$logo->value);
        } 

        if (count($socials) > 0) {
            $socialList = array();
            foreach ($socials as $social) {
                $socialList[] = $social['link'];
            }
            $websiteSchema->sameAs($socialList);
            $orgSchema->sameAs($socialList);
        }

		$data = [
			'pageTitle'              => 'Search Engine Optimization',
			'pageBreadcrumb'         => 'Settings::SEO',
            'settingMetaKeywords'    => $queryMetaKeywords,
            'settingMetaDescription' => $queryMetaDescription,
            'settingGoogleAnalytics' => $queryGoogleAnalytics,
            'settingLdJson'          => $queryLdJson,
            'settingSiteName'        => $querySiteName,
            'ldJsonWebsite'          => $purpleGlobal->reformatLdJson($websiteSchema),
            'ldJsonOrganization'     => $purpleGlobal->reformatLdJson($orgSchema)
		];
    	$this->set($data);
	}
	public function email()
	{
        $settingsTestEmail = new SettingsTestEmailForm();

        $querySmtpHost     = $this->Settings->find()->where(['name' => 'smtphost'])->first();
        $querySmtpAuth     = $this->Settings->find()->where(['name' => 'smtpauth'])->first();
        $querySmtpUsername = $this->Settings->find()->where(['name' => 'smtpusername'])->first();
        $querySmtpPassword = $this->Settings->find()->where(['name' => 'smtppassword'])->first();
        $querySmtpSecure   = $this->Settings->find()->where(['name' => 'smtpsecure'])->first();
        $querySmtpPort     = $this->Settings->find()->where(['name' => 'smtpport'])->first();
        $querySenderEmail  = $this->Settings->find()->where(['name' => 'senderemail'])->first();
        $querySenderName   = $this->Settings->find()->where(['name' => 'sendername'])->first();

		$data = [
			'pageTitle'           => 'Email',
			'pageBreadcrumb'      => 'Settings::Email',
            'settingSmtpHost'     => $querySmtpHost,
            'settingSmtpAuth'     => $querySmtpAuth,
            'settingSmtpUsername' => $querySmtpUsername,
            'settingSmtpPassword' => $querySmtpPassword,
            'settingSmtpSecure'   => $querySmtpSecure,
            'settingSmtpPort'     => $querySmtpPort,
            'settingSenderEmail'  => $querySenderEmail,
            'settingSenderName'   => $querySenderName,
            'settingsTestEmail'   => $settingsTestEmail
		];
    	$this->set($data);
	}
    public function ajaxFormStandardSetting()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $settingsStandardModal  = new SettingsStandardModalForm();

            $purpleSettings = new PurpleProjectSettings();
            $timezone       = $purpleSettings->timezone();
            $timezoneList   = $purpleSettings->generateTimezoneList();

            if ($this->request->getData('title') == 'Production Key') {
                $keyFile = new File(__DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'config' . DS . 'production_key.php');
                if (empty($keyFile->read()) || $keyFile->read() == '') {
                    $value = $this->Settings->fetch('productionkey', 'value');
                }
                else {
                    $value = $keyFile->read();
                }

                $name    = 'productionkey';
            }
            else {
                $settingsTable = $this->Settings;
                $setting       = $settingsTable->get($this->request->getData('id'));
                $value         = $setting->value;
                $name          = $setting->name;
            }

            $data = [
                'settingsStandardModal' => $settingsStandardModal,
                'value'                 => $value,
                'name'                  => $name,
                'title'                 => $this->request->getData('title'),
                'redirect'              => $this->request->getData('redirect'),
                'timezone'              => $timezone,
                'timezoneList'          => $timezoneList
            ];
            $this->set($data);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxUpdate()
	{
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');
            $admin     = $this->Admins->get($sessionID);
            
            $settingsTable = $this->Settings;
            $setting       = $settingsTable->get($this->request->getData('id'));

            $setting->value = $this->request->getData('value');

            if ($settingsTable->save($setting)) {
                // Send email to subscriber if the website is online
                $purpleApi   = new PurpleProjectApi();
                $record_id   = $setting->id;
                $maintenance = $this->Settings->get($record_id);
                if ($maintenance->name == 'comingsoon' && $maintenance->value == 'disable') {
                    $this->loadModel('Subscribers');

                    $subscribers      = $this->Subscribers->find()->where(['status' => 'active']);
                    $totalSubscribers = $subscribers->count();

                    $emailStatus = [];
                    $counter = 0;
                    foreach ($subscribers as $subscriber) {
                        $key    = $this->Settings->settingsPublicApiKey();
                        $userData      = array(
                            'sitename'    => $this->Settings->settingsSiteName(),
                            'email'       => $subscriber->email,
                        );
                        $senderData   = array(
                            'domain' => $this->request->host()
                        );
                        $notifySubscriber = $purpleApi->sendEmailOnlineWebsite($key, json_encode($userData), json_encode($senderData));

                        if ($notifySubscriber == true) {
                            $counter++;
                            $emailStatus[$subscriber->email] = true; 
                        }
                        else {
                            $emailStatus[$subscriber->email] = false; 
                        }
                    }

                    if ($totalSubscribers == $counter) {
                        $emailNotification = true;
                    }
                    else {
                        $emailNotification = false;
                    }
                }
                else {
                    $emailNotification = false;
                    $emailStatus = false;
                }

                // Tell system for new event
                $event = new Event('Model.Setting.afterSave', $this, ['setting' => $setting, 'admin' => $admin]);
                $this->getEventManager()->dispatch($event);

                $json = json_encode(['status' => 'ok', 'activity' => $event->getResult(), 'notification' => $emailStatus]);

                $this->Flash->set('Setting has been updated.', [
                    'element' => 'Flash/Purple/success'
                ]);
            }
            else {
                $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
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
    public function ajaxSendTestEmail()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $settingsTestEmail = new SettingsTestEmailForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($settingsTestEmail->execute($this->request->getData())) {
                $querySmtpHost     = $this->Settings->find()->where(['name' => 'smtphost'])->first();
                $querySmtpAuth     = $this->Settings->find()->where(['name' => 'smtpauth'])->first();
                $querySmtpUsername = $this->Settings->find()->where(['name' => 'smtpusername'])->first();
                $querySmtpPassword = $this->Settings->find()->where(['name' => 'smtppassword'])->first();
                $querySmtpSecure   = $this->Settings->find()->where(['name' => 'smtpsecure'])->first();
                $querySmtpPort     = $this->Settings->find()->where(['name' => 'smtpport'])->first();
                $querySenderEmail  = $this->Settings->find()->where(['name' => 'senderemail'])->first();
                $querySenderName   = $this->Settings->find()->where(['name' => 'sendername'])->first();

                if ($querySmtpPort->value == 'tls') {
                    $tls = true;
                }
                elseif ($querySmtpPort->value == 'ssl') {
                    $tls = null;
                }

                if ($querySenderEmail->value == '') {
                    $senderEmail = $querySmtpUsername->value;
                }
                else {
                    $senderEmail = $querySenderEmail->value;
                }

                $emailSubject = 'Test Send Email from Purple CMS';
                $emailBody    = 'Hey there, this email was sent from Purple CMS';


                $email = new Email();
                $email->setFrom([$senderEmail => $querySenderName->value])
                    ->setTo($this->request->getData('email'))
                    ->setSubject($emailSubject);

                if ($email->send($emailBody)) {
                    $json = json_encode(['status' => 'ok']);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't send email to ".$this->request->getData('email')]);
                }
            }
            else {
                $errors = $settingsTestEmail->getErrors();
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
    public function ajaxGenerateApiKey()
    {
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $hashApiKey = $this->apiKeyGenerator();

            $json = json_encode(['status' => 'ok', 'content' => $hashApiKey]);

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