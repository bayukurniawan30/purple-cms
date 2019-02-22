<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class MessagesTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('messages');
		$this->setPrimaryKey('id');
		$this->hasMany('Notifications')
		     ->setForeignKey('blog_id');
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		if ($entity->isNew()) {
			$entity->created  = $date;
		}

		$entity->type = 'contact';	
	}
}