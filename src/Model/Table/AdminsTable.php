<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class AdminsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('admins');
		$this->setPrimaryKey('id');
		$this->hasMany('Media')
		     ->setForeignKey('admin_id');
        $this->hasMany('MediaDocs')
		     ->setForeignKey('admin_id');
        $this->hasMany('Menus')
		     ->setForeignKey('admin_id');
        $this->hasMany('CustomPages')
		     ->setForeignKey('admin_id');
        $this->hasMany('Pages')
		     ->setForeignKey('admin_id');
        $this->hasMany('Generals')
		     ->setForeignKey('admin_id');
		$this->hasMany('Blogs')
		     ->setForeignKey('admin_id');
	    $this->hasMany('BlogCategories')
		     ->setForeignKey('admin_id');
	    $this->hasMany('Comments')
		     ->setForeignKey('admin_id');
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		$entity->display_name = ucwords(trim($entity->display_name));
		$entity->username     = trim(strtolower($entity->username));
		$entity->email        = trim(strtolower($entity->email));

		if ($entity->isNew()) {
			$entity->created  = $date;
		}
		else {
			$entity->modified = $date;
			
			if ($entity->photo == '' || $entity->photo == 'NULL') {
				$entity->photo = NULL;
            }
            else {
                $entity->photo = trim($entity->photo);
            }
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
	protected function _getLastLogin($lastLogin)
    {
        if ($lastLogin == NULL) {
            return $lastLogin;
        }
        else {
            $serverRequest   = new ServerRequest();
            $session         = $serverRequest->getSession();
            $timezone        = $session->read('Purple.timezone');
            $settingTimezone = $session->read('Purple.settingTimezone');

            $date = new \DateTime($lastLogin, new \DateTimeZone($settingTimezone));
            $date->setTimezone(new \DateTimeZone($timezone));
            return $date->format('Y-m-d H:i:s');
        }
    }
	public function lastUser()
	{
		$admin = $this->find()->all()->last();
		return $admin;
	}
}