<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;

class VisitorsController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $purpleGlobal = new PurpleProjectGlobal();
        $databaseInfo   = $purpleGlobal->databaseInfo();
        if ($databaseInfo == 'default') {
            throw new NotFoundException(__('Page not found'));
        }
        else {
            $purpleSettings = new PurpleProjectSettings();
            $maintenance    = $purpleSettings->maintenanceMode();
            $userLoggedIn   = $purpleSettings->checkUserLoggedIn();

            if ($maintenance == 'enable' && $userLoggedIn == false) {
                throw new NotFoundException(__('Page not found'));
            }
        }
    }
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');

        $this->loadModel('Visitors');
        $this->loadModel('Admins');
        $this->loadModel('Settings');
        $this->loadModel('Histories');

        $this->viewBuilder()->enableAutoLayout(false);

        $purpleGlobal = new PurpleProjectGlobal();
		$protocol     = $purpleGlobal->protocol();

        $data = [
            'baseUrl' => $protocol . $this->request->host() . $this->request->getAttribute("webroot")
        ];

        $this->set($data);
    }
    public function view()
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $purpleGlobal = new PurpleProjectGlobal();

                // Query string for additional condition
                $from = $this->request->getQuery('from'); // Y-m-d
                $to   = $this->request->getQuery('to'); // Y-m-d

                if ($from !== NULL && $to !== NULL) {
                    $explodeFrom = explode('-', $from);
                    $explodeTo   = explode('-', $to);

                    if (checkdate($explodeFrom[1], $explodeFrom[2], $explodeFrom[0]) && checkdate($explodeTo[1], $explodeTo[2], $explodeTo[0])) {
                        $begin = new \DateTime($from);
                        $end   = new \DateTime($to);
                        $end->modify('+1 day');

                        $interval = \DateInterval::createFromDateString('1 day');
                        $period   = new \DatePeriod($begin, $interval, $end);

                        $visitors       = 0;
                        $mobileVisitors = 0;
                        foreach ($period as $dt) {
                            $dateIntv       = $dt->format("Y-m-d");
                            $visitors       += $this->Visitors->totalVisitorsDate($dateIntv);
                            $mobileVisitors += $this->Visitors->totalMobileVisitorsDate($dateIntv);
                        }

                        $desktopVisitors = $visitors - $mobileVisitors;
                        
                        $return = [
                            'status'           => 'ok',
                            'error'            => NULL,
                            'from'             => $from,
                            'to'               => $to,
                            'total_visitors'   => [
                                'original'  => $visitors,
                                'formatted' => $purpleGlobal->shortenNumber($visitors)   
                            ],
                            'mobile_visitors'  => [
                                'original'  => $mobileVisitors,
                                'formatted' => $purpleGlobal->shortenNumber($mobileVisitors)  
                            ],
                            'desktop_visitors' => [
                                'original'  => $desktopVisitors,
                                'formatted' => $purpleGlobal->shortenNumber($desktopVisitors)  
                            ]
                        ];
                    }
                    else {
                        $return = [
                            'status' => 'error',
                            'error'  => 'Invalid date format'
                        ];
                    }
                }
                else {
                    $visitors        = $this->Visitors->totalAllVisitors();
                    $mobileVisitors  = $this->Visitors->totalMobileVisitors();
                    $desktopVisitors = $visitors - $mobileVisitors;

                    $return = [
                        'status'           => 'ok',
                        'error'            => NULL,
                        'total_visitors'   => [
                            'original'  => $visitors,
                            'formatted' => $purpleGlobal->shortenNumber($visitors)   
                        ],
                        'mobile_visitors'  => [
                            'original'  => $mobileVisitors,
                            'formatted' => $purpleGlobal->shortenNumber($mobileVisitors)  
                        ],
                        'desktop_visitors' => [
                            'original'  => $desktopVisitors,
                            'formatted' => $purpleGlobal->shortenNumber($desktopVisitors)  
                        ]
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

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