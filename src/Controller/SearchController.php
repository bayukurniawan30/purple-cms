<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Log\Log;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Form\SearchForm;
use Carbon\Carbon;
use Melbahja\Seo\Factory;
use EngageTheme\Functions\ThemeFunction;

class SearchController extends AppController
{
    public $tagsSidebarLimit = 10;
    
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
        else {
            $purpleSettings = new PurpleProjectSettings();
            $maintenance    = $purpleSettings->maintenanceMode();
            $userLoggedIn   = $purpleSettings->checkUserLoggedIn();

            if ($maintenance == 'enable' && $userLoggedIn == false) {
                return $this->redirect(
                    ['controller' => 'Maintenance', 'action' => 'index']
                );
            }
        }
    }
    public function beforeRender(\Cake\Event\Event $event)
    {
        $this->viewBuilder()->setTheme('EngageTheme');
        $this->viewBuilder()->setLayout('EngageTheme.default');
    }
    public function index()
    {
        if ($this->request->is('post')) {
            $this->loadModel('Settings');
            $this->loadModel('Admins');
            $this->loadModel('Menus');
            $this->loadModel('Blogs');
            $this->loadModel('Tags');
            $this->loadModel('Visitors');
            $this->loadModel('Socials');

            $purpleSettings = new PurpleProjectSettings();
            $timezone       = $purpleSettings->timezone();
            $visitorDate    = Carbon::now($timezone);

            // Check visitor
            $purpleGlobal = new PurpleProjectGlobal();
            $browser      = $purpleGlobal->detectBrowser();
            $platform     = $purpleGlobal->detectOS();
            $device       = $purpleGlobal->detectDevice();
            $ip           = $this->request->clientIp();
            $checkVisitor = $this->Visitors->checkVisitor($ip, $visitorDate, $browser, $platform, $device);

            if ($checkVisitor == 0) {
            $visitor = $this->Visitors->newEntity();
                $visitor->ip       = $ip;
                $visitor->browser  = $browser;
                $visitor->platform = $platform;
                $visitor->device   = $device;

                $this->Visitors->save($visitor);
            }

            $isVisitorsEnough = $this->Visitors->isVisitorsEnough();

            if ($isVisitorsEnough) {
                $totalAllVisitors = $this->Visitors->totalAllVisitors();

                // Send Email to User to Notify user
                $users     = $this->Admins->find()->where(['username <> ' => 'creatifycore'])->order(['id' => 'ASC']);
                $totalUser = $users->count();

                $emailStatus = [];
                $counter = 0;
                foreach ($users as $user) {
                    $key           = $this->Settings->settingsPublicApiKey();
                    $userData      = array(
                        'sitename'    => $this->Settings->settingsSiteName(),
                        'email'       => $user->email,
                        'displayName' => $user->display_name,
                        'level'       => $user->level
                    );
                    $senderData   = array(
                        'total'   => $totalAllVisitors,
                        'domain'  => $this->request->domain()
                    );
                    $notifyUser = $purpleApi->sendEmailCertainVisitors($key, json_encode($userData), json_encode($senderData));

                    if ($notifyUser == true) {
                        $counter++;
                        $emailStatus[$user->email] = true; 
                    }
                    else {
                        $emailStatus[$user->email] = false; 
                    }
                }

                if ($totalUser == $counter) {
                    $emailNotification = true;
                }
                else {
                    $emailNotification = false;
                }

                // Log::write('debug', $emailNotification);
            }

            $search = new SearchForm();

            if ($this->request->getEnv('HTTP_HOST') == 'localhost') {
                $cakeDebug    = 'on';
                $formSecurity = 'off';
            } 
            else {
                if (Configure::read('debug')) {
                    $cakeDebug = 'on';
                }
                else {
                    $cakeDebug = 'off';
                }
                $formSecurity  = 'on';
            }

            $archives    = $this->Blogs->archivesList();
            $tagsSidebar = $this->Tags->tagsSidebar($this->tagsSidebarLimit);

            // Search Post with related search
            $searchCondition = '%'.$this->request->getData('search').'%';
            $blogs = $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['Blogs.status' => '1', 'Blogs.title LIKE' => $searchCondition])->orWhere(['Blogs.content LIKE' => $searchCondition]);

            /**
             * Load Theme Global Function
             */

            $themeFunction = new ThemeFunction($this->request->getAttribute('webroot')); 
            $this->set('themeFunction', $themeFunction);

            $socials = $this->Socials->find('all')->order(['ordering' => 'ASC']);
            $this->set(compact('socials'));

            // Generate Schema.org ld+json
            $purpleSeo     = new PurpleProjectSeo();
            $websiteSchema = $purpleSeo->schemaLdJson('website');
            $orgSchema     = $purpleSeo->schemaLdJson('organization');

            $webpageSchemaOption = [
                "title" => "Search",
                "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot")."search"
            ];

            $webpageSchemaOption['description'] = NULL;
            $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

            $data['webpageSchema'] = $webpageSchema;

            $data = [
                'siteName'           => $this->Settings->settingsSiteName(),
                'tagLine'            => $this->Settings->settingsTagLine(),
                'metaKeywords'       => $this->Settings->settingsMetaKeywords(),
                'metaDescription'    => $this->Settings->settingsMetaDescription(),
                'googleAnalytics'    => $this->Settings->settingsAnalyticscode(),
                'metaOgType'         => 'website',
                'metaImage'          => '',
                'favicon'            => $this->Settings->settingsFavicon(),
                'logo'               => $this->Settings->settingsLogo(),
                'menus'              => $this->Menus->fetchPublishedMenus(),
                'leftFooter'         => $this->Settings->settingsLeftFooter(),
                'rightFooter'        => $this->Settings->settingsRightFooter(),
                'dateFormat'         => $this->Settings->settingsDateFormat(),
                'timeFormat'         => $this->Settings->settingsTimeFormat(),
                'cakeDebug'          => $cakeDebug,
                'formSecurity'       => $formSecurity,
                'pageTitle'          => 'Search',
                'breadcrumb'         => 'Home::Search',
                'searchText'         => $this->request->getData('search'),
                'searchResult'       => $blogs,
                'sidebarSearch'      => $search,
                'ldJsonWebsite'      => $websiteSchema,
                'ldJsonOrganization' => $orgSchema,
                'webpageSchema'      => $webpageSchema
            ];

            $this->set($data);

            $this->set(compact('archives'));
            $this->set(compact('tagsSidebar'));
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
}