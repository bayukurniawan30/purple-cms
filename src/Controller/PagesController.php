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
use App\Purple\PurpleProjectApi;
use App\Form\PageContactForm;
use App\Form\SearchForm;
use Carbon\Carbon;
use Melbahja\Seo\Factory;
use EngageTheme\Functions\ThemeFunction;

class PagesController extends AppController
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
    public function initialize()
    {
        $this->loadModel('Settings');
        $this->loadModel('Admins');
        $this->loadModel('Menus');
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

        $data = [
            'pageTitle'          => 'Home',
            'childPage'          => false,
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
            'homepage'           => html_entity_decode($this->Settings->settingsHomepage()),
            'leftFooter'         => $this->Settings->settingsLeftFooter(),
            'rightFooter'        => $this->Settings->settingsRightFooter(),
            'dateFormat'         => $this->Settings->settingsDateFormat(),
            'timeFormat'         => $this->Settings->settingsTimeFormat(),
            'postsLimit'         => $this->Settings->settingsPostLimitPerPage(),
            'cakeDebug'          => $cakeDebug,
            'formSecurity'       => $formSecurity,
            'recaptchaSitekey'   => $this->Settings->settingsRecaptchaSitekey(),
            'recaptchaSecret'    => $this->Settings->settingsRecaptchaSecret(),
            'sidebarSearch'      => $search,
            'ldJsonWebsite'      => $websiteSchema,
            'ldJsonOrganization' => $orgSchema
        ];
        $this->set($data);
    }
    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }
    public function home() 
    {
        $themeFolder  = new File(PLUGINS . 'EngageTheme' . DS . 'detail.json');
        $detail       = $themeFolder->read();
        $decodeDetail = json_decode($detail, true);

        if ($decodeDetail['homepage']['use'] == 'default') {
            $homepage = html_entity_decode($this->Settings->settingsHomepage());

            $data = [
                'homepage' => $homepage
            ];

            $this->set($data);
        }
        elseif ($decodeDetail['homepage']['use'] == 'theme') {
            $themeFunction   = new ThemeFunction($this->request->getAttribute('webroot')); 

            $loadFunction    = $decodeDetail['homepage']['function'];

            if ($loadFunction != "") {
                $explodeFunction = explode(',', $loadFunction);
                $loadedFunction = [];
                foreach ($explodeFunction as $load) {
                    $loadedFunction[$load] = $themeFunction->$load();
                }

                $this->set($loadedFunction);
            }
            $this->render('EngageTheme.Home/index');
        }
    }
    public function index()
    {
        $slug = $this->request->getParam('page');
        $page = $this->Pages->find('all')->contain('PageTemplates')->where(['slug' => $slug, 'status' => '1']);
        if ($page->count() == 1) {
            $loadPage = $page->first();
            $isChild  = $loadPage->parent;

            if ($isChild != NULL ) {
                throw new NotFoundException(__('Page not found'));
            }
            else {
                if ($loadPage->page_template_id == '1') {
                    $viewPage = $this->Pages->find('all')->contain('Generals')->where(['Pages.slug' => $slug])->limit(1);
                    if ($viewPage->count() == 0) {
                        throw new NotFoundException(__('Page not found'));
                    }
                    else {
                        $this->setAction('general');
                    }
                }
                elseif ($loadPage->page_template_id == '2') {
                    $this->setAction('blog');
                }
                elseif ($loadPage->page_template_id == '3') {
                    $viewPage = $this->Pages->find('all')->contain('CustomPages')->where(['Pages.slug' => $slug])->limit(1);
                    if ($viewPage->count() == 0) {
                        throw new NotFoundException(__('Page not found'));
                    }
                    else {
                        $this->setAction('code');
                    }
                }
            }
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function child()
    {
        $slug   = $this->request->getParam('child');
        $parent = $this->request->getParam('page');
        $page   = $this->Pages->find('all')->contain('PageTemplates')->where(['slug' => $slug]);
        if ($page->count() == 1) {
            $loadPage = $page->first();
            if ($loadPage->page_template_id == '1') {
                $viewPage = $this->Pages->find('all')->contain('Generals')->where(['Pages.slug' => $slug])->limit(1);
                if ($viewPage->count() == 0) {
                    throw new NotFoundException(__('Page not found'));
                }
                else {
                    $this->setAction('general');
                }
            }
            elseif ($loadPage->page_template_id == '2') {
                $this->setAction('blog');
            }
            elseif ($loadPage->page_template_id == '3') {
                $viewPage = $this->Pages->find('all')->contain('CustomPages')->where(['Pages.slug' => $slug])->limit(1);
                if ($viewPage->count() == 0) {
                    throw new NotFoundException(__('Page not found'));
                }
                else {
                    $this->setAction('code');
                }
            }
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function general()
    {
        if (empty($this->request->getParam('child'))) {
            $slug = $this->request->getParam('page');
            $viewPage = $this->Pages->find('all')->contain('Generals')->where(['Pages.slug' => $slug, 'Pages.status' => '1'])->limit(1);
        }
        else {
            $slug     = $this->request->getParam('child');
            $parent   = $this->request->getParam('page');
            $viewPage = $this->Pages->find('all')->contain('Generals')->where(['Pages.slug' => $slug, 'Pages.status' => '1'])->limit(1);
            $viewParent = $this->Pages->find('all')->where(['Pages.slug' => $parent])->limit(1);
        }
        
        if ($viewPage->count() == 0) {
            throw new NotFoundException(__('Page not found'));
        }
        else {
            $data = [
                'pageType'        => 'general',
                'pageTitle'       => $viewPage->first()->title,
                'breadcrumb'      => 'Home::'.$viewPage->first()->title,
                'metaKeywords'    => $viewPage->first()->general->meta_keywords,
                'metaDescription' => $viewPage->first()->general->meta_description,
                'viewPage'        => $viewPage->first()
            ];

            $purpleGlobal = new PurpleProjectGlobal();
            $purpleSeo    = new PurpleProjectSeo();
            $protocol     = $purpleGlobal->protocol();

            if (!empty($this->request->getParam('child'))) {
                $data['parentPageTitle'] = $viewParent->first()->title;
                $data['breadcrumb']      = 'Home::'.$viewParent->first()->title.'::'.$viewPage->first()->title;
                $data['childPage']       = true;

                // Generate Schema.org ld+json
                $webpageSchemaOption = [
                    "title" => $viewPage->first()->title,
                    "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewParent->first()->slug.'/'.$viewPage->first()->slug
                ];

                if ($viewPage->first()->general->meta_description == NULL || $viewPage->first()->general->meta_description == '') {
                    $webpageSchemaOption['description'] = NULL;
                }
                else {
                    $webpageSchemaOption['description'] = $viewPage->first()->general->meta_description;
                }

                $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

                $data['webpageSchema'] = $webpageSchema;

                $breadcrumbList   = [
                    0 => [
                        "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewParent->first()->slug,
                        "name" => $viewParent->first()->title
                    ],
                    1 => [
                        "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewParent->first()->slug.'/'.$viewPage->first()->slug,
                        "name" => $viewPage->first()->title
                    ]
                ];
                $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

                $data['breadcrumbSchema'] = $breadcrumbSchema;
            }
            else {
                $data['childPage'] = false;

                // Generate Schema.org ld+json
                $webpageSchemaOption = [
                    "title" => $viewPage->first()->title,
                    "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewPage->first()->slug
                ];

                if ($viewPage->first()->general->meta_description == NULL || $viewPage->first()->general->meta_description == '') {
                    $webpageSchemaOption['description'] = NULL;
                }
                else {
                    $webpageSchemaOption['description'] = $viewPage->first()->general->meta_description;
                }

                $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

                $data['webpageSchema'] = $webpageSchema;

                $breadcrumbList   = [
                    0 => [
                        "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewPage->first()->slug,
                        "name" => $viewPage->first()->title
                    ]
                ];
                $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

                $data['breadcrumbSchema'] = $breadcrumbSchema;
            }

            $this->set($data);
            $this->render();
        }
    }
    public function code()
    {
        if (empty($this->request->getParam('child'))) {
            $slug     = $this->request->getParam('page');
            $viewPage = $this->Pages->find('all')->contain('CustomPages')->where(['Pages.slug' => $slug, 'Pages.status' => '1'])->limit(1);
        }
        else {
            $slug       = $this->request->getParam('child');
            $parent     = $this->request->getParam('page');
            $viewPage   = $this->Pages->find('all')->contain('CustomPages')->where(['Pages.slug' => $slug, 'Pages.status' => '1'])->limit(1);
            $viewParent = $this->Pages->find('all')->where(['Pages.slug' => $parent])->limit(1);
        }
        
        if ($viewPage->count() == 0) {
            throw new NotFoundException(__('Page not found'));
        }
        else {
            $file = WWW_ROOT . 'uploads' . DS . 'custom-pages' . DS . $viewPage->first()->custom_page->file_name;
            // $readFile  = new File(WWW_ROOT . 'uploads' . DS . 'custom-pages' . DS . $viewPage->first()->custom_page->file_name);
            // $code = $readFile->read();
            $data = [
                'pageType'        => 'custom',
                'pageTitle'       => $viewPage->first()->title,
                'metaKeywords'    => $viewPage->first()->meta_keywords,
                'metaDescription' => $viewPage->first()->meta_description,
                'breadcrumb'      => 'Home::'.$viewPage->first()->title,
                'viewPage'        => $file
            ];

            $purpleGlobal = new PurpleProjectGlobal();
            $purpleSeo    = new PurpleProjectSeo();
            $protocol     = $purpleGlobal->protocol();

            if (!empty($this->request->getParam('child'))) {
                $data['parentPageTitle'] = $viewParent->first()->title;
                $data['breadcrumb']      = 'Home::'.$viewParent->first()->title.'::'.$viewPage->first()->title;
                $data['childPage']       = true;

                // Generate Schema.org ld+json
                $webpageSchemaOption = [
                    "title" => $viewPage->first()->title,
                    "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewPage->first()->slug
                ];

                if ($viewPage->first()->meta_description == NULL || $viewPage->first()->meta_description == '') {
                    $webpageSchemaOption['description'] = NULL;
                }
                else {
                    $webpageSchemaOption['description'] = $viewPage->first()->meta_description;
                }

                $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

                $data['webpageSchema'] = $webpageSchema;

                $breadcrumbList   = [
                    0 => [
                        "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewParent->first()->slug,
                        "name" => $viewParent->first()->title
                    ],
                    1 => [
                        "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewParent->first()->slug.'/'.$viewPage->first()->slug,
                        "name" => $viewPage->first()->title
                    ]
                ];
                $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

                $data['breadcrumbSchema'] = $breadcrumbSchema;
            }
            else {
                $data['childPage'] = false;

                // Generate Schema.org ld+json
                $webpageSchemaOption = [
                    "title" => $viewPage->first()->title,
                    "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewPage->first()->slug
                ];

                if ($viewPage->first()->meta_description == NULL || $viewPage->first()->meta_description == '') {
                    $webpageSchemaOption['description'] = NULL;
                }
                else {
                    $webpageSchemaOption['description'] = $viewPage->first()->meta_description;
                }

                $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

                $data['webpageSchema'] = $webpageSchema;

                $breadcrumbList   = [
                    0 => [
                        "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewPage->first()->slug,
                        "name" => $viewPage->first()->title
                    ]
                ];
                $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

                $data['breadcrumbSchema'] = $breadcrumbSchema;
            }

            $this->set($data);
            $this->render();
        }
    }
    public function blog($paging = 1)
    {
        $this->loadComponent('Paginator');

        if (empty($this->request->getParam('child'))) {
            $slug = $this->request->getParam('page');
            $page = $this->Pages->find('all')->contain('PageTemplates')->where(['Pages.slug' => $slug]);
        }
        else {
            $slug       = $this->request->getParam('child');
            $parent     = $this->request->getParam('page');
            $page       = $this->Pages->find('all')->contain('PageTemplates')->where(['Pages.slug' => $slug]);
            $viewParent = $this->Pages->find('all')->where(['Pages.slug' => $parent])->limit(1);
        }

        $this->loadModel('Blogs');
        $this->loadModel('BlogCategories');

        $blogs = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id' => $page->first()->id, 'Blogs.status' => '1']);

        $categories = $this->BlogCategories->find('all', [
                'order' => ['BlogCategories.ordering' => 'ASC']])->where(['BlogCategories.page_id' => $page->first()->id]);

        $archives = $this->Blogs->archivesList();

        $data = [
            'pageType'        => 'blog',
            'pageTitle'       => $page->first()->title,
            'breadcrumb'      => 'Home::'.$page->first()->title,
            'metaOgType'      => 'blog',
            'postsTotal'      => $blogs->count()
        ];

        $purpleGlobal = new PurpleProjectGlobal();
        $purpleSeo    = new PurpleProjectSeo();
        $protocol     = $purpleGlobal->protocol();

        if (!empty($this->request->getParam('child'))) {
            $data['parentPageTitle'] = $viewParent->first()->title;
            $data['breadcrumb']      = 'Home::'.$viewParent->first()->title.'::'.$page->first()->title;
            $data['childPage']       = true;

            // Generate Schema.org ld+json
            $webpageSchemaOption = [
                "title" => $page->first()->title,
                "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot").$page->first()->slug
            ];

            $webpageSchemaOption['description'] = NULL;
            $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

            $data['webpageSchema'] = $webpageSchema;

            $breadcrumbList   = [
                0 => [
                    "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewParent->first()->slug,
                    "name" => $viewParent->first()->title
                ],
                1 => [
                    "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$viewParent->first()->slug.'/'.$page->first()->slug,
                    "name" => $page->first()->title
                ]
            ];
            $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

            $data['breadcrumbSchema'] = $breadcrumbSchema;
        }
        else {
            $data['childPage'] = false;

            // Generate Schema.org ld+json
            $webpageSchemaOption = [
                "title" => $page->first()->title,
                "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot").$page->first()->slug
            ];

            $webpageSchemaOption['description'] = NULL;
            $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

            $data['webpageSchema'] = $webpageSchema;
            
            $breadcrumbList   = [
                0 => [
                    "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$page->first()->slug,
                    "name" => $page->first()->title
                ]
            ];
            $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

            $data['breadcrumbSchema'] = $breadcrumbSchema;
        }

        $this->paginate = [
            'limit' => $this->Settings->settingsPostLimitPerPage(),
            'page'  => $paging
        ];
        $blogsList = $this->paginate($blogs);
        $this->set('blogs', $blogsList);
        $this->set(compact('categories'));
        $this->set(compact('archives'));
        $this->set($data);
        $this->render();
    }
    public function ajaxSendContact()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $pageContact = new PageContactForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($pageContact->execute($this->request->getData())) {
                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));
                
                if ($verifyEmail == true) {
                    $this->loadModel('Messages');

                    $message = $this->Messages->newEntity();
                    $message = $this->Messages->patchEntity($message, $this->request->getData());
                    $message->is_read = '0';
                    $message->folder  = 'inbox';
                    $message->replied = '0';

                    if ($this->Messages->save($message)) {
                        $record_id = $message->id;

                        /**
                         * Save data to Notifications Table
                         */
                        $this->loadModel('Notifications');
                        $notification = $this->Notifications->newEntity();
                        $notification->type       = 'message';
                        $notification->content    = $this->request->getData('name').' sent a message to you. Click to view the message.';
                        $notification->message_id = $record_id;

                        // Send Email to User to Notify user
                        $users     = $this->Admins->find()->where(['username <> ' => 'creatifycore'])->order(['id' => 'ASC']);
                        $totalUser = $users->count();

                        $emailStatus = [];
                        $counter = 0;
                        foreach ($users as $user) {
                            $key           = $this->Settings->settingsPublicApiKey();
                            $dashboardLink = $this->request->getData('ds');
                            $userData      = array(
                                'sitename'    => $this->Settings->settingsSiteName(),
                                'email'       => $user->email,
                                'displayName' => $user->display_name,
                                'level'       => $user->level
                            );
                            $senderData   = array(
                                'subject' => $this->request->getData('subject'),
                                'name'    => $this->request->getData('name'),
                                'email'   => $this->request->getData('email'),
                                'domain'  => $this->request->domain()
                            );
                            $notifyUser = $purpleApi->sendEmailContactMessage($key, $dashboardLink, json_encode($userData), json_encode($senderData));

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

                        if ($this->Notifications->save($notification)) {
                            $json = json_encode(['status' => 'ok', 'notification' => true, 'email' => $emailStatus, 'content' => '<strong>Success</strong> Your message has been sent. We will reply your message as soon as possible. Thank you.']);
                        }
                        else {
                            $json = json_encode(['status' => 'ok', 'email' => $emailStatus, 'content' => '<strong>Success</strong> Your message has been sent. We will reply your message as soon as possible. Thank you.']);
                        }
                    }
                    else {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
                }
            }
            else {
                $errors = $pageContact->errors();
                $purpleGlobal = new PurpleProjectGlobal();
                $generateErrorValidation = $purpleGlobal->showUserValidateErrors($errors);

                $json = json_encode(['status' => 'error', 'error' => $errors, 'validation' => $generateErrorValidation]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxVerifyForm($action, $token)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (empty($action) || empty($token)) {
            throw new NotFoundException(__('Page not found'));
        }
        else {
            $explodeAction = explode('-', $action);
            $newAction     = '';
            foreach ($explodeAction as $act) {
                $newAction .= ucwords($act);
            }

            $sitekey = $this->Settings->settingsRecaptchaSitekey();
            $secret  = $this->Settings->settingsRecaptchaSecret();

            $recaptcha = new \ReCaptcha\ReCaptcha($secret);
            $response  = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                              ->setExpectedAction($newAction)
                              ->setScoreThreshold(0.5)
                              ->verify($token, $_SERVER['REMOTE_ADDR']);
            $json = json_encode($response->toArray());
            $this->set(['json' => $json]);
        }
    }
}
