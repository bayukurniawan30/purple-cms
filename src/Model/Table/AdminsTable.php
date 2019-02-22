<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class AdminsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('admins');
		$this->setPrimaryKey('id');
		$this->hasMany('Media')
		     ->setForeignKey('admin_id');
        $this->hasMany('MediaDocs')
		     ->setForeignKey('admin_id');
        $this->hasMany('Menus')
		     ->setForeignKey('admin_id');
        $this->hasMany('CustomPages')
		     ->setForeignKey('admin_id');
        $this->hasMany('Pages')
		     ->setForeignKey('admin_id');
        $this->hasMany('Generals')
		     ->setForeignKey('admin_id');
		$this->hasMany('Blogs')
		     ->setForeignKey('admin_id');
	    $this->hasMany('BlogCategories')
		     ->setForeignKey('admin_id');
	    $this->hasMany('Comments')
		     ->setForeignKey('admin_id');
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		$entity->display_name = ucwords(trim($entity->display_name));
		$entity->username     = trim(strtolower($entity->username));
		$entity->email        = trim(strtolower($entity->email));

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
		else {
			$entity->modified = $date;
		}
	}
	public function lastUser()
	{
		$admin = $this->find()->all()->last();
		return $admin;
	}
}