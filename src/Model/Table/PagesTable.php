<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class PagesTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('pages');
		$this->setPrimaryKey('id');
		$this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
             ->setJoinType('INNER');
        $this->belongsTo('PageTemplates')
		     ->setForeignKey('page_template_id')
             ->setJoinType('INNER');
        $this->hasOne('Generals', [
			    'dependent' => true,
			    'cascadeCallbacks' => true,
			 ])
             ->setForeignKey('page_id')
             ->setJoinType('INNER');
        $this->hasOne('CustomPages', [
			    'dependent' => true,
			    'cascadeCallbacks' => true,
			 ])
             ->setForeignKey('page_id')
             ->setJoinType('INNER');
        $this->hasOne('Menus', [
			    'dependent' => true,
			    'cascadeCallbacks' => true,
			 ])
		     ->setForeignKey('page_id')
	         ->setJoinType('LEFT');
	    $this->hasOne('Submenus', [
			    'dependent' => true,
			    'cascadeCallbacks' => true,
			 ])
		     ->setForeignKey('page_id')
	         ->setJoinType('LEFT');
	}
    public function beforeSave($event, $entity, $options)
    {
    	$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// Sanitize and capitalize title
		$entity->title = ucwords(trim(strip_tags($entity->title)));

		// if (!$entity->slug) {
	        $sluggedTitle = Text::slug(strtolower($entity->title));
	        // trim slug to maximum length defined in schema
	        $entity->slug = substr($sluggedTitle, 0, 191);
		// }

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
}