<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
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
				$query     = $this->find()->contain('Admins')->where(['Histories.created' => $date])->limit(1);
				$adminName = $query->first()->admin->display_name;

				// Write activity to debug
				$content = ucwords($adminName) . ' => ' . 'Date:' . $date . '. Title: ' . $title . '. Activity: ' . ucwords($adminName) . $detail;
				Log::info($content, 'purple');

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