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

		$query     = $this->find();
		$dateYear  = $query->func()->extract('YEAR', 'created');
		$dateMonth = $query->func()->extract('MONTH', 'created');
		$dateDay   = $query->func()->extract('DAY', 'created');
		$query->select([
			'yearCreated'  => $dateYear,
			'monthCreated' => $dateMonth,
			'dayCreated'   => $dateMonth,
			'ip'
		])
		->having(['yearCreated' => $year, 'monthCreated' => $month, 'dayCreated' => $date, 'ip' => $ip]);

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
			$explodeDate = explode('-', $data);

			$totalVisitors = $this->find();
			$dateYear  = $totalVisitors->func()->extract('YEAR', 'created');
			$dateMonth = $totalVisitors->func()->extract('MONTH', 'created');
			$dateDay   = $totalVisitors->func()->extract('DAY', 'created');
			$totalVisitors->select([
				'yearCreated'  => $dateYear,
				'monthCreated' => $dateMonth,
				'dayCreated'   => $dateMonth,
				'blog_id'
			])
			->having(['yearCreated' => $explodeDate[0], 'monthCreated' => $explodeDate[1], 'dayCreated' => $explodeDate[2], 'blog_id' => $blogId]);
			$arrayDays[] = $totalVisitors->count();
		}
		
		return array_reverse($arrayDays);
    }
    public function totalVisitorsDate($blogId, $date) 
    {
		$explodeDate = explode('-', $date);

		$totalVisitors = $this->find();
		$dateYear  = $totalVisitors->func()->extract('YEAR', 'created');
		$dateMonth = $totalVisitors->func()->extract('MONTH', 'created');
		$dateDay   = $totalVisitors->func()->extract('DAY', 'created');
		$totalVisitors->select([
			'yearCreated'  => $dateYear,
			'monthCreated' => $dateMonth,
			'dayCreated'   => $dateMonth,
			'blog_id'
		])
		->having(['yearCreated' => $explodeDate[0], 'monthCreated' => $explodeDate[1], 'dayCreated' => $explodeDate[2], 'blog_id' => $blogId]);

        return $totalVisitors->count();
    }
}