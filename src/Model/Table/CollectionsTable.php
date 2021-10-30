<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class CollectionsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('collections');
		$this->setPrimaryKey('id');
		$this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
             ->setJoinType('INNER');
        $this->hasMany('CollectionDatas')
		     ->setForeignKey('collection_id');
    }
    public function beforeSave($event, $entity, $options)
    {
    	$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// Sanitize and capitalize name
		$entity->name = ucwords(trim(htmlentities(strip_tags($entity->name))));

        $sluggedTitle = Text::slug(strtolower($entity->name));
        // trim slug to maximum length defined in schema
        if ($entity->status != '2') {
            $entity->slug = substr($sluggedTitle, 0, 191);
        }

        if ($entity->connecting != '1') {
            $entity->connecting = '0';
        }

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
    public function total($status = NULL)
    {
        $collections = $this->find();
        if ($status == '0') {
            $collections->where(['status' => '0']);
        }
        elseif ($status == '1') {
            $collections->where(['status' => '1']);
        }
        elseif ($status == NULL) {
            $collections->where(['OR' => [['status' => '0'], ['status' => '1']]]);
        }

        return $collections->count();
    }
}