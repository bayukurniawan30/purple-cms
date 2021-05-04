<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Http\Client;
use Cake\Cache\Cache;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class VisitorsTable extends Table
{
	public function initialize(array $config)
	{
        $this->setTable('visitors');
		$this->setPrimaryKey('id');
    }
    public function beforeSave($event, $entity, $options)
    {
        $serverRequest   = new ServerRequest();
        $session         = $serverRequest->getSession();
        $timezone        = $session->read('Purple.timezone');
        $settingTimezone = $session->read('Purple.settingTimezone');

        $date = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone($settingTimezone));
        if ($session->check('Purple.timezone')) {
            $date->setTimezone(new \DateTimeZone($timezone));
        }
        else {
            $date->setTimezone(new \DateTimeZone($settingTimezone));
        }
        $formattedDate = $date->format('Y-m-d');
        $formattedTime = $date->format('H:i:s');

        if ($entity->isNew()) {
            $entity->date_created = $formattedDate;
            $entity->time_created = $formattedTime;
        }
    }
    public function topCountries($limit = 10) 
    {
        $purpleGlobal = new PurpleProjectGlobal();
        $apiPath      = $purpleGlobal->apiDomain;
        
        $http = new Client();

        $countVisitors = $this->find()->count();
        $visitors      = $this->find()->select(['id', 'ip'])->toArray();

        if ($countVisitors > 0) {
            $newArray = [];
            $i = 0;
            foreach ($visitors as $key => $value) {
                if ($value['ip'] != '::1' && $value['ip'] != '127.0.0.1') {
                    if (($cache = Cache::read('visitor_' . $value['ip'])) === false) {
                        $response     = $http->get($apiPath . '/visitor/look-up/' . $value['ip']);
                        if ($response->isOk()) {
                            $verifyResult = $response->body();
                            $decodeResult = json_decode($verifyResult, true);
                            $data         = [
                                'country_code'    => $decodeResult['country_code'],
                                'country_name'    => $decodeResult['country_name'],
                                'city'            => $decodeResult['city'],
                                'zip'             => $decodeResult['zip'],
                                'flag'            => $decodeResult['location']['country_flag'],
                            ];
                            $country = $decodeResult['country_name']; 
                            $flag    = $decodeResult['location']['country_flag']; 

                            Cache::write('visitor_' . $value['ip'], json_encode($data));

                            array_push($newArray, $country . '::' . $flag);
                        }
                    }
                    else {
                        $cache       = Cache::read('visitor_' . $value['ip']);
                        $decodeCache = json_decode($cache, true);
                        $country     = $decodeCache['country_name'];
                        $flag        = $decodeCache['flag'];
                        array_push($newArray, $country . '::' . $flag);
                    }

                    $i++;
                }
            }

            $countValues = array_count_values($newArray);
            arsort($countValues);
            $frequentCountry = array_slice($countValues, 0, $limit, true);

            return $frequentCountry;
        }
        else {
            return false;
        }
    }
    public function totalAllVisitors() 
    {
        $totalVisitors = $this->find()->count();
        return $totalVisitors;
    }
    public function totalUniqueVisitors() 
    {
        $totalVisitors = $this->find('all',
                array('fields'=> array('DISTINCT date_created'))
            )->count();
        return $totalVisitors;
    }
    public function totalMobileVisitors() 
    {
        $totalVisitors = $this->find()->where(['OR' => [['device' => 'Phone'], ['device' => 'Tablet']]])->count();
        return $totalVisitors;
    }
    public function lastTwoWeeksTotalVisitors() 
    {
        $arrayDays = array();
        for ($day = 1; $day <= 14; $day++) {
            $data = date('Y-m-d', strtotime("-".$day." days"));

            $totalVisitors = $this->find();
            $totalVisitors->where(['date_created' => $data])->count();
            $arrayDays[] = $totalVisitors;
        }
        
        return array_reverse($arrayDays);
    }
    public function lastTwoWeeksBeforeTotalVisitors() 
    {
        $arrayDays = array();
        for ($day = 15; $day <= 28; $day++) {
            $data = date('Y-m-d', strtotime("-".$day." days"));

            $totalVisitors = $this->find();
            $totalVisitors->where(['date_created' => $data])->count();
            $arrayDays[] = $totalVisitors;
        }
        
        return array_reverse($arrayDays);
    }
    public function lastSixMonthVisitors() 
    {
    	$arrayMonth = array();
		for ($month = 1; $month <= 6; $month++) {
			$arrayMonth[] = strtoupper(date('M', strtotime("-".$month." month")));
		}
		return array_reverse($arrayMonth);
    }
    public function lastSixMonthTotalVisitors() 
    {
    	$arrayMonth = array();
		for ($month = 1; $month <= 6; $month++) {
			$data = date('Y-m', strtotime("-".$month." month"));
            $explodeData = explode('-', $data);
            
            $totalVisitors = $this->find();
            $dateYear  = 'YEAR(date_created)';
            $dateMonth = 'MONTH(date_created)';
            $totalVisitors->select([
                'yearCreated'  => $dateYear,
                'monthCreated' => $dateMonth
            ])
            ->having(['yearCreated' => $explodeData[0], 'monthCreated' => $explodeData[1]]);

            $arrayMonth[] = $totalVisitors->count();
        }
		
		return array_reverse($arrayMonth);
    }
    public function lastSixMonthTotalUniqueVisitors() 
    {
    	$arrayMonth = array();
		for ($month = 1; $month <= 6; $month++) {
			$data = date('Y-m', strtotime("-".$month." month"));
			$explodeData = explode('-', $data);

            $totalVisitors = $this->find();
            $dateYear  = 'YEAR(date_created)';
            $dateMonth = 'MONTH(date_created)';
            $totalVisitors->select([
                'yearCreated'  => $dateYear,
                'monthCreated' => $dateMonth
            ])
            ->distinct(['date_created'])
            ->having(['yearCreated' => $explodeData[0], 'monthCreated' => $explodeData[1]]);
			$arrayMonth[] = $totalVisitors->count();
        }
        
		return array_reverse($arrayMonth);
    }
    public function lastSixMonthTotalMobileVisitors() 
    {
        $arrayMonth = array();
        for ($month = 1; $month <= 6; $month++) {
            $data = date('Y-m', strtotime("-".$month." month"));
            $explodeData = explode('-', $data);

            $totalVisitors = $this->find();
            $dateYear  = 'YEAR(date_created)';
            $dateMonth = 'MONTH(date_created)';
            $totalVisitors->select([
                'yearCreated'  => $dateYear,
                'monthCreated' => $dateMonth
            ])
            ->having(['yearCreated' => $explodeData[0], 'monthCreated' => $explodeData[1]])
            ->where(['OR' => [['device' => 'Phone'], ['device' => 'Tablet']]]);
            $arrayMonth[] = $totalVisitors->count();
        }
        
        return array_reverse($arrayMonth);
    }
    public function countVisitorsInMonth($year = NULL, $month = NULL) 
    {
    	if ($year == NULL) {
    		$usedYear = date('Y');
    	}
    	else {
    		$usedYear = $year;
    	}

    	if ($month == NULL) {
    		$usedMonth = date('m');
    	}
    	else {
    		$usedMonth = $month;
    	}

        $totalVisitors = $this->find();
        $dateYear  = 'YEAR(date_created)';
        $dateMonth = 'MONTH(date_created)';
        $totalVisitors->select([
            'yearCreated'  => $dateYear,
            'monthCreated' => $dateMonth
        ])
        ->having(['yearCreated' => $usedYear, 'monthCreated' => $usedMonth]);

    	return $totalVisitors->count();
    }
    public function totalVisitorsDate($date) 
    {
        $totalVisitors = $this->find();
        $totalVisitors->where(['date_created' => $date]);
        return $totalVisitors->count();
    }
    public function totalUniqueVisitorsDate($date) 
    {
        $totalVisitors = $this->find('all', ['fields'=> array('DISTINCT date_created')]);
        $totalVisitors->where(['date_created' => $date]);
        return $totalVisitors->count();
    }
    public function totalMobileVisitorsDate($date) 
    {
        $totalVisitors = $this->find();
        $totalVisitors->where(['date_created' => $date, 'OR' => [['device' => 'Phone'], ['device' => 'Tablet']]]);
        return $totalVisitors->count();
    }
    public function visitorsPlatform($browser, $month = NULL, $year = NULL)
    {
        if ($year == NULL && $month == NULL) {
            $query = $this->find('all', [
                'conditions' => ['platform LIKE' => '%'.$browser.'%']
            ]);

            $totalVisitorsBrowser = $query->count();
        }
        else {
            $query = $this->find('all', [
                'conditions' => ['platform LIKE' => '%'.$browser.'%']
            ]);
            $dateYear  = 'YEAR(date_created)';
            $dateMonth = 'MONTH(date_created)';
            $query->select([
                'yearCreated'  => $dateYear,
                'monthCreated' => $dateMonth,
            ])
            ->having(['yearCreated' => $year, 'monthCreated' => $month]);

            if ($query->count() > 0) {
                $totalVisitorsBrowser = $query->count();
            }
            else {
                $totalVisitorsBrowser = 0;
            }
        }

        return $totalVisitorsBrowser;
    }
    public function checkVisitor($ip, $created, $browser, $platform, $device)
    {
        $purpleGlobal = new PurpleProjectGlobal();
        $apiPath      = $purpleGlobal->apiDomain;
        
        $http = new Client();

        if (($cache = Cache::read('visitor_' . $ip)) === false) {
            $response     = $http->get($apiPath . '/visitor/look-up/' . $ip);
            if ($response->isOk()) {
                $verifyResult = $response->getStringBody();
                $decodeResult = json_decode($verifyResult, true);
                $data         = [
                    'country_code'    => $decodeResult['country_code'],
                    'country_name'    => $decodeResult['country_name'],
                    'city'            => $decodeResult['city'],
                    'zip'             => $decodeResult['zip'],
                    'flag'            => $decodeResult['location']['country_flag'],
                ];

                Cache::write('visitor_' . $ip, json_encode($data));
            }
        }

        $year  = date('Y', strtotime($created));
        $month = date('m', strtotime($created));
        $date  = date('d', strtotime($created));
        $fullDate = $year.'-'.$month.'-'.$date;

        $query = $this->find();
        $query->where(['date_created' => $fullDate, 'ip' => $ip, 'browser' => $browser, 'platform' => $platform, 'device' => $device]);
        return $query->count(); 
    }
    public function isVisitorsEnough() {
        $totalAllVisitors = $this->find()->count();
        if ($totalAllVisitors == 50 || $totalAllVisitors == 100 || $totalAllVisitors == 500 || $totalAllVisitors == 1000 || $totalAllVisitors == 5000 || $totalAllVisitors == 20000 || $totalAllVisitors == 50000) { 
            return true;
        }
        elseif ($totalAllVisitors > 50000) {
            if ($totalAllVisitors % 50000 == 0) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
}