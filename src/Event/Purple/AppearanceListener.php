<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class AppearanceListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Setting.afterUpdateAppearance' => 'afterUpdateAppearance',
            'Model.Setting.afterDeleteAppearance' => 'afterDeleteAppearance',
            'Model.Setting.afterUpdateFooter'     => 'afterUpdateFooter',
        );
    }
    public function afterUpdateAppearance($event, $setting, $admin, $type) 
    {
        $options = [
            'title'    => 'Change of '.ucwords($type),
            'detail'   => ' change the '.$type.' of the website.',
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
    public function afterDeleteAppearance($event, $setting, $admin, $type) 
    {
        $options = [
            'title'    => 'Delete '.ucwords($type),
            'detail'   => ' delete '.ucwords($type).' website.',
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
    public function afterUpdateFooter($event, $setting, $admin) 
    {
        $options = [
            'title'    => 'Update Footer Data',
            'detail'   => ' update footer content in website.',
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