<?php
namespace App\Event\Purple;

use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectApi;

class SettingListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Setting.afterSave' => 'afterSave',
        );
    }
    public function afterSave($event, $setting, $admin) 
    {
        $options = [
            'title'    => 'Change of Setting',
            'detail'   => ' change '.$setting['name'].' setting.',
            'admin_id' => $admin['id']
        ];

        $tableHistories = TableRegistry::get('Histories');
        $saveActivity   = $tableHistories->saveActivity($options);
        if ($saveActivity) {
            $result = true;
        }
        else {
            $result = false;
        }
        
        $event->setResult($result);
    }
}