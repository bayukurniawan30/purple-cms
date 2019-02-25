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
use Cake\I18n\Time;
use Cake\Log\Log;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;

class BlogsController extends AppController
{
    public $imagesLimit = 30;
    
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
        $this->loadComponent('RequestHandler');
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		if ($this->request->getEnv('HTTP_HOST') != $sessionHost || !$session->check('Admin.id')) {
			return $this->redirect(
	            ['controller' => 'Authenticate', 'action' => 'login']
	        );
		}
		else {
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

			$queryAdmin      = $this->Admins->find()->where(['id' => $sessionID, 'password' => $sessionPassword])->limit(1);
			$queryFavicon    = $this->Settings->find()->where(['name' => 'favicon'])->first();
			$queryDateFormat = $this->Settings->find()->where(['name' => 'dateformat'])->first();
			$queryTimeFormat = $this->Settings->find()->where(['name' => 'timeformat'])->first();
            $medias       = $this->Medias->find('all', [
                'order' => ['Medias.id' => 'DESC']])->contain('Admins');

			$rowCount = $queryAdmin->count();
			if ($rowCount > 0) {
				$adminData = $queryAdmin->first();

                $dashboardSearch = new SearchForm();

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
	}
	public function index() 
	{
		$blogDelete = new BlogDeleteForm();

		if ($this->request->getParam('id') == NULL) {
	        $blogs = $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id IS' => NULL]);
        }
        else {
        	$blogs = $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id' => $this->request->getParam('id')]);
        }

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
			
		$this->loadModel('BlogCategories');
		$this->loadModel('Tags');

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
		$blogEdit = new BlogEditForm();
		$blogCategoryAdd = new BlogCategoryAddForm();
			
		$this->loadModel('BlogCategories');
		$this->loadModel('BlogVisitors');
		$this->loadModel('Tags');

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
	public function ajaxAdd()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$blogAdd = new BlogAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($blogAdd->execute($this->request->getData())) {
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $slug = Text::slug(strtolower($this->request->getData('title')));
                $findDuplicate = $this->Blogs->find()->where(['slug' => $slug]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another title."]);
                }
                else {
					$blog = $this->Blogs->newEntity();
	                $blog = $this->Blogs->patchEntity($blog, $this->request->getData());
	                if (empty($this->request->getData('featured'))) {
		                $blog->blog_type_id = '1';
	                }
	                else {
		                $blog->blog_type_id = '2';
	                }
	                $blog->admin_id = $sessionID;

					if ($this->Blogs->save($blog)) {
						$record_id = $blog->id;

						// Save Tags
		                if(!empty($this->request->getData('tags'))) {
							$this->loadModel('Tags');
							$this->loadModel('BlogsTags');
		                	$explodeTags = explode(',', $this->request->getData('tags'));
		                	foreach ($explodeTags as $addedTag) {
								$checkTag       = $this->Tags->checkExists($addedTag);
		                		
		                		if ($checkTag == false) {
		                			$tag = $this->Tags->newEntity();
		                			$tag->title = $addedTag;
		                			$this->Tags->save($tag);
									$tag_id = $tag->id;

									$checkBlogsTags = $this->BlogsTags->checkExists($record_id, $tag_id);

			                		if ($checkBlogsTags == false) {
										$blogsTags = $this->BlogsTags->newEntity();
			                			$blogsTags->blog_id = $record_id;
			                			$blogsTags->tag_id  = $tag_id;
			                			$this->BlogsTags->save($blogsTags);
			                		}
		                		}
		                	}
		                }

						$blog   = $this->Blogs->get($record_id);

						/**
						 * Save user activity to histories table
						 * array $options => title, detail, admin_id
						 */

						$options = [
							'title'    => 'Addition of New Post',
							'detail'   => ' add '.$blog->title.' as a new post.',
							'admin_id' => $sessionID
						];

						$this->loadModel('Histories');
	                    $saveActivity   = $this->Histories->saveActivity($options);

						if ($saveActivity == true) {
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
			else {
				$errors = $blogAdd->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
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

		$blogEdit = new BlogEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($blogEdit->execute($this->request->getData())) {
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $slug = Text::slug(strtolower($this->request->getData('title')));
                $findDuplicate = $this->Blogs->find()->where(['slug' => $slug, 'id <>' => $this->request->getData('id')]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another title."]);
                }
                else {
	                $blog = $this->Blogs->get($this->request->getData('id'));
	                if (empty($this->request->getData('featured'))) {
		                $blog->blog_type_id = '1';
	                }
	                else {
		                $blog->blog_type_id = '2';
	                }
	                $blog->admin_id = $sessionID;

					$this->Blogs->patchEntity($blog, $this->request->getData());

					if ($this->Blogs->save($blog)) {
						$record_id = $blog->id;

						// Save Tags
		                if(!empty($this->request->getData('tags'))) {
							$this->loadModel('Tags');
							$this->loadModel('BlogsTags');
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

									$checkBlogsTags = $this->BlogsTags->checkExists($record_id, $tag_id);

			                		if ($checkBlogsTags == false) {
										$blogsTags = $this->BlogsTags->newEntity();
			                			$blogsTags->blog_id = $record_id;
			                			$blogsTags->tag_id  = $tag_id;
			                			$this->BlogsTags->save($blogsTags);
			                		}
		                		}
		                	}
		                }

						/**
						 * Save user activity to histories table
						 * array $options => title, detail, admin_id
						 */

						$options = [
							'title'    => 'Data Change of a Post',
							'detail'   => ' update '.$blog->title.' data from posts.',
							'admin_id' => $sessionID
						];

						$this->loadModel('Histories');
	                    $saveActivity   = $this->Histories->saveActivity($options);

						if ($saveActivity == true) {
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
			else {
				$errors = $blogEdit->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
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

        $blogDelete = new BlogDeleteForm();
        if ($this->request->is('ajax')) {
            if ($blogDelete->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
				$blog  = $this->Blogs->get($this->request->getData('id'));
				$title = $blog->title;

				$result = $this->Blogs->delete($blog);

                if ($result) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a Post',
                        'detail'   => ' delete '.$title.' from posts.',
                        'admin_id' => $sessionID
                    ];

                    $this->loadModel('Histories');
                    $saveActivity   = $this->Histories->saveActivity($options);

                    if ($saveActivity == true) {
                        $json = json_encode(['status' => 'ok', 'activity' => true]);
                    }
                    else {
                        $json = json_encode(['status' => 'ok', 'activity' => false]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $blogDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxRemoveTag()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax')) {
			$blogId  = $this->request->getData('id');
			$tagSlug = Text::slug(strtolower($this->request->getData('slug')));

			$this->loadModel('Tags');
			$this->loadModel('BlogsTags');
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

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}