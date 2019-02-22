<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class HistoriesTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('histories');
		$this->setPrimaryKey('id');
		$this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
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
		else {
			$entity->modified = $date;
		}
	}
	public function saveActivity($options) 
	{
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		$title  = $options['title'];
		$detail = $options['detail'];
		$admin  = $options['admin_id'];

		if ($admin > 1) {
			$history           = $this->newEntity();
			$history->title    = $title;
			$history->detail   = $detail;
			$history->created  = $date;
			$history->admin_id = $admin;
			
	        if ($this->save($history)) {
	        	return true;
	        }
	        else {
	        	return false;
	        }
	    }
	    else {
	    	return true;
	    }
	}
}