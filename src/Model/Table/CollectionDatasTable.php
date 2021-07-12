<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class CollectionDatasTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('collection_datas');
		$this->setPrimaryKey('id');
		$this->belongsTo('Collections')
		     ->setForeignKey('collection_id')
             ->setJoinType('INNER');
        $this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
             ->setJoinType('INNER');
    }
    public function beforeSave($event, $entity, $options)
    {
    	$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// Sanitize name, email, and content
		$entity->name    = trim(strip_tags($entity->name));
		$entity->email   = trim($entity->email);

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
}