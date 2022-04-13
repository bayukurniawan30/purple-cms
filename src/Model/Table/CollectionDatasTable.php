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
    public function total($collectionId = NULL)
    {
        $data = $this->find()->contain('Collections');
        if ($collectionId != NULL) {
            $data->where(['CollectionDatas.collection_id' => $collectionId]);
        }
        else {
            $data->where(['OR' => [['Collections.status' => '0'], ['Collections.status' => '1']]]);
        }

        return $data->count();
    }
}