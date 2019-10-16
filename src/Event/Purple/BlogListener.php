<?php
namespace App\Event\Purple;

use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectApi;

class BlogListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Blog.afterSave'   => 'afterSave',
            'Model.Blog.afterDelete' => 'afterDelete',
        );
    }
    public function afterSave($event, $blog, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Post';
            $detail = ' add '.$blog['title'].' as a new post.';

        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Post';
            $detail = ' update '.$blog['title'].' data from posts.';
        }

        $options = [
            'title'    => $title,
            'detail'   => $detail,
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
    public function afterDelete($event, $blog, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Post',
            'detail'   => ' delete '.$blog['title'].' from posts.',
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