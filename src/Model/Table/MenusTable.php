<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class MenusTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('menus');
		$this->setPrimaryKey('id');
		$this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
             ->setJoinType('INNER');
	    $this->belongsTo('Pages')
	 		 ->setForeignKey('page_id')
	         ->setJoinType('LEFT');
		$this->hasMany('Submenus')
 		     ->setForeignKey('menu_id');
	}
	public function beforeSave($event, $entity, $options)
    {
		$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// Sanitize and capitalize title
		$entity->title = ucwords(trim(strip_tags($entity->title)));

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
	public function fetchPublishedMenus($id = NULL) 
	{
		if ($id == NULL) {
			$menus = $this->find('all')->contain('Pages')->where(['Menus.status' => 1])->order(['Menus.ordering' => 'ASC']);
		}
		else {
			$menus = $this->find('all')->contain('Pages')->where(['Menus.status' => 1, 'Menus.id' => $id])->order(['Menus.ordering' => 'ASC'])->limit(1);
		}

		if ($menus->count() > 0) {
			$json  = [];
			$i = 0;
			$j = 0;
			foreach ($menus as $menu) {
				$json[$i]['id']      = $menu->id;
				$json[$i]['title']   = $menu->title;
				$json[$i]['has_sub'] = $menu->has_sub;
				$json[$i]['page']    = $menu->page_id;
				if ($menu->page_id == NULL) {
					$json[$i]['target']  = $menu->target;
				}
				else {
					$json[$i]['target']  = $menu->page->slug;
				}

				if ($menu->has_sub == 0) {
					$json[$i]['child'] = '';
				}
				else {
					$submenus = TableRegistry::get('Submenus')->find('all')->contain('Pages')->where(['Submenus.menu_id' => $menu->id, 'Submenus.status' => 1])->order(['Submenus.ordering' => 'ASC']);
					if ($submenus->count() == 0) {
						$json[$i]['child'] = '';
					} 
					else {
						$json[$i]['child'] = [];
						foreach ($submenus as $submenu) {
							$json[$i]['child'][$j]['id']     = $submenu->id;
							$json[$i]['child'][$j]['title']  = $submenu->title;
							$json[$i]['child'][$j]['page']   = $submenu->page_id;
							if ($submenu->page_id == NULL) {
								$json[$i]['child'][$j]['target'] = $submenu->target;
							}
							else {
								$json[$i]['child'][$j]['target'] = $submenu->page->slug;
							}

							$j++;
						}
					}
				}
				$i++;
			}

	    	return $json;
	   	}
	   	else {
	   		return false;
	   	}
	}
}