<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class MediasTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('medias');
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

		// Sanitize title and description
		$entity->title       = trim(strip_tags($entity->title));
		$entity->description = trim(strip_tags($entity->description));

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
		else {
			$entity->modified = $date;
		}
	}
}