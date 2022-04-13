<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
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
        $settingTimezone = $session->check('Purple.settingTimezone') ? $session->read('Purple.settingTimezone') : PurpleProjectSettings::timezone();

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
		$dateYear  = 'YEAR(created)';
		$dateMonth = 'MONTH(created)';
		$dateDay   = 'DAY(created)';
		$query->select([
			'yearCreated'  => $dateYear,
			'monthCreated' => $dateMonth,
			'dayCreated'   => $dateDay,
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
			$dateYear  = 'YEAR(created)';
			$dateMonth = 'MONTH(created)';
			$dateDay   = 'DAY(created)';
			$totalVisitors->select([
				'yearCreated'  => $dateYear,
				'monthCreated' => $dateMonth,
				'dayCreated'   => $dateDay,
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
		$dateYear  = 'YEAR(created)';
		$dateMonth = 'MONTH(created)';
		$dateDay   = 'DAY(created)';
		$totalVisitors->select([
			'yearCreated'  => $dateYear,
			'monthCreated' => $dateMonth,
			'dayCreated'   => $dateDay,
			'blog_id'
		])
		->having(['yearCreated' => $explodeDate[0], 'monthCreated' => $explodeDate[1], 'dayCreated' => $explodeDate[2], 'blog_id' => $blogId]);

        return $totalVisitors->count();
	}
	public function topBlogs($limit = 10)
	{
        $countVisitors = $this->find()->count();
		$visitors      = $this->find()->select(['ip', 'blog_id'])->toArray();

		if ($countVisitors > 0) {
			$newArray = [];
			$i = 0;
			foreach ($visitors as $key => $value) {
				if ($value['ip'] != '::1' && $value['ip'] != '127.0.0.1') {
					array_push($newArray, $value['blog_id']);
				}
			}

			$countValues   = array_count_values($newArray);
			arsort($countValues);
			$frequentBlogs = array_slice($countValues, 0, $limit, true);
			
			$blogsTable = TableRegistry::getTableLocator()->get('Blogs');
			$blogsArray = [];
			$j = 0;
			foreach ($frequentBlogs as $key => $value) {
				$blog = $blogsTable->find('all')->contain('BlogCategories')->where(['Blogs.id' => $key])->first();
				$blogTitle    = $blog->title;
				$blogSlug     = $blog->slug;
				$blogCreated  = $blog->created;
				$blogCategory = $blog->blog_category->name;

				$blogsArray[$j] = ['title' => $blogTitle, 'slug' => $blogSlug, 'created' => $blogCreated, 'category' => $blogCategory, 'total' => $value];
				$j++;
			}

			return $blogsArray;
		}
		else {
			return false;
		}
	}
}