<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class BlogVisitorsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('blog_visitors');
		$this->setPrimaryKey('id');
		$this->belongsTo('Blogs')
		     ->setForeignKey('blog_id')
             ->setJoinType('INNER');
    }
    public function beforeSave($event, $entity, $options)
    {
    	$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
	}
	protected function _getCreated($created)
    {
        $serverRequest   = new ServerRequest();
        $session         = $serverRequest->getSession();
        $timezone        = $session->read('Purple.timezone');
        $settingTimezone = $session->read('Purple.settingTimezone');

        $date = new \DateTime($created, new \DateTimeZone($settingTimezone));
        $date->setTimezone(new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }
	public function checkVisitor($ip, $created, $blogId)
	{
		$year  = date('Y', strtotime($created));
		$month = date('m', strtotime($created));
		$date  = date('d', strtotime($created));
		$fullDate = $year.'-'.$month.'-'.$date;

		$query = $this->find()->where(['DATE(created)' => $fullDate, 'ip' => $ip]);
		return $query->count(); 
	}
	public function totalVisitors($blogId) 
	{
		$query = $this->find()->where(['blog_id' => $blogId]);
		return $query->count();
	}
	public function lastTwoWeeksVisitors() 
    {
    	$arrayDays = array();
		for ($day = 1; $day <= 14; $day++) {
			$arrayDays[] = strtoupper(date('j M', strtotime("-".$day." days")));
		}
		return array_reverse($arrayDays);
    }
    public function lastTwoWeeksTotalVisitors($blogId) 
    {
    	$arrayDays = array();
		for ($day = 1; $day <= 14; $day++) {
			$data = date('Y-m-d', strtotime("-".$day." days"));

	    	$totalVisitors = $this->find()->where(['DATE(created)' => $data, 'blog_id' => $blogId])->count();
			$arrayDays[] = $totalVisitors;
		}
		
		return array_reverse($arrayDays);
    }
    public function totalVisitorsDate($blogId, $date) 
    {
        $totalVisitors = $this->find()->where(['blog_id' => $blogId, 'DATE(created)' => $date])->count();
        return $totalVisitors;
    }
}