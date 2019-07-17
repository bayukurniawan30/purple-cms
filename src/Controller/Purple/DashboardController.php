<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\AdminLoginForm;
use App\Form\Purple\DashboardMonthOfVisitForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectPlugins;

class DashboardController extends AppController
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
			$this->loadModel('Comments');
    		$this->loadModel('Blogs');
	        $this->loadModel('Visitors');
            $this->loadModel('Settings');
	        $this->loadModel('Histories');

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
			    $purpleGlobal = new PurpleProjectGlobal();
			    $greeting     = $purpleGlobal->greetings();

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
					'greeting'			=> $greeting,
					'dashboardSearch'	=> $dashboardSearch,
					'title'             => 'Dashboard | Purple CMS',
					'pageTitle'         => 'Dashboard',
					'pageTitleIcon'     => 'mdi-home',
					'pageBreadcrumb'    => 'Overview <span id="selected-date-range"></span> <a href="#" class="button-dashboard-date-range" data-purple-modal="#modal-select-date-range"><span class="dashboard-date-range bg-gradient-primary text-white" uk-tooltip="title:Select Month of Visit""><i class="mdi mdi-calendar"></i></span></a>',
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
	public function index() 
	{
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
    		

			$queryAdmin   = $this->Admins->find()->where(['id' => $sessionID, 'password' => $sessionPassword])->first();
			$loggedInAdmin = [
				'id'    => $sessionID,
				'email' => $queryAdmin->email,
				'level' => $queryAdmin->level
			];

	        $dashboardMonthOfVisit  = new DashboardMonthOfVisitForm();

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

			$yearArray = array();
			for ($y = 2015; $y <= date('Y'); $y++) {
				$yearArray[$y]  = $y;
			}

			$allComments    = $this->Comments->dashboardStatistic();		
			$unreadComments = $this->Comments->dashboardStatistic('unread');	

			$allPosts       = $this->Blogs->dashboardStatistic();		
			$draftPosts     = $this->Blogs->dashboardStatistic('draft');		
			$oneMonthPosts  = $this->Blogs->lastMonthTotalPosts();		

			$data = [
				'statisticVisitors'         => $purpleGlobal->shortenNumber($currentMonthVisitor),
				'precentageVisitors'        => $precentageVisitor.'%',
				'visitorsCard'              => $visitorsCard,
				'visitorsMonth'             => $this->Visitors->lastSixMonthVisitors(),
				'totalVisitors6Month'       => $this->Visitors->lastSixMonthTotalVisitors(),
				'totalUniqueVisitors6Month' => $this->Visitors->lastSixMonthTotalUniqueVisitors(),
				'totalMobileVisitors6Month' => $this->Visitors->lastSixMonthTotalMobileVisitors(),
				'totalAllVisitors'     		=> $totalAllVisitors,
				'totalUniqueVisitors'     	=> $totalUniqueVisitors,
				'totalMobileVisitors'     	=> $totalMobileVisitors,
				'twoWeeksVisitors'     		=> $twoWeeksVisitors,
				'twoWeeksIcon'    			=> $twoWeeksIcon,
				'dashboardMonthOfVisit'     => $dashboardMonthOfVisit,
				'monthLatinArray'			=> $monthLatinArray,
				'yearArray'					=> $yearArray,
				'allComments'				=> $allComments,
				'unreadComments'			=> $unreadComments,
				'allPosts'			     	=> $allPosts,
				'draftPosts'			    => $draftPosts,
				'oneMonthPosts'			    => $oneMonthPosts
			];
	    	$this->set($data);

	        if ($loggedInAdmin['level'] == 1) {
		    	$histories = $this->Histories->find('all', ['contain' => ['Admins']])->order(['Histories.id' => 'DESC'])->limit(6);
	    	}
	    	else {
		    	$histories = $this->Histories->find('all', ['contain' => ['Admins']])->where(['Admins.id' => $loggedInAdmin['id']])->order(['Histories.id' => 'DESC'])->limit(5);
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
            	$errors = $dashboardMonthOfVisit->errors();
            }

            $this->render();
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}