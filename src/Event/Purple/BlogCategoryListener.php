<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class BlogCategoryListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.BlogCategory.afterSave'    => 'afterSave',
            'Model.BlogCategory.afterDelete'  => 'afterDelete',
            'Model.BlogCategory.afterReorder' => 'afterReorder',
        );
    }
    public function afterSave($event, $category, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Post Category';
            $detail = ' add '.$category['name'].' as a new post category.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Post Category';
            $detail = ' update '.$category['name'].' data from post category.';
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
    public function afterDelete($event, $category, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Post Category',
            'detail'   => ' delete '.$category.' from post category.',
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
    public function afterReorder($event, $admin) 
    {
        $options = [
            'title'    => 'Reorder Post Categories',
            'detail'   => ' reorder post category.',
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