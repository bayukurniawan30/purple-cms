<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class NavigationListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Navigation.afterSave'    => 'afterSave',
            'Model.Navigation.afterDelete'  => 'afterDelete',
            'Model.Navigation.afterReorder' => 'afterReorder',
        );
    }
    public function afterSave($event, $navigation, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Navigation';
            $detail = ' add '.$navigation['title'].' as a new navigation.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Navigation';
            $detail = ' change '.$navigation['title'].' data.';
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
    public function afterDelete($event, $navigation, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Navigation',
            'detail'   => ' delete '.$navigation['title'].' from navigation.',
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
    public function afterReorder($event, $admin, $type, $parent = NULL) 
    {
        if ($parent == NULL) {
            $detail = ' reorder navigation '.$type.'.';
        }
        else {
            $detail = ' reorder navigation '.$type.' under '.$parent['title'].'.';
        }

        $options = [
            'title'    => 'Reorder Navigation '.ucwords($type),
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
}