<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class PageListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Page.afterSave'    => 'afterSave',
            'Model.Page.changeStatus' => 'changeStatus',
            'Model.Page.afterDelete'  => 'afterDelete',
        );
    }
    public function afterSave($event, $page, $admin, $type, $save = 'new') 
    {
        if ($type == 'homepage') {
            $title  = 'Update Home Page Content';
            $detail = ' update home page content of your website.';

        }
        elseif ($type == 'general') {
            if ($save == 'new') {
                $title  = 'Content Making of a Page';
                $detail = ' add content in '.$page.'.';
            }
            elseif ($save == 'update') {
                $title  = 'Content Update of a Page';
                $detail = ' update content in '.$page.'.';
            }
        }
        elseif ($type == 'blog') {
            $title  = 'Data Change of a Page';
            $detail = ' change page title from '.$page['title'].' to '.$page['newTitle'].'.';
        }
        elseif ($type == 'custom') {
            if ($save == 'new') {
                $title  = 'Content Making of a Page';
                $detail = ' add custom code in '.$page.'.';
            }
            elseif ($save == 'update') {
                $title  = 'Content Update of a Page';
                $detail = ' update custom code in '.$page.'.';
            }
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
    public function changeStatus($event, $page, $admin, $type, $status) 
    {
        $options = [
            'title'    => 'Change Status of a Page',
            'detail'   => ' change status '.$page.' in pages to '.$status.'.',
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
    public function afterDelete($event, $page, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Page',
            'detail'   => ' delete '.$page.' from pages.',
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