<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class ThemeListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Theme.afterApply'  => 'afterApply',
            'Model.Theme.afterUpload' => 'afterUpload',
            'Model.Theme.afterDelete' => 'afterDelete',
        );
    }
    public function afterApply($event, $theme, $admin) 
    {
        $options = [
            'title'    => 'Applying a Theme',
            'detail'   => ' apply '.$theme.' as an active theme.',
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
    public function afterUpload($event, $theme, $admin) 
    {
        $options = [
            'title'    => 'Addition of a New Theme',
            'detail'   => ' upload '.$theme.' to themes.',
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
    public function afterDelete($event, $theme, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Theme',
            'detail'   => ' delete '.$theme.' from themes.',
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