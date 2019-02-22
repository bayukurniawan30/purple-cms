<?php

namespace App\Model\Table;

use Cake\ORM\Table;
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
	public function checkVisitor($ip, $created, $blog_id)
	{
		$year  = date('Y', strtotime($created));
		$month = date('m', strtotime($created));
		$date  = date('d', strtotime($created));
		$fullDate = $year.'-'.$month.'-'.$date;

		$query = $this->find()->where(['DATE(created)' => $fullDate, 'ip' => $ip]);
		return $query->count(); 
	}
	public function totalVisitors($blog_id) 
	{
		$query = $this->find()->where(['blog_id' => $blog_id]);
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
    public function lastTwoWeeksTotalVisitors($blog_id) 
    {
    	$arrayDays = array();
		for ($day = 1; $day <= 14; $day++) {
			$data = date('Y-m-d', strtotime("-".$day." days"));

	    	$totalVisitors = $this->find()->where(['DATE(created)' => $data, 'blog_id' => $blog_id])->count();
			$arrayDays[] = $totalVisitors;
		}
		
		return array_reverse($arrayDays);
    }
    public function totalVisitorsDate($blog_id, $date) 
    {
        $totalVisitors = $this->find()->where(['blog_id' => $blog_id, 'DATE(created)' => $date])->count();
        return $totalVisitors;
    }
}