<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use App\Form\PostCommentForm;
use App\Form\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use Carbon\Carbon;
use EngageTheme\Functions\ThemeFunction;

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
    }
    public function beforeRender(\Cake\Event\Event $event)
    {
        $this->viewBuilder()->setTheme('EngageTheme');
        $this->viewBuilder()->setLayout('EngageTheme.default');
    }
    public function initialize()
    {
        $this->loadModel('Settings');
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

        $themeFunction = new ThemeFunction(); 
        $this->set('themeFunction', $themeFunction);

        $socials = $this->Socials->find('all')->order(['ordering' => 'ASC']);
        $this->set(compact('socials'));

        $data = [
            'siteName'        => $this->Settings->settingsSiteName(),
            'tagLine'         => $this->Settings->settingsTagLine(),
            'metaKeywords'    => $this->Settings->settingsMetaKeywords(),
            'metaDescription' => $this->Settings->settingsMetaDescription(),
            'metaOgType'      => 'blog',
            'metaImage'       => '',
            'favicon'         => $this->Settings->settingsFavicon(),
            'logo'            => $this->Settings->settingsLogo(),
            'menus'           => $this->Menus->fetchPublishedMenus(),
            'leftFooter'      => $this->Settings->settingsLeftFooter(),
            'rightFooter'     => $this->Settings->settingsRightFooter(),
            'dateFormat'      => $this->Settings->settingsDateFormat(),
            'timeFormat'      => $this->Settings->settingsTimeFormat(),
            'postsLimit'      => $this->Settings->settingsPostLimitPerPage(),
            'socialShare'     => $this->Settings->settingsSocialShare(),
            'socialTheme'     => $this->Settings->settingsSocialTheme(),
            'socialFontSize'  => $this->Settings->settingsSocialFontSize(),
            'socialLabel'     => $this->Settings->settingsSocialLabel(),
            'socialCount'     => $this->Settings->settingsSocialCount(),
            'cakeDebug'       => $cakeDebug,
            'formSecurity'    => $formSecurity,
            'sidebarSearch'   => $search
        ];
        $this->set($data);
    }
    public function detail($post)
    {
        $this->loadModel('Pages');
        $this->loadModel('BlogCategories');
        $this->loadModel('BlogVisitors');
        $this->loadModel('Comments');
        $this->loadModel('Tags');
        $this->loadModel('Settings');

        $postComment = new PostCommentForm();

        $blogs = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins')->where(['Blogs.status' => '1', 'Blogs.slug' => $post])->limit(1);

        if ($blogs->count() > 0) {
            $blog   = $blogs->first();
            $pageID = $blog->blog_category->page_id;
            $tags   = $this->Tags->postTags($blog->id);

            $page = $this->Pages->find('all')->contain('PageTemplates')->where(['Pages.id' => $pageID]);
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
                'recaptchaSitekey' => $this->Settings->settingsRecaptchaSitekey(),
                'recaptchaSecret'  => $this->Settings->settingsRecaptchaSecret()
            ];

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
    	$this->loadModel('Pages');
        $this->loadModel('BlogCategories');
        $this->loadModel('Tags');
        $this->loadModel('Settings');

        $categories = $this->BlogCategories->find()->where(['slug' => $category]);
        if ($categories->count() > 0) {
	        $blogs = $this->Blogs->find('all', [
	                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins')->where(['Blogs.status' => '1', 'BlogCategories.slug' => $category]);

			$category = $categories->first();
			$pageID   = $category->page_id;

            if ($pageID == NULL) {
                $total = 0;
            }
            else {
                $page  = $this->Pages->find('all')->contain('PageTemplates')->where(['Pages.id' => $pageID]);
                $total = $page->count();
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

            $this->paginate = [
	            'limit' => $this->Settings->settingsPostLimitPerPage(),
	            'page'  => $paging
	        ];
	        $blogsList = $this->paginate($blogs);
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
        $this->loadModel('Tags');
        $this->loadModel('Settings');

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
    	$this->loadModel('Pages');
        $this->loadModel('Tags');
        $this->loadModel('Settings');

        $monthFormat = date('F', strtotime($year.'-'.$month.'-14'));

        $blogs = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins')->where(['Blogs.status' => '1', 'YEAR(Blogs.created)' => $year, 'MONTH(Blogs.created)' => $month]);
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
                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                if ($verifyEmail == true) {
                    $purpleGlobal = new PurpleProjectGlobal();
                    if ($purpleGlobal->isRecaptchaPass($this->request->getData('status'), $this->request->getData('score')) == true) {
                        $this->loadModel('Blogs');
                        $this->loadModel('Admins');
        		    	$this->loadModel('Comments');
                        $this->loadModel('Settings');

                        $comment = $this->Comments->newEntity();
                        $comment = $this->Comments->patchEntity($comment, $this->request->getData());
                        $comment->status = '0';
                        $comment->reply  = '0';

        				if ($this->Comments->save($comment)) {
                            $record_id = $comment->id;

                            /**
                             * Save data to Notifications Table
                             */
                            $this->loadModel('Notifications');
                            $notification = $this->Notifications->newEntity();
                            $notification->type       = 'comment';
                            $notification->content    = $this->request->getData('name').' sent a comment to your post. Click to view the comment.';
                            $notification->comment_id = $record_id;
                            $notification->blog_id    = $this->request->getData('blog_id');

                            // Send Email to User to Notify author
                            $blog   = $this->Blogs->get($this->request->getData('blog_id'));
                            $author = $this->Admins->get($blog->admin_id);
                            $key    = $this->Settings->settingsPublicApiKey();
                            $dashboardLink = $this->request->getData('ds');
                            $userData      = array(
                                'sitename'    => $this->Settings->settingsSiteName(),
                                'email'       => $author->email,
                                'displayName' => $author->display_name,
                                'level'       => $author->level
                            );
                            $post          = $blog->title;
                            $commentData   = array(
                                'name'   => $this->request->getData('name'),
                                'email'  => $this->request->getData('email'),
                                'blogId' => $this->request->getData('blog_id'),
                                'domain' => $this->request->domain()
                            );
                            $notifyUser = $purpleApi->sendEmailPostComment($key, $dashboardLink, json_encode($userData), $post, json_encode($commentData));

                            if ($notifyUser == true) {
                                $emailNotification = true;
                            }
                            else {
                                $emailNotification = false;
                            }

                            if ($this->Notifications->save($notification)) {
                                $json = json_encode(['status' => 'ok', 'notification' => true, 'email' => [$author->email => $emailNotification], 'content' => '<strong>Success</strong> Your comment has been sent. We need to review your comment before publish it. Thank you.']);
                            }
                            else {
                                $json = json_encode(['status' => 'ok', 'email' => [$author->email => $emailNotification], 'content' => '<strong>Success</strong> Your comment has been sent. We need to review your comment before publish it. Thank you.']);
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
				$errors = $postComment->errors();
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
}