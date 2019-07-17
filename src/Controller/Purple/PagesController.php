<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Utility\Text;
use App\Form\Purple\PageAddForm;
use App\Form\Purple\PageEditForm;
use App\Form\Purple\PageStatusForm;
use App\Form\Purple\PageBlogEditForm;
use App\Form\Purple\PageSaveForm;
use App\Form\Purple\PageCustomSaveForm;
use App\Form\Purple\PageDeleteForm;
use App\Form\Purple\BlogDeleteForm;
use App\Form\Purple\SearchForm;
use App\Form\PageContactForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectFroalaBlocks;
use App\Purple\PurpleProjectPlugins;
use \Gumlet\ImageResize;

class PagesController extends AppController
{
    public $imagesLimit = 30;
    public $pagesLimit  = 10;

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
            $this->loadModel('PageTemplates');
            $this->loadModel('Generals');
            $this->loadModel('CustomPages');
            $this->loadModel('Medias');
            $this->loadModel('Settings');
			$this->loadModel('Histories');

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

            $browseMedias  = $this->Medias->find('all', [
                'order' => ['Medias.id' => 'DESC']])->contain('Admins');

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
                    'title'              => 'Pages | Purple CMS',
                    'pageTitle'          => 'Pages',
                    'pageTitleIcon'      => 'mdi-file-multiple',
                    'pageBreadcrumb'     => 'Pages',
                    'appearanceFavicon'  => $queryFavicon,
                    'settingsDateFormat' => $queryDateFormat->value,
                    'settingsTimeFormat' => $queryTimeFormat->value,
                    'mediaImageTotal'    => $browseMedias->count(),
                    'mediaImageLimit'    => $this->imagesLimit
                ];
                $this->set($data);
                $this->set(compact('browseMedias'));
            }
            else {
                return $this->redirect(
                    ['controller' => 'Authenticate', 'action' => 'login']
                );
            }
        }
    }
    public function index($id = 1)
    {
        $pageAdd    = new PageAddForm();
        $pageEdit   = new PageEditForm();
        $pageStatus = new PageStatusForm();
        $pageDelete = new PageDeleteForm();

        $pages = $this->Pages->find('all', [
            'order' => ['Pages.id' => 'DESC']])->contain('PageTemplates')->contain('Admins')->where(['Pages.parent IS' => NULL]);

        $parentPages = $this->Pages->find('list')->select(['id','title'])->order(['id' => 'ASC'])->where(['parent IS' => NULL, 'page_template_id <>' => '2'])->toArray();

        $data = [
            'pageTitle'         => 'Pages',
            'pageBreadcrumb'    => 'Pages',
            'parentPages'       => $parentPages,
            'pageAdd'           => $pageAdd,
            'pageEdit'          => $pageEdit,
            'pageStatus'        => $pageStatus,
            'pageDelete'        => $pageDelete,
            'pagesTotal'        => $pages->count(),
            'pagesLimit'        => $this->pagesLimit
        ];

        $pageTemplates = $this->PageTemplates->find('list')->select(['id','name'])->order(['id' => 'ASC'])->toArray();
        $this->set(compact('pageTemplates'));

        $this->paginate = [
			'limit' => $this->pagesLimit,
			'page'  => $id
		];
		$pagesList = $this->paginate($pages);
	    $this->set('pages', $pagesList);

        $this->set($data);
    }
    public function detail($type, $id, $slug) 
    {
        $tmpFile            = $type . '.' . $slug;
        $fileName           = $type . '.' . $slug . '.php';
        $generatedTempFile  = new File(TMP . 'html/' . $fileName, true, 0644);

        if ($type == 'general') {
            $pageSave = new PageSaveForm();

            $purpleFroalaBlocks = new PurpleProjectFroalaBlocks();

            $froalaCallToAction = new Folder(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'images' . DS . 'call-to-action');
            $filesCallToAction  = $froalaCallToAction->find('.*\.jpg', true);
            
            $froalaContents     = new Folder(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'images' . DS . 'contents');
            $filesContents      = $froalaContents->find('.*\.jpg', true);
            
            $froalaFeatures     = new Folder(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'images' . DS . 'features');
            $filesFeatures      = $froalaFeatures->find('.*\.jpg', true);
            
            $froalaTeams        = new Folder(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'images' . DS . 'teams');
            $filesTeams         = $froalaTeams->find('.*\.jpg', true);

            $froalaContacts     = new Folder(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'images' . DS . 'contacts');
            $filesContacts      = $froalaContacts->find('.*\.jpg', true);
            
            $froalaLightbox     = new Folder(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'images' . DS . 'lightbox');
            $filesLightbox      = $froalaLightbox->find('.*\.jpg', true);
            
            $froalaSlider       = new Folder(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'images' . DS . 'slider');
            $filesSlider        = $froalaSlider->find('.*\.jpg', true);

            $savedBlocks        = $purpleFroalaBlocks->savedBlocks();

            $themeBlocks        = $purpleFroalaBlocks->themeBlocks();

            $generals = $this->Pages->find('all')->contain('Generals')->where(['Pages.slug' => $slug])->limit(1);
            if ($generals->count() == 0) {
                $result = NULL;
                $generatedTempFile->write('');
            }
            else {
                $result = $this->Pages->get($id, [
                    'contain' => ['Generals']
                ]);
                $generatedTempFile->write(html_entity_decode($result->general->content));
            }

            $froalaBlocks = [
                'fdbCallToAction' => $filesCallToAction,
                'fdbContents'     => $filesContents,
                'fdbFeatures'     => $filesFeatures,
                'fdbTeams'        => $filesTeams,
                'fdbContacts'     => $filesContacts,
                'fdbLightbox'     => $filesLightbox,
                'fdbSlider'       => $filesSlider,
                'savedBlocks'     => $savedBlocks,
                'themeBlocks'     => $themeBlocks,
                'pageSave'        => $pageSave,
                'query'           => $result,
                'tmpFile'         => $tmpFile
            ];

            $this->set($froalaBlocks);
        }
        elseif ($type == 'blog') {
            $pageBlogEdit = new PageBlogEditForm();
            $blogDelete   = new BlogDeleteForm();

            $this->loadModel('Blogs');
            $this->loadModel('BlogCategories');

            $blogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.page_id' => $id])->order(['BlogCategories.ordering' => 'ASC']);

            if (!empty($this->request->getParam('category'))) {
                $blogs = $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id' => $id, 'BlogCategories.slug' => $this->request->getParam('category')]);
                $selectedBlogCategories = $this->BlogCategories->find('all')->contain('Admins')->where(['BlogCategories.slug' => $this->request->getParam('category')])->first();
                $this->set('selectedBlogCategories', $selectedBlogCategories);
            }
            else {
                $blogs = $this->Blogs->find('all')->contain('BlogCategories')->contain('Admins')->where(['BlogCategories.page_id' => $id]);
            }

            $this->set(compact('blogCategories'));
            $this->set(compact('blogs'));
            $data = [
                'pageBlogEdit' => $pageBlogEdit,
                'blogDelete'   => $blogDelete
            ];
            $this->set($data);
        }
        elseif ($type == 'custom') {
            $pageSave = new PageCustomSaveForm();

            $customPages = $this->Pages->find('all')->contain('CustomPages')->where(['Pages.slug' => $slug])->limit(1);
            if ($customPages->count() == 0) {
                $result = NULL;
                $code   = NULL;
            }
            else {
                $result = $this->Pages->get($id, [
                    'contain' => ['CustomPages']
                ]);
                $readFile  = new File(WWW_ROOT . 'uploads' . DS . 'custom-pages' . DS . $customPages->first()->custom_page->file_name);
                $code = $readFile->read();
            }

            $data = [
                'query'    => $result,
                'pageSave' => $pageSave,
                'code'     => $code
            ];

            $this->set($data);
        }

        if ($id == 0 && $slug == 'purple-home-page-builder') {
            $queryHomepage  = $this->Settings->find()->where(['name' => 'homepagestyle'])->first();

            $generatedTempFile->write(html_entity_decode($queryHomepage->value));

            $data = [
                'pages'           => NULL,
                'type'            => $type,
                'homePageContent' => html_entity_decode($queryHomepage->value),
                'pageId'          => 0,
                'pageTitle'       => 'Home',
                'pageBreadcrumb'  => 'Pages::Home',
                'tmpFile'         => $tmpFile
            ];
        }
        else {
            $pages = $this->Pages->find('all', [
                'order' => ['Pages.id' => 'DESC']])->contain('PageTemplates')->contain('Admins')->where(['Pages.slug' => $slug])->first();

            $data = [
                'pages'          => $pages,
                'type'           => $type,
                'pageId'         => $pages->id,
                'pageTitle'      => $pages->title,
                'pageBreadcrumb' => 'Pages::'.$pages->title,
            ];
        }

        $this->set($data);
    }
    public function generatedBlocks($file)
    {
        $this->viewBuilder()->setLayout('blocks');

        $filePath = new File(TMP . 'html/' . $file . '.php', false);
        $content  = $filePath->read();
        $this->set('content', $content);
    }
    public function ajaxAdd() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $pageAdd = new PageAddForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($pageAdd->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $reservedText = ['purple', 'setup', 'posts', 'tag'];

                $slug = Text::slug(strtolower($this->request->getData('title')));
                $findDuplicate = $this->Pages->find()->where(['slug' => $slug]);
                if ($findDuplicate->count() >= 1) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another title."]);
                }
                else {
                    if (in_array($slug, $reservedText)) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please use another title, because the title is reserved word by Purple CMS. Please try again."]);
                    }
                    else {
                        $page = $this->Pages->newEntity();
                        $page = $this->Pages->patchEntity($page, $this->request->getData());
                        $page->admin_id = $sessionID;
                    
                        if ($this->Pages->save($page)) {
                            $json = json_encode(['status' => 'ok']);
                        }
                        else {
                            $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
                        }
                    }
                }
            }
            else {
                $errors = $pageAdd->errors();
                $json = json_encode(['status' => 'error', 'error' => "Make sure you don't enter the same title and please fill all field."]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxSave() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $pageSave = new PageSaveForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($pageSave->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                if ($this->request->getData('id') == 0) {
                    $queryHomepage  = $this->Settings->find()->where(['name' => 'homepagestyle'])->first();
                    $setting        = $this->Settings->get($queryHomepage->id);

                    // Trim empty style tag
                    $trimContent = str_replace('<style></style>', '', $this->request->getData('content'));
                    $trimContent = str_replace('<style class="" style="display: none;"></style>', '', $trimContent);
                    $trimContent = str_replace('<style class="" style="display: none;">', '<style>', $trimContent);

                    // Cut generated slider arrow by UIKit
                    $trimContent = str_replace('<svg width="14" height="24" viewBox="0 0 14 24" xmlns="http://www.w3.org/2000/svg">', '', $trimContent);
                    $trimContent = str_replace('<polyline fill="none" stroke="#000" stroke-width="1.4" points="12.775,1 1.225,12 12.775,23 "></polyline>', '', $trimContent);
                    $trimContent = str_replace('<polyline fill="none" stroke="#000" stroke-width="1.4" points="1.225,23 12.775,12 1.225,1 "></polyline>', '', $trimContent);

                    // Cut style tag and its content
                    $trimContent = preg_replace('~<style(.*?)</style>~Usi', "", $trimContent);

                    $setting->value = trim(htmlentities('<style>'.$this->request->getData('css-content').'</style>'.$trimContent));
                    
                    if ($this->Settings->save($setting)) {
                        /**
                         * Save user activity to histories table
                         * array $options => title, detail, admin_id
                         */
                        
                        $options = [
                            'title'    => 'Update Home Page Content',
                            'detail'   => ' update home page content of your website.',
                            'admin_id' => $sessionID
                        ];

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
                else {
                    $slug = Text::slug(strtolower($this->request->getData('title')));

                    $reservedText = ['purple', 'setup', 'posts', 'tag'];

                    if (in_array($slug, $reservedText)) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please use another title, because the title is reserved word by Purple CMS. Please try again."]);
                    }
                    else {
                        $generals = $this->Generals->find()->where(['page_id' => $this->request->getData('id')]);

                        if ($generals->count() < 1) {
                            $general = $this->Generals->newEntity();

                            // Trim empty style tag
                            $trimContent = str_replace('<style></style>', '', $this->request->getData('content'));
                            $trimContent = str_replace('<style class="" style="display: none;"></style>', '', $trimContent);
                            $trimContent = str_replace('<style class="" style="display: none;">', '<style>', $trimContent);

                            // Cut generated slider arrow by UIKit
                            $trimContent = str_replace('<svg width="14" height="24" viewBox="0 0 14 24" xmlns="http://www.w3.org/2000/svg">', '', $trimContent);
                            $trimContent = str_replace('<polyline fill="none" stroke="#000" stroke-width="1.4" points="12.775,1 1.225,12 12.775,23 "></polyline>', '', $trimContent);
                            $trimContent = str_replace('<polyline fill="none" stroke="#000" stroke-width="1.4" points="1.225,23 12.775,12 1.225,1 "></polyline>', '', $trimContent);

                            // Cut style tag and its content
                            $trimContent = preg_replace('~<style(.*?)</style>~Usi', "", $trimContent);
                            // $trimContent = preg_replace('~<svg width="14" height="24"(.*?)</svg>~Usi', "", $trimContent);

                            $general->content          = trim('<style>'.$this->request->getData('css-content').'</style>'.$trimContent);
                            $general->meta_keywords    = $this->request->getData('meta_keywords');
                            $general->meta_description = $this->request->getData('meta_description');
                            $general->page_id          = $this->request->getData('id');
                            $general->admin_id         = $sessionID;

                            $page = $this->Pages->get($this->request->getData('id'));
                            $page->title = $this->request->getData('title');

                            if ($this->Generals->save($general) && $this->Pages->save($page)) {
                                $record_id = $page->id;
                                $page      = $this->Pages->get($record_id);
                                $title     = $page->title;
                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */
                                
                                $options = [
                                    'title'    => 'Content Making of a Page',
                                    'detail'   => ' add content in '.$title.'.',
                                    'admin_id' => $sessionID
                                ];

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
                        else {
                            $generals = $this->Generals->find()->where(['page_id' => $this->request->getData('id')])->first();
                            $general  = $this->Generals->get($generals->id);

                            $general->content          = trim('<style>'.$this->request->getData('css-content').'</style>'.$this->request->getData('content'));
                            $general->meta_keywords    = $this->request->getData('meta_keywords');
                            $general->meta_description = $this->request->getData('meta_description');
                            $general->page_id          = $this->request->getData('id');
                            $general->admin_id         = $sessionID;

                            $findDuplicate = $this->Pages->find('all')->where(['slug' => $slug, 'id <>' => $this->request->getData('id')]);
                            if ($findDuplicate->count() >= 1) {
                                $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another title."]);
                            }
                            else {
                                $page = $this->Pages->get($this->request->getData('id'));
                                $page->title = $this->request->getData('title');

                                if ($this->Generals->save($general) && $this->Pages->save($page)) {
                                    $record_id = $page->id;
                                    $page      = $this->Pages->get($record_id);
                                    $title     = $page->title;
                                    /**
                                     * Save user activity to histories table
                                     * array $options => title, detail, admin_id
                                     */
                                    
                                    $options = [
                                        'title'    => 'Content Update of a Page',
                                        'detail'   => ' update content in '.$title.'.',
                                        'admin_id' => $sessionID
                                    ];

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
                    }
                }
            }
            else {
                $errors = $pageSave->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxChangeStatus() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $pageStatus = new PageStatusForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($pageStatus->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $page         = $this->Pages->get($this->request->getData('id'));
                $title        = $page->title;
                $page->status = $this->request->getData('status');

                if ($this->request->getData('status') == '0') {
                    $statusText = 'draft';
                }
                else if ($this->request->getData('status') == '1') {
                    $statusText = 'publish';
                }

                if ($this->Pages->save($page)) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Change Status of a Page',
                        'detail'   => ' change status '.$title.' in pages to '.$statusText.'.',
                        'admin_id' => $sessionID
                    ];

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
            else {
                $errors = $pageStatus->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxSaveBlogPage() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $pageBlogEdit = new PageBlogEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($pageBlogEdit->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $slug = Text::slug(strtolower($this->request->getData('title')));

                $reservedText = ['purple', 'setup', 'posts', 'tag'];

                if (in_array($slug, $reservedText)) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please use another title, because the title is reserved word by Purple CMS. Please try again."]);
                }
                else {
                    $page  = $this->Pages->get($this->request->getData('id'));
                    $title = $page->title;
                    $page  = $this->Pages->patchEntity($page, $this->request->getData());
                    
                    if ($this->Pages->save($page)) {
                        $record_id = $page->id;
                        $page      = $this->Pages->get($record_id);
                        $newTitle  = $page->title;

                        /**
                         * Save user activity to histories table
                         * array $options => title, detail, admin_id
                         */
                        
                        $options = [
                            'title'    => 'Data Change of a Page',
                            'detail'   => ' change page title from '.$title.' to '.$newTitle.'.',
                            'admin_id' => $sessionID
                        ];

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
                $errors = $pageBlogEdit->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxSaveCustomPage() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $pageCustomSave = new PageCustomSaveForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($pageCustomSave->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $slug = Text::slug(strtolower($this->request->getData('title')));
                    
                $reservedText = ['purple', 'setup'];

                if (in_array($slug, $reservedText)) {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please use another title, because the title is reserved word by Purple CMS. Please try again."]);
                }
                else {
                    $customPages = $this->CustomPages->find()->where(['page_id' => $this->request->getData('id')]);

                    if ($customPages->count() < 1) {
                        $fileName   = Text::slug(strtolower($this->request->getData('title'))) . '-' . time() . '.php';
                        $customPage = $this->CustomPages->newEntity();

                        $customPage->file_name        = $fileName;
                        $customPage->meta_keywords    = $this->request->getData('meta_keywords');
                        $customPage->meta_description = $this->request->getData('meta_description');
                        $customPage->page_id          = $this->request->getData('id');
                        $customPage->admin_id         = $sessionID;

                        // Create php file with code editor content
                        $createFile = new File(WWW_ROOT . 'uploads' . DS . 'custom-pages' . DS . $fileName, true, 0644);
                        $writeFile  = new File(WWW_ROOT . 'uploads' . DS . 'custom-pages' . DS . $fileName);
                        $writeFile->write($this->request->getData('content'));

                        $page = $this->Pages->get($this->request->getData('id'));
                        $page->title = $this->request->getData('title');

                        if ($this->CustomPages->save($customPage) && $this->Pages->save($page)) {
                            $record_id = $page->id;
                            $page      = $this->Pages->get($record_id);
                            $title     = $page->title;
                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => 'Content Making of a Page',
                                'detail'   => ' add custom code in '.$title.'.',
                                'admin_id' => $sessionID
                            ];

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
                    else {
                        $customPages = $this->CustomPages->find()->where(['page_id' => $this->request->getData('id')])->first();
                        $customPage  = $this->CustomPages->get($customPages->id);

                        $fileName                     = $customPage->file_name;
                        $customPage->meta_keywords    = $this->request->getData('meta_keywords');
                        $customPage->meta_description = $this->request->getData('meta_description');
                        $customPage->page_id          = $this->request->getData('id');
                        $customPage->admin_id         = $sessionID;

                        $slug = Text::slug(strtolower($this->request->getData('title')));
                        $findDuplicate = $this->Pages->find('all')->where(['slug' => $slug, 'id <>' => $this->request->getData('id')]);
                        if ($findDuplicate->count() >= 1) {
                            $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another title dsa."]);
                        }
                        else {
                            $writeFile  = new File(WWW_ROOT . 'uploads' . DS . 'custom-pages' . DS . $fileName);
                            $writeFile->write($this->request->getData('content'));

                            $page = $this->Pages->get($this->request->getData('id'));
                            $page->title = $this->request->getData('title');

                            if ($this->CustomPages->save($customPage) && $this->Pages->save($page)) {
                                $record_id = $page->id;
                                $page      = $this->Pages->get($record_id);
                                $title     = $page->title;
                                /**
                                 * Save user activity to histories table
                                 * array $options => title, detail, admin_id
                                 */
                                
                                $options = [
                                    'title'    => 'Content Update of a Page',
                                    'detail'   => ' update custom code in '.$title.'.',
                                    'admin_id' => $sessionID
                                ];

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
                }
            }
            else {
                $errors = $pageCustomSave->errors();
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

        $pageDelete = new PageDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($pageDelete->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $page  = $this->Pages->get($this->request->getData('id'));
                $title = $page->title;

                // Delete custom php file if page type is custom
                if ($this->request->getData('page_type') == 'custom') {
                    $checkCustom = $this->CustomPages->find()->where(['page_id' => $this->request->getData('id')])->count();

                    if ($checkCustom > 0) {
                        $customPage = $this->CustomPages->find()->where(['page_id' => $this->request->getData('id')])->first();
                        $fileName   = $customPage->file_name;
                        $customFile = WWW_ROOT . 'uploads' . DS .'custom-pages' . DS . $fileName;

                        if ($this->Pages->delete($page)) {
                            if (file_exists($customFile)) {
                                $readFile   = new File($customFile);
                                $readFile->delete();
                            }

                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => 'Deletion of a Page',
                                'detail'   => ' delete '.$title.' in pages.',
                                'admin_id' => $sessionID
                            ];

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
                        if ($this->Pages->delete($page)) {
                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => 'Deletion of a Page',
                                'detail'   => ' delete '.$title.' in pages.',
                                'admin_id' => $sessionID
                            ];

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
                }
                else {
                    if ($this->Pages->delete($page)) {
                        /**
                         * Save user activity to histories table
                         * array $options => title, detail, admin_id
                         */
                        
                        $options = [
                            'title'    => 'Deletion of a Page',
                            'detail'   => ' delete '.$title.' in pages.',
                            'admin_id' => $sessionID
                        ];

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
            }
            else {
                $errors = $pageDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxFroalaBlocks()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $purpleFroalaBlocks = new PurpleProjectFroalaBlocks();

            $number  = $this->request->getData('number');
            $filter  = $this->request->getData('filter');
            $webroot = $this->request->getAttribute('webroot');
            $randomNumber = rand(0, 99999);

            $findBlock = $purpleFroalaBlocks->$filter($number, $webroot, $randomNumber);

            $json = json_encode(['status' => 'ok', 'id' => $randomNumber, 'html' => $findBlock]);
            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxThemeBlocks()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $purpleFroalaBlocks = new PurpleProjectFroalaBlocks();

            $number  = $this->request->getData('number');
            $filter  = $this->request->getData('filter');
            $webroot = $this->request->getAttribute('webroot');
            $randomNumber = rand(0, 99999);

            $findBlock = $purpleFroalaBlocks->themeBlocksJson($filter);

            $json = json_encode(['status' => 'ok', 'id' => $randomNumber, 'html' => $findBlock]);
            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxLoadSavedBlock()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $purpleFroalaBlocks = new PurpleProjectFroalaBlocks();

            $number  = $this->request->getData('number');
            $svId    = $this->request->getData('svId');
            $filter  = $this->request->getData('filter');
            $webroot = $this->request->getAttribute('webroot');
            $randomNumber = rand(0, 99999);

            $findBlock = $purpleFroalaBlocks->savedBlocksJson($filter);

            if (strpos($svId, 'saved::') !== false) {
                $explodeId = explode('::', $svId);
                $json      = json_encode(['status' => 'ok', 'id' => $explodeId[1], 'html' => $findBlock]);
            }
            else {
                $json = json_encode(['status' => 'ok', 'id' => $randomNumber, 'html' => $findBlock]);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxSaveBlock()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $name   = $this->request->getData('name');
            $html   = $this->request->getData('html');
            $target = $this->request->getData('target');

            $randomId = rand(10000, 99999);
            $newHtml  = str_replace($target, $randomId, $html);
            $newHtml2 = str_replace('fdb-block-selected', $randomId, $newHtml);

            $random   = rand(1000, 9999);
            $fileName = Text::slug(strtolower($this->request->getData('name')));
            $content  = [
                'name' => trim($name),
                'id'   => $randomId,
                'html' => $newHtml2
            ];
            $json = json_encode($content);

            $jsonFile   = $fileName.'_'.$random.'.json';
            $writeJson  = new File(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'saved' . DS . $jsonFile, true);
            
            if ($writeJson->write($json)) {
                $json = json_encode(['status' => 'ok', 'json' => $jsonFile]);
            }
            else {
                $json = json_encode(['status' => 'error']);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxDeleteBlock() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $file     = $this->request->getData('file');
            $class    = $this->request->getData('class');
            $fileJson = new File(WWW_ROOT . 'master-assets' . DS . 'plugins' . DS . 'froala-blocks' . DS . 'saved' . DS . $file);

            if ($fileJson->delete()) {
                $json = json_encode(['status' => 'ok', 'json' => $class]);
            }
            else {
                $json = json_encode(['status' => 'error']);
            }

            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxFroalaCodeEditor()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $id          = $this->request->getData('id');
            $url         = $this->request->getData('url');
            $redirect    = $this->request->getData('redirect');
            $html        = $this->request->getData('html');
            if (strpos($html, '</style>') !== false) {
                $explodeHtml = explode('</style>', $html);
                $style       = str_replace('<style>', '', $explodeHtml[0]);
                $json        = json_encode(['status' => 'ok', 'id' => $id, 'url' => $url, 'redirect' => $redirect, 'html' => $explodeHtml[1], 'css' => $style]);
            }
            else {
                $html        = $this->request->getData('html');
                $style       = '';
                $json        = json_encode(['status' => 'ok', 'id' => $id, 'url' => $url, 'redirect' => $redirect, 'html' => $html, 'css' => $style]);
            }
            $this->set(['json' => $json]);
            $this->render();
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function ajaxParseJsonHtml()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('ajax') || $this->request->is('post')) {
            $cssPath    = $this->request->getData('css');
            $jsPath     = $this->request->getData('js');
            $html       = $this->request->getData('htmldata');
            $jsonDecode = json_decode($html, true);

            ob_start();

            function createHtmlFromArray($array) {
                echo '<ul>';

                $treeId    = '';
                $tagId     = '';
                $treeClass = '';

                foreach ($array['child'] as $data) {
                    if ($data['node'] == 'element') {
                        $tag = $data['tag'];


                        if (array_key_exists('attr', $data)) {
                            if (array_key_exists('class', $data['attr'])) {
                                $tagClass = $data['attr']['class'];
                                if (is_array($tagClass)) {
                                    $countClass = count($tagClass);
                                    if ($countClass > 1) {
                                        $formatedClass = ' <span class="uk-text-muted">'.$tagClass[0].'...</span>';
                                    }
                                    else {
                                        $formatedClass = ' <span class="uk-text-muted">'.$tagClass[0].'</span>';
                                    }

                                    $treeClass = implode('::', $tagClass); 
                                }
                                else {
                                    if ($tagClass == '') {
                                        $formatedClass = '';
                                        $treeClass     = 'empty-class';
                                    }
                                    else {
                                        $formatedClass = ' <span class="uk-text-muted">'.$tagClass.'</span>';
                                        $treeClass     = $tagClass;
                                    }
                                }
                            }
                            else {
                                $formatedClass = '';
                                $treeClass     = 'empty-class';
                            }

                            if (array_key_exists('id', $data['attr'])) {
                                if ($data['attr']['id'] == '') {
                                    $tagId = 'empty-id';
                                    $formatedId = '';
                            }
                                else {
                                    $tagId = $data['attr']['id'];
                                    $formatedId = '<span class="text-success">#'.$tagId.'</span>';
                                }
                            }
                            else {
                                $formatedId = '';
                                $tagId      = 'empty-id';
                            }

                            if (array_key_exists('data-tree-id', $data['attr'])) {
                                $treeId = $data['attr']['data-tree-id'];
                            }
                            else {
                                $treeId = '';
                            }
                        }
                        else {
                            $formatedClass = '';
                            $formatedId    = '';
                        }

                        echo '<li class="jstree-open" data-jstree=\'{"icon":"fa fa-code"}\' data-purple-tree-hover="yes" data-purple-tree-id="'.$treeId.'" data-purple-tree-tag="'.$tag.'" data-purple-tree-class="'.$treeClass.'" data-purple-tree-hash="'.$tagId.'"><a href="#"><strong>'.$tag.'</strong>'.$formatedId.$formatedClass.'</a>';

                        if (array_key_exists("child",$data)) {
                            createHtmlFromArray($data);
                        }
                        else {
                            echo '</li>';
                        }
                    }
                }
                echo '</ul>';
            }
            
            createHtmlFromArray($jsonDecode);
            
            $htmlNow = ob_get_contents();
            ob_end_clean();
            
            $json = json_encode(['status' => 'ok', 'content' => $htmlNow, 'jsond' => $jsonDecode]);
            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
}