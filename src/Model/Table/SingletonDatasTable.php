<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class SingletonDatasTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('singleton_datas');
		$this->setPrimaryKey('id');
		$this->belongsTo('Singletons')
		     ->setForeignKey('singleton_id')
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

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
    }
    protected function _getCreated($created)
    {
        $serverRequest   = new ServerRequest();
        $session         = $serverRequest->getSession();
        $timezone        = $session->read('Purple.timezone');
        $settingTimezone = $session->check('Purple.settingTimezone') ? $session->read('Purple.settingTimezone') : PurpleProjectSettings::timezone();

        $date = new \DateTime($created, new \DateTimeZone($settingTimezone));
        $date->setTimezone(new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }
    public function total($singletonId = NULL)
    {
        $data = $this->find()->contain('Singletons');
        if ($singletonId != NULL) {
            $data->where(['Singletons.singleton_id' => $singletonId, 'OR' => [['Singletons.status' => '0'], ['Singletons.status' => '1']]]);
        }
        else {
            $data->where(['OR' => [['Singletons.status' => '0'], ['Singletons.status' => '1']]]);
        }

        return $data->count();
    }
}