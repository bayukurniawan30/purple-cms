<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class SubscribersTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('subscribers');
		$this->setPrimaryKey('id');
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// Sanitize email
		$entity->email = trim($entity->email);

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
	}
}