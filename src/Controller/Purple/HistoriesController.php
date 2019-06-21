<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;
use App\Form\Purple\HistoryFilterForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;

class HistoriesController extends AppController
{
	public $historiesLimit = 10;

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

            if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
                $cakeDebug = 'on';
            } 
            else {
                $cakeDebug = 'off';
            }

			$queryAdmin   = $this->Admins->find()->where(['id' => $sessionID, 'password' => $sessionPassword])->limit(1);
            $queryFavicon = $this->Settings->find()->where(['name' => 'favicon'])->first();

			$rowCount = $queryAdmin->count();
			if ($rowCount > 0) {
				$adminData = $queryAdmin->first();

				$dashboardSearch = new SearchForm();
				
				// Plugins List
				$purplePlugins 	= new PurpleProjectPlugins();
				$plugins		= $purplePlugins->purplePlugins();
	        	$this->set('plugins', $plugins);

				$data = [
					'sessionHost'       => $sessionHost,
					'sessionID'         => $sessionID,
					'sessionPassword'   => $sessionPassword,
                    'cakeDebug'         => $cakeDebug,
					'adminName' 	    => ucwords($adminData->display_name),
					'adminLevel' 	    => $adminData->level,
					'adminEmail' 	    => $adminData->email,
					'adminPhoto' 	    => $adminData->photo,
                    'greeting'          => '',
					'dashboardSearch'   => $dashboardSearch,
					'title'             => 'Activity Log | Purple CMS',
					'pageTitle'         => 'Activity Log',
					'pageTitleIcon'     => 'mdi-cached',
					'pageBreadcrumb'    => 'Activity Log',
                    'appearanceFavicon' => $queryFavicon
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
	public function index($id = 1)
	{
		$historyFilter = new HistoryFilterForm();

		$monthLatinArray  = array();
		for ($m = 1; $m <= 12; $m++) {
			$monthLatin   = date('F', mktime(0, 0, 0, $m, 10));
			$monthNumber  = date('m', mktime(0, 0, 0, $m, 10));
			$monthLatinArray[$monthNumber]  = $monthLatin;
		}

		$yearArray = array();
		for ($y = 2015; $y <= date('Y'); $y++) {
			$yearArray[$y]  = $y;
		}

		$histories = $this->Histories->find('all')->contain('Admins')->order(['Histories.id' => 'DESC']);

        $data = [
			'historiesTotal'  => $histories->count(),
			'historiesLimit'  => $this->historiesLimit,
			'historyFilter'   => $historyFilter,
			'monthLatinArray' => $monthLatinArray,
			'yearArray'       => $yearArray,
		];

		$this->paginate = [
			'limit' => $this->historiesLimit,
			'page'  => $id
		];
		$historiesList = $this->paginate($histories);
	    $this->set('histories', $historiesList);
    	$this->set($data);
	}
	public function filter($year, $month, $id = 1)
	{
		$historyFilter = new HistoryFilterForm();

		$monthLatinArray  = array();
		for ($m = 1; $m <= 12; $m++) {
			$monthLatin   = date('F', mktime(0, 0, 0, $m, 10));
			$monthNumber  = date('m', mktime(0, 0, 0, $m, 10));
			$monthLatinArray[$monthNumber]  = $monthLatin;
		}

		$yearArray = array();
		for ($y = 2015; $y <= date('Y'); $y++) {
			$yearArray[$y]  = $y;
		}

		$selectedMonth = date('F Y', strtotime($year.'-'.$month.'-01'));

		$histories = $this->Histories->find('all')->contain('Admins')->where(['YEAR(Histories.created)' => $year, 'MONTH(Histories.created)' => $month])->order(['Histories.id' => 'DESC']);

        $data = [
			'historiesTotal'  => $histories->count(),
			'historiesLimit'  => $this->historiesLimit,
			'pageBreadcrumb'  => 'Activity Log::'.$selectedMonth,
			'historyFilter'   => $historyFilter,
			'monthLatinArray' => $monthLatinArray,
			'yearArray'       => $yearArray
		];

		$this->paginate = [
			'limit' => $this->historiesLimit,
			'page'  => $id
		];
		$historiesList = $this->paginate($histories);
	    $this->set('histories', $historiesList);
    	$this->set($data);

	}
	public function ajaxFilter()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$historyFilter = new HistoryFilterForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($historyFilter->execute($this->request->getData())) {
                $json = json_encode(['status' => 'ok', 'activity' => false]);
				$this->set(['json' => $json]);
			}
			else {
            	$errors = $historyFilter->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}