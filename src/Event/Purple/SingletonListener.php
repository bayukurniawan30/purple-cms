<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class SingletonListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Singleton.afterSave'       => 'afterSave',
            'Model.Singleton.afterSaveData'   => 'afterSaveData',
            'Model.Singleton.afterDelete'     => 'afterDelete',
            'Model.Singleton.afterDeleteData' => 'afterDeleteData',
        );
    }
    public function afterSave($event, $singleton, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Singleton';
            $detail = ' add '.$singleton['name'].' as a new singleton.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Singleton';
            $detail = ' update '.$singleton['name'].' data from singletons.';
        }

        $options = [
            'title'    => $title,
            'detail'   => $detail,
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
    public function afterSaveData($event, $singletonData, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Singleton Data in ' . $singletonData['name'];
            $detail = ' add a new singleton.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Singleton in ' . $singletonData['name'];
            $detail = ' update data from singletons.';
        }

        $options = [
            'title'    => $title,
            'detail'   => $detail,
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
    public function afterDelete($event, $singleton, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Singleton',
            'detail'   => ' delete '.$singleton.' from singletons.',
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
    public function afterDeleteData($event, $singletonData, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Data in ' . $singletonData['singleton_name'] . ' Singletons',
            'detail'   => ' delete one data from ' . $singletonData['singleton_name'] . ' singletons.',
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