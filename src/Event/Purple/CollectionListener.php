<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class CollectionListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Collection.afterSave'       => 'afterSave',
            'Model.Collection.afterSaveData'   => 'afterSaveData',
            'Model.Collection.afterDelete'     => 'afterDelete',
            'Model.Collection.afterDeleteData' => 'afterDeleteData',
        );
    }
    public function afterSave($event, $collection, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Collection';
            $detail = ' add '.$collection['name'].' as a new collection.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Collection';
            $detail = ' update '.$collection['name'].' data from collections.';
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
    public function afterSaveData($event, $collectionData, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Collection Data in ' . $collectionData['name'];
            $detail = ' add a new collection.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Collection in ' . $collectionData['name'];
            $detail = ' update data from collections.';
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
    public function afterDelete($event, $collection, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Collection',
            'detail'   => ' delete '.$collection.' from collections.',
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
    public function afterDeleteData($event, $collectionData, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Data in ' . $collectionData['collection_name'] . ' Collections',
            'detail'   => ' delete one data from ' . $collectionData['collection_name'] . ' collections.',
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