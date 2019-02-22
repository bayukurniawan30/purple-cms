<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class BlogCategoriesTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('blog_categories');
		$this->setPrimaryKey('id');
		$this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
             ->setJoinType('INNER');
        $this->belongsTo('Pages')
	 		 ->setForeignKey('page_id')
	         ->setJoinType('LEFT');
        $this->hasMany('Blogs')
		     ->setForeignKey('blog_category_id');
    }
    public function beforeSave($event, $entity, $options)
    {
    	$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// Sanitize and capitalize name
		$entity->name = ucwords(trim(htmlentities(strip_tags($entity->name))));

		// if (!$entity->slug) {
	        $sluggedTitle = Text::slug(strtolower($entity->name));
	        // trim slug to maximum length defined in schema
	        $entity->slug = substr($sluggedTitle, 0, 191);
		// }

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
		else {
			$entity->modified = $date;
		}
	}
}