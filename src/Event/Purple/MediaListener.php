<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class MediaListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Media.afterSave'    => 'afterSave',
            'Model.Media.afterDelete'  => 'afterDelete',
        );
    }
    public function afterSave($event, $media, $admin, $type, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Media '.ucwords($type);
            $detail = ' add '.$media.' to media '.strtolower($type).'s.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Media '.ucwords($type);
            $detail = ' change '.$media.' data from media '.strtolower($type).'s.';
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
    public function afterDelete($event, $media, $admin, $type) 
    {
        $options = [
            'title'    => 'Deletion of a Media '.ucwords($type),
            'detail'   => ' delete '.$media.' from media '.strtolower($type).'s.',
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