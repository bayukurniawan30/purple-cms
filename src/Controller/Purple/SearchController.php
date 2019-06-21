<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\SearchForm;
use Cake\Utility\Text;
use Cake\I18n\Time;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;

class SearchController extends AppController
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
            $this->loadModel('Blogs');
            $this->loadModel('Settings');

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
					'title'              => 'Search | Purple CMS',
					'pageTitle'          => 'Search',
					'pageTitleIcon'      => 'mdi-magnify',
					'pageBreadcrumb'     => 'Search',
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
	}
	public function index()
	{
        if ($this->request->is('post')) {
        	// Search Post with related search
            $searchCondition = '%'.$this->request->getData('search').'%';
            $blogs = $this->Blogs->find('all')->contain('BlogCategories', function (Query $q) {
            			return $q->contain('Pages');
            		 })->contain('Admins')->where(['Blogs.status' => '1', 'Blogs.title LIKE' => $searchCondition])->orWhere(['Blogs.content LIKE' => $searchCondition]);

            $data = [
				'searchText'   => $this->request->getData('search'),
				'searchResult' => $blogs
            ];

            $this->set($data);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
	}
}