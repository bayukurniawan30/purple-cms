<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Log\Log;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class BlogsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('blogs');
		$this->setPrimaryKey('id');
		$this->hasMany('BlogLikes')
		     ->setForeignKey('blog_id');
        $this->hasMany('BlogVisitors')
		     ->setForeignKey('blog_id');
        $this->hasMany('Comments', [
			    'dependent' => true,
			    'cascadeCallbacks' => true,
			 ])
		     ->setForeignKey('blog_id')
             ->setJoinType('INNER');
		$this->hasMany('Notifications')
		     ->setForeignKey('blog_id');
	    $this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
             ->setJoinType('INNER');
        $this->belongsTo('BlogCategories')
		     ->setForeignKey('blog_category_id')
             ->setJoinType('INNER');
        $this->belongsTo('BlogTypes')
		     ->setForeignKey('blog_type_id')
             ->setJoinType('INNER');
        $this->hasMany('BlogVisitors', [
			    'dependent' => true,
			    'cascadeCallbacks' => true,
			 ])
 		     ->setForeignKey('blog_id')
             ->setJoinType('INNER');
        $this->belongsToMany('Tags', [
			'joinTable'        => 'blogs_tags',
			'foreignKey'       => 'blog_id',
			'targetForeignKey' => 'tag_id'
        ]);
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// if (!$entity->slug) {
	        $sluggedTitle = Text::slug(strtolower($entity->title));
	        // trim slug to maximum length defined in schema
	        $entity->slug = substr($sluggedTitle, 0, 191);
		// }

		// Sanitize title and content
		$entity->title   = trim(strip_tags($entity->title));
		$entity->content = trim(htmlentities($entity->content));

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
		else {
			$entity->modified = $date;
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
    protected function _getModified($modified)
    {
        if ($modified == NULL) {
            return $modified;
        }
        else {
            $serverRequest   = new ServerRequest();
            $session         = $serverRequest->getSession();
            $timezone        = $session->read('Purple.timezone');
            $settingTimezone = $session->read('Purple.settingTimezone');

            $date = new \DateTime($modified, new \DateTimeZone($settingTimezone));
            $date->setTimezone(new \DateTimeZone($timezone));
            return $date->format('Y-m-d H:i:s');
        }
	}
	public function findTagged(Query $query, array $options)
	{
		$columns = [
			'Blogs.id', 'Blogs.admin_id', 'Blogs.title', 'Blogs.content', 'Blogs.blog_type_id', 'Blogs.blog_category_id', 'Blogs.comment', 'Blogs.featured', 'Blogs.status', 'blogs.created', 'Blogs.slug', 'Blogs.meta_keywords', 'Blogs.meta_description', 'Blogs.social_share'
		];

		$query = $query->select($columns)->distinct($columns);
		
		if (empty($options['tags'])) {
			// If there are no tags provided, find articles that have no tags.
			$query->leftJoinWith('Tags')->where(['Tags.title IS' => null]);
		} 
		else {
			// Find articles that have one or more of the provided tags.
			$query->innerJoinWith('Tags')->where(['Tags.slug IN' => $options['tags']]);
		}

		return $query->group(['Blogs.id']);
	}
	public function taggedPosts($tagId)
	{
		$blogs  = $this->find()->contain('BlogCategories')->contain('Admins');
		$blogs->matching('Tags', function ($q) use ($tagId) {
		    return $q->where(['Tags.id' => $tagId]);
		});
		return $blogs;
	}
	public function archivesList($page = NULL)
	{
		$find  = $this->find('all')->contain('BlogCategories');
		$query = $find->select($this)->select(['pcount' => $find->func()->count('Blogs.id')])->group(["DATE_FORMAT(Blogs.created, '%Y-%m') DESC"]);
		if ($page != NULL) {
			$query = $query->where(['BlogCategories.page_id' => $page]);
		}
		
		return $query;
	}
	public function fetchPosts($limit, $return = 'fetch')
	{
		$blogs = $this->Blogs->find('all', [
                'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories')->contain('Admins')->where(['Blogs.status' => '1'])->limit($limit);

		if ($return == 'count') {
			return $blogs->count();
		}
		elseif($return == 'fetch') {
			return $blogs;
		}
	}
	public function dashboardStatistic($status = 'all')
    {
        $query = $this->find('all')->contain(['Admins']);
        if ($status == 'publish') {
            $query->where(['Blogs.status' => '1']);
        }
        elseif ($status == 'draft') {
            $query->where(['Blogs.status' => '0']);
        }
        elseif ($status == 'all') {
            $query->where(['Blogs.id >' => '0']);
        }

        return $query->count();
    }
    public function lastMonthTotalPosts() 
    {
        $arrayDays = array();
        for ($day = 1; $day <= 30; $day++) {
            $data = date('Y-m-d', strtotime("-".$day." days"));

            $totalPosts = $this->find()->where(['DATE(created)' => $data, 'status' => '1'])->count();
            $arrayDays[] = $totalPosts;
        }
        
        $total = array_sum($arrayDays);
        return $total;
    }
}