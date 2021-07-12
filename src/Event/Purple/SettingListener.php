<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

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

        $tableHistories = TableRegistry::getTableLocator()->get('Histories');
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