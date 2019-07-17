<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class NotificationsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('notifications');
		$this->setPrimaryKey('id');
		$this->belongsTo('Comments')
		     ->setForeignKey('comment_id')
             ->setJoinType('LEFT');
        $this->belongsTo('Messages')
		     ->setForeignKey('message_id')
             ->setJoinType('LEFT');
        $this->belongsTo('Blogs')
		     ->setForeignKey('blog_id')
             ->setJoinType('LEFT');
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		if ($entity->isNew()) {
			$entity->created = $date;
			$entity->is_read = '0';
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