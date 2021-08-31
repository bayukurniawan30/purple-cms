<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\BlogAddForm;
use App\Form\Purple\BlogEditForm;
use App\Form\Purple\BlogDeleteForm;
use App\Form\Purple\BlogCategoryAddForm;
use App\Form\Purple\SearchForm;
use Cake\Utility\Text;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;
use Particle\Filter\Filter;

class BlogsController extends AppController
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
				['_name' => 'adminLogin']
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

		// Set layout
		$this->viewBuilder()->setLayout('dashboard');

		// Load other models
		$this->loadModel('Admins');
		$this->loadModel('BlogCategories');
		$this->loadModel('Tags');
		$this->loadModel('BlogsTags');
		$this->loadModel('BlogVisitors');
		$this->loadModel('Medias');
		$this->loadModel('Settings');

		// Check debug is on or off
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
		$medias          = $this->Medias->find('all', [
			'order' => ['Medias.id' => 'DESC']])->contain('Admins');

		$rowCount = $queryAdmin->count();
		if ($rowCount > 0) {
			$adminData = $queryAdmin->first();

			// Dashboard search form
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
				'title'              => 'Posts | Purple CMS',
				'pageTitle'          => 'Posts',
				'pageTitleIcon'      => 'mdi-lead-pencil',
				'pageBreadcrumb'     => 'Posts',
				'appearanceFavicon'  => $queryFavicon,
				'settingsDateFormat' => $queryDateFormat->value,
				'settingsTimeFormat' => $queryTimeFormat->value,
				'mediaImageTotal'    => $medias->count(),
				'mediaImageLimit'    => $this->imagesLimit
			];
			$this->set($data);

			$this->paginate = [
				'limit' => $this->imagesLimit,
				'page'  => 1
			];
			$browseMedias = $this->paginate($medias);
			$this->set('browseMedias', $browseMedias);
		}
		else {
			return $this->redirect(
				['controller' => 'Authenticate', 'action' => 'login']
			);
		}
	}
	public function index() 
	{
		$blogDelete = new BlogDeleteForm();

		if ($this->request->getParam('id') == NULL) {
            $blogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.page_id IS' => NULL])->order(['BlogCategories.ordering' => 'ASC']);
	        $blogs 			= $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id IS' => NULL]);
        }
        else {
            $blogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.page_id' => $this->request->getParam('id')])->order(['BlogCategories.ordering' => 'ASC']);
        	$blogs 			= $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id' => $this->request->getParam('id')]);
        }

        $this->set(compact('blogCategories'));
        $this->set(compact('blogs'));
        $data = [
            'blogDelete' => $blogDelete,
        ];
        $this->set($data);
	}
	public function add()
	{
		$blogAdd         = new BlogAddForm();
		$blogCategoryAdd = new BlogCategoryAddForm();
			
		$blogCategoriesArray = $this->BlogCategories->find('list')->select(['id','name'])->order(['id' => 'ASC'])->toArray();
    	$this->set(compact('blogCategoriesArray'));

    	$tagsArray = $this->Tags->find('list')->select(['title'])->order(['id' => 'ASC'])->toArray();
    	$this->set(compact('tagsArray'));

		$data = [
			'pageBreadcrumb'  => 'Posts::Add',
			'blogAdd'         => $blogAdd,
			'blogCategoryAdd' => $blogCategoryAdd,
    	];

    	$this->set($data);
	}
	public function edit()
	{
		$blogEdit        = new BlogEditForm();
		$blogCategoryAdd = new BlogCategoryAddForm();
			
		$blogCategoriesArray = $this->BlogCategories->find('list')->select(['id','name'])->order(['id' => 'ASC'])->toArray();
    	$this->set(compact('blogCategoriesArray'));

    	$tagsArray = $this->Tags->find('list')->select(['title'])->order(['id' => 'ASC'])->toArray();
    	$this->set(compact('tagsArray'));

        $query = $this->Blogs->find('all')->where(['Blogs.id' => $this->request->getParam('blogid')]);
        $tags  = $this->Tags->postTags($this->request->getParam('blogid'));

        if ($query->count() == 1) {
        	$blogData = $query->first();

        	$data = [
				'pageBreadcrumb'      => 'Posts::Edit',
				'blogEdit'            => $blogEdit,
				'blogData'            => $blogData,
				'initialTags'         => $tags,
				'blogCategoryAdd'     => $blogCategoryAdd,
				'visitorsDays'        => $this->BlogVisitors->lastTwoWeeksVisitors(),
				'totalVisitors2Weeks' => $this->BlogVisitors->lastTwoWeeksTotalVisitors($blogData->id),
				'totalVisitors'       => $this->BlogVisitors->totalVisitors($blogData->id),
			];

			$this->set($data);
        }
        else {
        	$this->setAction('index');
        }
	}
	public function filterCategory() {
		$category = trim($this->request->getParam('category'));

		$blogDelete = new BlogDeleteForm();

		if ($this->request->getParam('id') == NULL) {
            $blogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.page_id IS' => NULL])->order(['BlogCategories.ordering' => 'ASC']);
	        $blogs 			= $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id IS' => NULL, 'BlogCategories.slug' => $category]);
        }
        else {
            $blogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.page_id' => $this->request->getParam('id')])->order(['BlogCategories.ordering' => 'ASC']);
        	$blogs 			= $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id' => $this->request->getParam('id'), 'BlogCategories.slug' => $category]);
		}
		
		$selectedBlogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.slug' => $category])->first();

        $this->set(compact('blogCategories'));
        $this->set(compact('blogs'));
        $data = [
			'blogDelete'             => $blogDelete,
			'selectedBlogCategories' => $selectedBlogCategories
        ];
        $this->set($data);
	}
	public function ajaxAdd()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$blogAdd = new BlogAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($blogAdd->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['title', 'featured', 'social_share', 'comment', 'meta_keywords', 'meta_description'])->trim()->stripHtml();
				$filter->values(['status', 'blog_category_id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $slug = Text::slug(strtolower($requestData->title));
                $findDuplicate = $this->Blogs->find()->where(['slug' => $slug]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another title."]);
                }
                else {
					$blog = $this->Blogs->newEntity();
	                $blog = $this->Blogs->patchEntity($blog, $requestArray);
	                if (empty($this->request->getData('featured'))) {
		                $blog->blog_type_id = '1';
	                }
	                else {
		                $blog->blog_type_id = '2';
	                }
	                $blog->admin_id = $sessionID;

					if ($this->Blogs->save($blog)) {
						$recordId = $blog->id;

						// Save Tags
		                if(!empty($this->request->getData('tags'))) {
		                	$explodeTags = explode(',', $this->request->getData('tags'));
		                	foreach ($explodeTags as $addedTag) {
								$checkTag       = $this->Tags->checkExists($addedTag);
		                		
		                		if ($checkTag == false) {
		                			$tag = $this->Tags->newEntity();
		                			$tag->title = $addedTag;
		                			$this->Tags->save($tag);
									$tag_id = $tag->id;

									$checkBlogsTags = $this->BlogsTags->checkExists($recordId, $tag_id);

			                		if ($checkBlogsTags == false) {
										$blogsTags = $this->BlogsTags->newEntity();
			                			$blogsTags->blog_id = $recordId;
			                			$blogsTags->tag_id  = $tag_id;
			                			$this->BlogsTags->save($blogsTags);
			                		}
		                		}
		                	}
		                }

						$blog = $this->Blogs->get($recordId);

						// Tell system for new event
						$event = new Event('Model.Blog.afterSave', $this, ['blog' => $blog, 'admin' => $admin, 'save' => 'new']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

						$this->Flash->set($blog->title . ' has been added.', [
							'element' => 'Flash/Purple/success'
						]);
	                }
	                else {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
	                }
	            }
			}
			else {
				$errors = $blogAdd->getErrors();
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
	public function ajaxUpdate()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$blogEdit = new BlogEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($blogEdit->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['title', 'featured', 'social_share', 'comment', 'meta_keywords', 'meta_description'])->trim()->stripHtml();
				$filter->values(['id', 'status', 'blog_category_id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);
				$requestArray = json_decode(json_encode($filterResult), TRUE);

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $slug = Text::slug(strtolower($requestData->title));
                $findDuplicate = $this->Blogs->find()->where(['slug' => $slug, 'id <>' => $requestData->id]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another title."]);
                }
                else {
	                $blog = $this->Blogs->get($requestData->id);
	                if (empty($this->request->getData('featured'))) {
		                $blog->blog_type_id = '1';
	                }
	                else {
		                $blog->blog_type_id = '2';
	                }
	                $blog->admin_id = $sessionID;

					$this->Blogs->patchEntity($blog, $requestArray);

					if ($this->Blogs->save($blog)) {
						$recordId = $blog->id;

						// Save Tags
		                if(!empty($this->request->getData('tags'))) {
		                	$explodeTags = explode(',', $this->request->getData('tags'));
		                	$tagsSlug = [];
		                	foreach ($explodeTags as $addedTag) {
								$tagsSlug[] = Text::slug(strtolower($addedTag));
								$checkTag = $this->Tags->checkExists($addedTag);
		                		
		                		if ($checkTag == false) {
		                			$tag = $this->Tags->newEntity();
		                			$tag->title = $addedTag;
		                			$this->Tags->save($tag);
									$tag_id = $tag->id;

									$checkBlogsTags = $this->BlogsTags->checkExists($recordId, $tag_id);

			                		if ($checkBlogsTags == false) {
										$blogsTags = $this->BlogsTags->newEntity();
			                			$blogsTags->blog_id = $recordId;
			                			$blogsTags->tag_id  = $tag_id;
			                			$this->BlogsTags->save($blogsTags);
			                		}
		                		}
		                	}
						}
						
						// Tell system for new event
						$event = new Event('Model.Blog.afterSave', $this, ['blog' => $blog, 'admin' => $admin, 'save' => 'update']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

						$this->Flash->set($blog->title . ' has been updated.', [
							'element' => 'Flash/Purple/success'
						]);
	                }
	                else {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
	                }
	            }
			}
			else {
				$errors = $blogEdit->getErrors();
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
	public function ajaxDelete()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $blogDelete = new BlogDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($blogDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$blog  = $this->Blogs->get($requestData->id);
				$title = $blog->title;

				$result = $this->Blogs->delete($blog);

                if ($result) {
					// Tell system for new event
					$event = new Event('Model.Blog.afterDelete', $this, ['blog' => $blog, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set($title . ' has been deleted.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $blogDelete->getErrors();
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
    public function ajaxRemoveTag()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
			// Sanitize user input
			$filter = new Filter();
			$filter->values(['id'])->int();
			$filter->values(['slug'])->trim()->stripHtml();
			$filterResult = $filter->filter($this->request->getData());
			$requestData  = json_decode(json_encode($filterResult), FALSE);

			$blogId  = $requestData->id;
			$tagSlug = Text::slug(strtolower($requestData->slug));

			$findTag = $this->Tags->find()->where(['slug' => $tagSlug])->limit(1);

			if ($findTag->count() > 0) {
				$blogTag  = $this->BlogsTags->find()->where(['tag_id' => $findTag->first()->id, 'blog_id' => $blogId]);
				if ($blogTag->count() > 0) {
					$record = $blogTag->first();
					$result = $this->BlogsTags->delete($record);

	                if ($result) {
                        $json = json_encode(['status' => 'ok']);
	                }
	                else {
		                $json = json_encode(['status' => 'error']);
	                }
				}
				else {
	                $json = json_encode(['status' => 'notfound']);
				}
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