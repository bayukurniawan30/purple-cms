<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use App\Form\PostCommentForm;
use App\Form\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use Carbon\Carbon;
use EngageTheme\Functions\ThemeFunction;
use Particle\Filter\Filter;

class BlogsController extends AppController
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
            $headlessFront  = $purpleSettings->headlessFront();
            $userLoggedIn   = $purpleSettings->checkUserLoggedIn();

            if ($maintenance == 'enable' && $userLoggedIn == false) {
                return $this->redirect(
                    ['controller' => 'Maintenance', 'action' => 'index']
                );
            }
            else {
                if ($headlessFront == 'enable') {
                    return $this->redirect(
                        ['controller' => 'Headless', 'action' => 'index']
                    );
                }
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
        parent::initialize();

        $this->loadModel('Settings');
        $this->loadModel('Admins');
        $this->loadModel('Menus');
        $this->loadModel('Visitors');
        $this->loadModel('Socials');
        $this->loadModel('Pages');
        $this->loadModel('BlogCategories');
        $this->loadModel('BlogVisitors');
        $this->loadModel('Comments');
        $this->loadModel('Tags');

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
            $purpleApi = new PurpleProjectApi();
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
                    'total'   => $purpleGlobal->shortenNumber($totalAllVisitors),
                    'domain'  => $this->request->host()
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
            'siteName'           => $this->Settings->settingsSiteName(),
            'tagLine'            => $this->Settings->settingsTagLine(),
            'childPage'          => false,
            'metaKeywords'       => $this->Settings->settingsMetaKeywords(),
            'metaDescription'    => $this->Settings->settingsMetaDescription(),
            'googleAnalytics'    => $this->Settings->settingsAnalyticscode(),
            'metaOgType'         => 'blog',
            'metaImage'          => '',
            'favicon'            => $this->Settings->settingsFavicon(),
            'logo'               => $this->Settings->settingsLogo(),
            'menus'              => $this->Menus->fetchPublishedMenus(),
            'leftFooter'         => $this->Settings->settingsLeftFooter(),
            'rightFooter'        => $this->Settings->settingsRightFooter(),
            'dateFormat'         => $this->Settings->settingsDateFormat(),
            'timeFormat'         => $this->Settings->settingsTimeFormat(),
            'postsLimit'         => $this->Settings->settingsPostLimitPerPage(),
            'socialShare'        => $this->Settings->settingsSocialShare(),
            'socialTheme'        => $this->Settings->settingsSocialTheme(),
            'socialFontSize'     => $this->Settings->settingsSocialFontSize(),
            'socialLabel'        => $this->Settings->settingsSocialLabel(),
            'socialCount'        => $this->Settings->settingsSocialCount(),
            'cakeDebug'          => $cakeDebug,
            'formSecurity'       => $formSecurity,
            'sidebarSearch'      => $search,
            'ldJsonWebsite'      => $websiteSchema,
            'ldJsonOrganization' => $orgSchema,
            'recaptchaSitekey'   => $this->Settings->settingsRecaptchaSitekey(),
            'recaptchaSecret'    => $this->Settings->settingsRecaptchaSecret()
        ];
        $this->set($data);
    }
    public function detail($post)
    {
        $postComment = new PostCommentForm();

        $blogs = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins')->where(['Blogs.status' => '1', 'Blogs.slug' => $post])->limit(1);

        if ($blogs->count() > 0) {
            $blog   = $blogs->first();
            $pageID = $blog->blog_category->page_id;
            $tags   = $this->Tags->postTags($blog->id);

            $page = $this->Pages->find('all')->contain('PageTemplates')->where(['Pages.id' => $pageID]);
            if ($page->count() > 0) {
                $pageParent = $page->first()->parent;
            }
            else {
                $pageParent = NULL;
            }

            if ($page->count() == 0) {
                $pageSlug   = 'posts';
                $pageTitle  = 'Posts';
                $categories = $this->BlogCategories->find('all', [
                    'order' => ['BlogCategories.ordering' => 'ASC']])->where(['BlogCategories.page_id IS' => NULL]);
            }
            else {
                $pageSlug   = $page->first()->slug;
                $pageTitle  = $page->first()->title;
                $categories = $this->BlogCategories->find('all', [
                    'order' => ['BlogCategories.ordering' => 'ASC']])->where(['BlogCategories.page_id' => $pageID]);
            }

            $archives    = $this->Blogs->archivesList();
            $tagsSidebar = $this->Tags->tagsSidebar($this->tagsSidebarLimit);

            // Save Visitor IP and Date
            $purpleSettings = new PurpleProjectSettings();
            $timezone       = $purpleSettings->timezone();
            $visitorDate    = Carbon::now($timezone);

            $visitorIp    = $this->request->clientIp(); 
            $checkVisitor = $this->BlogVisitors->checkVisitor($visitorIp, $visitorDate, $blog->id);
            if ($checkVisitor == 0) {
                $blogVisitor = $this->BlogVisitors->newEntity();
                $blogVisitor->ip      = $visitorIp;
                $blogVisitor->created = $visitorDate;
                $blogVisitor->blog_id = $blog->id;

                $this->BlogVisitors->save($blogVisitor);
            }

            $data = [
                'pageType'         => 'blog',
                'pageTitle'        => $blog->title,
                'pageSlug'         => $pageSlug,
                'breadcrumb'       => 'Home::'.$pageTitle,
                'blog'             => $blog,
                'blogTags'         => $tags,
                'metaKeywords'     => $blog->meta_keywords,
                'metaDescription'  => $blog->meta_description,
                'metaImage'        => $blog->featured,
                'postComment'      => $postComment,
                'totalComments'    => $this->Comments->publishedComments($blog->id, 'countall'),
                'fetchComments'    => $this->Comments->publishedComments($blog->id, 'fetch'),
                'totalVisitors'    => $this->BlogVisitors->totalVisitors($blog->id),
            ];

            if ($pageParent == NULL || $pageParent == '0') {
                $data['childPage'] = false;
            }
            else {
                $viewParent = $this->Pages->find('all')->where(['Pages.id' => $pageParent])->limit(1);
                $data['breadcrumb'] = 'Home::'.$viewParent->first()->title.'::'.$pageTitle;
            }

            // Generate BreadcrumbList reSchema.org ld+json
            $purpleGlobal = new PurpleProjectGlobal();
            $purpleSeo    = new PurpleProjectSeo();
            $protocol     = $purpleGlobal->protocol();
            
            $blogYear  = date('Y', strtotime($blog->created));
            $blogMonth = date('m', strtotime($blog->created));
            $blogDate  = date('d', strtotime($blog->created));

            $breadcrumbList   = [
                0 => [
                    "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").$blogYear.'/'.$blogMonth.'/'.$blogDate.'/'.$blog->slug,
                    "name" => $blog->title
                ]
            ];
            $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

            $data['breadcrumbSchema'] = $breadcrumbSchema;

            // Generate Article reSchema.org ld+json
            $timezoneSchema = $purpleSettings->timezone('time');
            if ($blog->modified == NULL) {
                $modified = $blog->created->format('Y-m-d') . 'T' . $blog->created->format('H:i:s') . $timezoneSchema;
            }
            else {
                $modified = $blog->modified->format('Y-m-d') . 'T' . $blog->modified->format('H:i:s') . $timezoneSchema;
            }

            if ($blog->featured == NULL || $blog->featured == '') {
                $blogImage = NULL;
            }
            else {
                if (strpos($blog->featured, ',') !== false) {
                    $explodeBlogImage = explode(',', $blog->featured);
                    $blogImage = $explodeBlogImage[0];
                }
                else {
                    $blogImage = $blog->featured;
                }

                list($widthImage, $heightImage) = getimagesize(WWW_ROOT . 'uploads' . DS . 'images' . DS . 'original' . DS . $blogImage);
            }

            $articleJsonLd = [
                "author"        => $blog->admin->display_name,
                "datePublished" => $blog->created->format('Y-m-d') . 'T' . $blog->created->format('H:i:s') . $timezoneSchema,
                "datemodified"  => $modified,
                "headline"      => $blog->title,
                "articleBody"   => strip_tags($blog->content)
            ];

            if ($blogImage != NULL) {
                $articleJsonLd["image"] = [
                    "@type"  => "imageObject",
                    "url"    => $protocol.$this->request->host().$this->request->getAttribute("webroot").'uploads/images/original/'.$blogImage,
                    "height" => $heightImage,
                    "width"  => $widthImage
                ];
            }
            else {
                $articleJsonLd["image"] = NULL;
            }

            $articleSchema = $purpleSeo->schemaLdJson('article', $articleJsonLd);
            $data['articleSchema'] = $articleSchema;

            $this->set(compact('categories'));
            $this->set(compact('archives'));
            $this->set(compact('tagsSidebar'));
            $this->set($data);
            $this->render();
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function postsInCategory($category, $paging = 1)
    {
        $this->loadComponent('Paginator');

        $categories = $this->BlogCategories->find()->where(['slug' => $category]);
        if ($categories->count() > 0) {
	        $blogs = $this->Blogs->find('all', [
	                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins')->where(['Blogs.status' => '1', 'BlogCategories.slug' => $category]);

			$category = $categories->first();
			$pageID   = $category->page_id;

            if ($pageID == NULL) {
                $total = 0;
                $pageParent = NULL;
            }
            else {
                $page  = $this->Pages->find('all')->contain('PageTemplates')->where(['Pages.id' => $pageID]);
                $total = $page->count();
                $pageParent = $page->first()->parent;
            }

            if ($total == 0) {
                $pageSlug   = 'posts';
                $pageTitle  = 'Posts';
                $categories = $this->BlogCategories->find('all', [
                    'order' => ['BlogCategories.ordering' => 'ASC']])->where(['BlogCategories.page_id IS' => NULL]);
            }
            else {
                $pageSlug   = $page->first()->slug;
                $pageTitle  = $page->first()->title;
                $categories = $this->BlogCategories->find('all', [
                    'order' => ['BlogCategories.ordering' => 'ASC']])->where(['BlogCategories.page_id' => $pageID]);
            }

            $archives    = $this->Blogs->archivesList();
            $tagsSidebar = $this->Tags->tagsSidebar($this->tagsSidebarLimit);

            $data = [
                'pageType'   => 'blog',
                'pageTitle'  => $category->name.' - '.$pageTitle,
                'pageSlug'   => $pageSlug,
                'breadcrumb' => 'Home::'.$pageTitle.'::'.$category->name,
	            'postsTotal' => $blogs->count()
            ];

            if ($pageParent == NULL || $pageParent == '0') {
                $data['childPage'] = false;
            }
            else {
                $viewParent = $this->Pages->find('all')->where(['Pages.id' => $pageParent])->limit(1);
                $data['breadcrumb'] = 'Home::'.$viewParent->first()->title.'::'.$pageTitle.'::'.$category->name;
            }

            $this->paginate = [
	            'limit' => $this->Settings->settingsPostLimitPerPage(),
	            'page'  => $paging
	        ];
            $blogsList = $this->paginate($blogs);

            // Generate Schema.org ld+json
            $purpleGlobal = new PurpleProjectGlobal();
            $purpleSeo    = new PurpleProjectSeo();
            $protocol     = $purpleGlobal->protocol();

            $webpageSchemaOption = [
                "title" => $category->name.' - '.$pageTitle,
                "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot")."search"
            ];

            $webpageSchemaOption['description'] = NULL;
            $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

            $data['webpageSchema'] = $webpageSchema;
            
            $breadcrumbList   = [
                0 => [
                    "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").'posts/'.$category->slug,
                    "name" => $category->name
                ]
            ];
            $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

            $data['breadcrumbSchema'] = $breadcrumbSchema;
            
	        $this->set('blogs', $blogsList);
            $this->set(compact('categories'));
            $this->set(compact('archives'));
            $this->set(compact('tagsSidebar'));
            $this->set($data);
            $this->render();
	    }
	    else {
            throw new NotFoundException(__('Page not found'));
	    }
    }
    public function tag($tag, $paging = 1)
    {
        $this->loadComponent('Paginator');

        $tag = $this->Tags->find()->where(['slug' => $tag]);

        if ($tag->count() > 0) {
            $blogs = $this->Blogs->taggedPosts($tag->first()->id);

            $archives    = $this->Blogs->archivesList();
            $tagsSidebar = $this->Tags->tagsSidebar($this->tagsSidebarLimit);

            $data = [
                'pageType'   => 'blog',
                'pageTitle'  => 'Tag - '.$tag->first()->title,
                'pageSlug'   => 'tag',
                'breadcrumb' => 'Home::Tag::'.$tag->first()->title,
                'postsTotal' => $blogs->count()
            ];

            $this->paginate = [
                'limit' => $this->Settings->settingsPostLimitPerPage(),
                'page'  => $paging
            ];
            $blogsList = $this->paginate($blogs);

            // Generate Schema.org ld+json
            $purpleGlobal = new PurpleProjectGlobal();
            $purpleSeo    = new PurpleProjectSeo();
            $protocol     = $purpleGlobal->protocol();

            $webpageSchemaOption = [
                "title" => 'Tag - '.$tag->first()->title,
                "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot")."search"
            ];

            $webpageSchemaOption['description'] = NULL;
            $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

            $data['webpageSchema'] = $webpageSchema;
            
            $breadcrumbList   = [
                0 => [
                    "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").'tag/'.$tag->first()->slug,
                    "name" => $tag->first()->title
                ]
            ];
            $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

            $data['breadcrumbSchema'] = $breadcrumbSchema;

            $this->set('blogs', $blogsList);
            $this->set(compact('archives'));
            $this->set(compact('tagsSidebar'));
            $this->set($data);
            $this->render();
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function archives($year, $month, $paging = 1)
    {
    	$this->loadComponent('Paginator');

        $monthFormat = date('F', strtotime($year.'-'.$month.'-14'));

        $blogs     = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins');
        $dateYear  = $blogs->func()->extract('YEAR', 'Blogs.created');
        $dateMonth = $blogs->func()->extract('MONTH', 'Blogs.created');
        $blogs->select([
            'yearCreated'  => $dateYear,
            'monthCreated' => $dateMonth
        ])
        ->select($this->Blogs)
        ->select($this->BlogCategories)
        ->select($this->Admins)
        ->having(['Blogs.status' => '1', 'yearCreated' => $year, 'monthCreated' => $month]);

        if ($blogs->count() > 0) {
            $archives    = $this->Blogs->archivesList();
            $tagsSidebar = $this->Tags->tagsSidebar($this->tagsSidebarLimit);

            $data = [
                'pageType'   => 'blog',
                'pageTitle'  => 'Archives '.$monthFormat.' '.$year,
                'breadcrumb' => 'Home::Archives::'.$monthFormat.' '.$year,
	            'postsTotal' => $blogs->count()
            ];

            $this->paginate = [
	            'limit' => $this->Settings->settingsPostLimitPerPage(),
	            'page'  => $paging
	        ];
            $blogsList = $this->paginate($blogs);
            
            // Generate Schema.org ld+json
            $purpleGlobal = new PurpleProjectGlobal();
            $purpleSeo    = new PurpleProjectSeo();
            $protocol     = $purpleGlobal->protocol();

            $webpageSchemaOption = [
                "title" => 'Archives '.$monthFormat.' '.$year,
                "url"   => $protocol.$this->request->host().$this->request->getAttribute("webroot")."search"
            ];

            $webpageSchemaOption['description'] = NULL;
            $webpageSchema = $purpleSeo->schemaLdJson('webpage', $webpageSchemaOption);

            $data['webpageSchema'] = $webpageSchema;
            
            $breadcrumbList   = [
                0 => [
                    "@id"  => $protocol.$this->request->host().$this->request->getAttribute("webroot").'archives/'.$year.'/'.$month,
                    "name" => 'Archives '.$monthFormat.' '.$year
                ]
            ];
            $breadcrumbSchema = $purpleSeo->schemaLdJson('breadcrumblist', $breadcrumbList);

            $data['breadcrumbSchema'] = $breadcrumbSchema;

	        $this->set('blogs', $blogsList);
            $this->set(compact('archives'));
            $this->set(compact('tagsSidebar'));
            $this->set($data);
            $this->render();
	    }
	    else {
            throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxSendComment()
    {
    	$this->viewBuilder()->enableAutoLayout(false);

		$postComment = new PostCommentForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($postComment->execute($this->request->getData())) {
                // Sanitize user input
                $filter = new Filter();
                if ($this->request->getEnv('HTTP_HOST') == 'localhost') {
                    $filter->values(['status'])->defaults('success');
                    $filter->values(['score'])->defaults('0.9');
                }
                $filter->value('content')->trim()->stripHtml()->replace("\n", '<br>');
				$filter->values(['name', 'email', 'ds', 'status', 'score'])->trim()->stripHtml();
				$filter->values(['blog_id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
                $requestArray = json_decode(json_encode($filterResult), TRUE);

                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($requestData->email);

                if ($verifyEmail == true) {
                    $purpleGlobal = new PurpleProjectGlobal();
                    if ($purpleGlobal->isRecaptchaPass($requestData->status, $requestData->score) == true) {
                        $comment = $this->Comments->newEntity();
                        $comment = $this->Comments->patchEntity($comment, $requestArray);
                        $comment->status = '0';
                        $comment->reply  = '0';

        				if ($this->Comments->save($comment)) {
                            $recordId = $comment->id;

                            // Tell system for new event
                            // Notification Event
                            $notificationEvent = new Event('Model.Notification.afterSaveComment', $this, ['data' => ['blog_id' => $requestData->blog_id, 'comment_id' => $recordId, 'name' => $requestData->name]]);
                            $this->getEventManager()->dispatch($notificationEvent);
                            
                            // Send Email Event
                            $commentEvent = new Event('Model.Blog.afterSentComment', $this, ['data' => ['blog_id' => $requestData->blog_id, 'link' => $requestData->ds, 'name' => $requestData->name, 'email' => $requestData->email, 'domain' => $this->request->host()]]);
                            $this->getEventManager()->dispatch($commentEvent);

                            if ($notificationEvent->getResult()) {
                                $json = json_encode(['status' => 'ok', 'notification' => $notificationEvent->getResult(), 'email' => $commentEvent->getResult(), 'content' => '<strong>Success</strong> Your comment has been sent. We need to review your comment before publish it. Thank you.']);
                            }
                            else {
                                $json = json_encode(['status' => 'ok', 'email' => $commentEvent->getResult(), 'content' => '<strong>Success</strong> Your comment has been sent. We need to review your comment before publish it. Thank you.']);
                            }
                        }
                        else {
                            $json = json_encode(['status' => 'error', 'error' => "Can't send your comment. Please try again."]);
                        }
                    }
                    else {
                        $json = json_encode(['status' => 'error', 'error' => "Can't prove you are a human. Please try again."]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
                }
			}
			else {
				$errors = $postComment->getErrors();
                $purpleGlobal = new PurpleProjectGlobal();
                $generateErrorValidation = $purpleGlobal->showUserValidateErrors($errors);

                $json = json_encode(['status' => 'error', 'error' => $errors, 'validation' => $generateErrorValidation]);
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