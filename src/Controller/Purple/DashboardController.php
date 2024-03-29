<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\DashboardMonthOfVisitForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectPlugins;

class DashboardController extends AppController
{
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

		// Get Admin Session data
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		// Set layout
		$this->viewBuilder()->setLayout('dashboard');

		// Load other models
		$this->loadModel('Admins');
		$this->loadModel('Comments');
		$this->loadModel('Blogs');
		$this->loadModel('BlogVisitors');
		$this->loadModel('Visitors');
		$this->loadModel('Settings');
		$this->loadModel('Histories');

		// Check debug is on or off
		if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
			$cakeDebug = 'on';
		} 
		else {
			$cakeDebug = 'off';
		}            

		$queryAdmin   = $this->Admins->signedInUser($sessionID, $sessionPassword);
		$queryFavicon = $this->Settings->fetch('favicon');

		$rowCount = $queryAdmin->count();
		if ($rowCount > 0) {
			$purpleGlobal = new PurpleProjectGlobal();
			$greeting     = $purpleGlobal->greetings();

			$adminData = $queryAdmin->first();

			$dashboardSearch = new SearchForm();
			
			// Plugins List
			$purplePlugins 	= new PurpleProjectPlugins();
			$plugins		= $purplePlugins->purplePlugins();
			$this->set('plugins', $plugins);

			$headlessStatus = $this->Settings->find()->where(['name' => 'headlessfront'])->first();

			$data = [
				'sessionHost'       => $sessionHost,
				'sessionID'         => $sessionID,
				'sessionPassword'   => $sessionPassword,
				'cakeDebug'         => $cakeDebug,
				'adminName' 	    => ucwords($adminData->display_name),
				'adminLevel' 	    => $adminData->level,
				'adminEmail' 	    => $adminData->email,
				'adminPhoto' 	    => $adminData->photo,
				'greeting'			=> $greeting,
				'dashboardSearch'	=> $dashboardSearch,
				'title'             => 'Dashboard | Purple CMS',
				'pageTitle'         => 'Dashboard',
				'pageTitleIcon'     => 'mdi-home',
				'pageBreadcrumb'    => $headlessStatus->value == 'enable' ? '' : 'Overview <span id="selected-date-range"></span> <a href="#" class="button-dashboard-date-range" data-purple-modal="#modal-select-date-range"><span class="dashboard-date-range bg-gradient-primary text-white" uk-tooltip="title:Select Month of Visit""><i class="mdi mdi-calendar"></i></span></a>',
				'appearanceFavicon' => $queryFavicon
			];
			$this->set($data);
		}
		else {
			return $this->redirect(
				['controller' => 'Authenticate', 'action' => 'login', '?' => ['ref' => Router::url($this->getRequest()->getRequestTarget(), true)]]
			);
		}
	}
	public function index() 
	{
		$this->loadModel('Collections');
		$this->loadModel('CollectionDatas');
		$this->loadModel('Singletons');
		$this->loadModel('SingletonDatas');

		$session         = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		if ($this->request->getEnv('HTTP_HOST') != $sessionHost || !$session->check('Admin.id')) {
			return $this->redirect(
	            ['controller' => 'Authenticate', 'action' => 'login']
	        );
		}
		else {
			$queryAdmin   = $this->Admins->signedInUser($sessionID, $sessionPassword)->first();
			$loggedInAdmin = [
				'id'    => $sessionID,
				'email' => $queryAdmin->email,
				'level' => $queryAdmin->level
			];

			$headlessStatus = $this->Settings->find()->where(['name' => 'headlessfront'])->first();
			$headlessWeb    = $this->Settings->find()->where(['name' => 'headlessweb'])->first();

			if ($headlessStatus->value == 'enable') {
				$totalCollections     = $this->Collections->total();
				$totalCollectionDatas = $this->CollectionDatas->total();
				$collectionsTable     = $this->Collections->showInDashboard();

				$totalSingletons     = $this->Singletons->total();
				$totalSingletonDatas = $this->SingletonDatas->total();
				$singletonsTable     = $this->Singletons->showInDashboard();
			}
			else {
				// Dashboard mont of visit form
				$dashboardMonthOfVisit  = new DashboardMonthOfVisitForm();

				// Get last month name and year
				$lastMonth        = date('Y-m', strtotime(date('Y-m')." -1 month"));
				$explodeLastMonth = explode('-', $lastMonth);
				$lastMonthName    = $explodeLastMonth[1];
				$lastMonthYear    = $explodeLastMonth[0];

				$purpleGlobal  = new PurpleProjectGlobal();

				$currentMonthVisitor    = $this->Visitors->countVisitorsInMonth();
				$lastMonthVisitor       = $this->Visitors->countVisitorsInMonth($lastMonthYear, $lastMonthName);
				$totalAllVisitors       = $this->Visitors->totalAllVisitors();
				$totalUniqueVisitors    = $this->Visitors->totalUniqueVisitors();
				$totalMobileVisitors    = $this->Visitors->totalMobileVisitors();
				$twoWeeksVisitors       = $this->Visitors->lastTwoWeeksTotalVisitors();
				$twoWeeksBeforeVisitors = $this->Visitors->lastTwoWeeksBeforeTotalVisitors();
				if ($twoWeeksVisitors > $twoWeeksBeforeVisitors) {
					$twoWeeksIcon = 'up';
				}
				elseif ($twoWeeksVisitors == $twoWeeksBeforeVisitors) {
					$twoWeeksIcon = 'same';
				}
				elseif ($twoWeeksVisitors < $twoWeeksBeforeVisitors) {
					$twoWeeksIcon = 'down';
				}

				if ($lastMonthVisitor > 0) {
					$precentageVisitor = round(abs($currentMonthVisitor - $lastMonthVisitor) / $lastMonthVisitor * 100, 2);
				}
				else {
					$precentageVisitor = $currentMonthVisitor * 100;
				}

				if($lastMonthVisitor > $currentMonthVisitor) {
					$visitorsCard = 'Decreased by '.$precentageVisitor.'%';
				} 
				elseif($lastMonthVisitor < $currentMonthVisitor)  {
					if ($precentageVisitor > 1000) {
						$visitorsCard = 'Increased 10 times more than last month';
					}
					else {
						$visitorsCard = 'Increased by '.$precentageVisitor.'%'; 
					}
				}
				elseif($lastMonthVisitor == $currentMonthVisitor) {
					$visitorsCard = 'Same as last month';
				}

				$monthLatinArray  = array();
				for ($m = 1; $m <= 12; $m++) {
					$monthLatin   = date('F', mktime(0, 0, 0, $m, 10));
					$monthNumber  = date('m', mktime(0, 0, 0, $m, 10));
					$monthLatinArray[$monthNumber]  = $monthLatin;
				}
				$selectedMonth = date('m');

				$yearArray = array();
				for ($y = 2015; $y <= date('Y'); $y++) {
					$yearArray[$y]  = $y;
				}
				$selectedYear = date('Y');


				$allComments    = $this->Comments->dashboardStatistic();		
				$unreadComments = $this->Comments->dashboardStatistic('unread');	

				$allPosts       = $this->Blogs->dashboardStatistic();		
				$draftPosts     = $this->Blogs->dashboardStatistic('draft');		
				$oneMonthPosts  = $this->Blogs->lastMonthTotalPosts();
			}

			$data = [
				'headlessStatus' => $headlessStatus->value,
				'headlessWeb'	 => $headlessWeb->value,
			];

			if ($headlessStatus->value == 'enable') {
				$data['totalCollections']     = $totalCollections;
				$data['totalCollectionDatas'] = $totalCollectionDatas;
				$data['collectionsTable']     = $collectionsTable;
				$data['totalSingletons']      = $totalSingletons;
				$data['totalSingletonDatas']  = $totalSingletonDatas;
				$data['singletonsTable']      = $singletonsTable;
			}
			else {
				$data['statisticVisitors']         = $purpleGlobal->shortenNumber($currentMonthVisitor);
				$data['precentageVisitors']        = $precentageVisitor.'%';
				$data['visitorsCard']              = $visitorsCard;
				$data['visitorsMonth']             = $this->Visitors->lastSixMonthVisitors();
				$data['totalVisitors6Month']       = $this->Visitors->lastSixMonthTotalVisitors();
				$data['totalUniqueVisitors6Month'] = $this->Visitors->lastSixMonthTotalUniqueVisitors();
				$data['totalMobileVisitors6Month'] = $this->Visitors->lastSixMonthTotalMobileVisitors();
				$data['totalAllVisitors']          = $totalAllVisitors;
				$data['totalUniqueVisitors']       = $totalUniqueVisitors;
				$data['totalMobileVisitors']       = $totalMobileVisitors;
				$data['twoWeeksVisitors']          = $twoWeeksVisitors;
				$data['twoWeeksIcon']              = $twoWeeksIcon;
				$data['dashboardMonthOfVisit']     = $dashboardMonthOfVisit;
				$data['monthLatinArray']           = $monthLatinArray;
				$data['yearArray']                 = $yearArray;
				$data['selectedMonth']             = $selectedMonth;
				$data['selectedYear']              = $selectedYear;
				$data['allComments']               = $allComments;
				$data['unreadComments']            = $unreadComments;
				$data['allPosts']                  = $allPosts;
				$data['draftPosts']                = $draftPosts;
				$data['oneMonthPosts']             = $oneMonthPosts;
			}

	    	$this->set($data);

	        if ($loggedInAdmin['level'] == 1) {
		    	$histories = $this->Histories->recentActivity();
	    	}
	    	else {
		    	$histories = $this->Histories->recentActivity($loggedInAdmin['id']);
			}
			
	    	$this->set(compact('histories'));
	    }
	}
	public function ajaxUpdateStatistic()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $dashboardMonthOfVisit  = new DashboardMonthOfVisitForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($dashboardMonthOfVisit->execute($this->request->getData())) {

            	$totalDays = cal_days_in_month(CAL_GREGORIAN, $this->request->getData('month'), $this->request->getData('year'));
	            $nvArray  = '';
	            $uvArray  = '';
	            $mvArray  = '';
	            $label    = array();
	            $value    = array();
	            $unique   = array();
	            $mobile   = array();
	            $dateFormat = $this->request->getData('year').'-'.$this->request->getData('month').'-d';
	            for ($i=1; $i<=$totalDays; $i++) {
	                $time = mktime(12, 0, 0, $this->request->getData('month'), $i, $this->request->getData('year'));          
	                if (date('m', $time) == $this->request->getData('month'))  {      
	                    //date for x axis
	                    ${"xdate".$i}   = date('M j', $time);
	                    ${"mindate".$i} = date($dateFormat, $time);
	                }

					${"countTotalVisitor".$i}       = $this->Visitors->totalVisitorsDate(${"mindate".$i});
					${"countTotalUniqueVisitor".$i} = $this->Visitors->totalUniqueVisitorsDate(${"mindate".$i});
					${"countTotalMobileVisitor".$i} = $this->Visitors->totalMobileVisitorsDate(${"mindate".$i});
	                if ($i == $totalDays) {
	                    $nvArray .= ${"countTotalVisitor".$i};
	                    $uvArray .= ${"countTotalUniqueVisitor".$i};
	                    $mvArray .= ${"countTotalMobileVisitor".$i};
	                }
	                else {
	                    $nvArray .= ${"countTotalVisitor".$i}.',';
	                    $uvArray .= ${"countTotalUniqueVisitor".$i}.',';
	                    $mvArray .= ${"countTotalMobileVisitor".$i}.',';
	                }

					$label[]  = ${"xdate".$i};
					$value[]  = ${"countTotalVisitor".$i};
					$unique[] = ${"countTotalUniqueVisitor".$i};
					$mobile[] = ${"countTotalMobileVisitor".$i};
	            }

				$explodeNormal = explode(',', $nvArray);
				$maxValue      = max($explodeNormal);
				$sumNormal     = array_sum($explodeNormal);
				$explodeUnique = explode(',', $uvArray);
				$explodeMobile = explode(',', $mvArray);
				$maxUnique     = max($explodeUnique);
				$maxMobile     = max($explodeMobile);
				$maxVisitor    = max($maxValue, $maxMobile);

				$current = date('M Y', strtotime($this->request->getData('year').'-'.$this->request->getData('month').'-01'));

				// Visitors Platform
				$totalVisitorsAndroid = $this->Visitors->visitorsPlatform('Android', $this->request->getData('month'), $this->request->getData('year'));
				$totalVisitorsLinux   = $this->Visitors->visitorsPlatform('Linux', $this->request->getData('month'), $this->request->getData('year'));
				$totalVisitorsMac     = $this->Visitors->visitorsPlatform('OS X', $this->request->getData('month'), $this->request->getData('year'));
				$totalVisitorsWindows = $this->Visitors->visitorsPlatform('Windows', $this->request->getData('month'), $this->request->getData('year'));
				$totalVisitorsIos     = $this->Visitors->visitorsPlatform('iOS', $this->request->getData('month'), $this->request->getData('year'));
				$totalVisitorsUnkOs   = $this->Visitors->visitorsPlatform('Unknown OS', $this->request->getData('month'), $this->request->getData('year'));

				$visitorsPlatform = array();
				array_push($visitorsPlatform, $totalVisitorsAndroid, $totalVisitorsLinux, $totalVisitorsMac, $totalVisitorsWindows, $totalVisitorsIos);
				$totalVisitorsPlatform = $totalVisitorsAndroid + $totalVisitorsLinux + $totalVisitorsMac + $totalVisitorsWindows + $totalVisitorsIos;

				$data = [
					'current'               => $current,
					'label'                 => $label,
					'value'                 => $value,
					'unique'                => $unique,
					'mobile'                => $mobile,
					'maxValue'              => $maxValue,
					'maxUnique'             => $maxUnique,
					'maxMobile'             => $maxMobile,
					'maxVisitor'            => $maxVisitor,
					'sumNormal'				=> $sumNormal,
					'visitorsAndroid'       => $totalVisitorsAndroid,
					'visitorsLinux'         => $totalVisitorsLinux,
					'visitorsMac'           => $totalVisitorsMac,
					'visitorsWindows'       => $totalVisitorsWindows,
					'visitorsIos'           => $totalVisitorsIos,
					'visitorsUnkOs'         => $totalVisitorsUnkOs,
					'totalVisitorsPlatform' => $totalVisitorsPlatform
				];
				
		    	$this->set($data);
            }
            else {
            	$errors = $dashboardMonthOfVisit->getErrors();
            }

            $this->render();
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxGetTopCountries()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$topCountries = $this->Visitors->topCountries();

		$this->set('topCountries', $topCountries);
	}
	public function ajaxGetTopPosts()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$topPosts = $this->BlogVisitors->topBlogs();

		$this->set('topPosts', $topPosts);
	}
}