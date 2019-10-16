<?php
namespace App\Event\Purple;

use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectApi;

class SocialListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Social.afterSave'                 => 'afterSave',
            'Model.Social.afterReorder'              => 'afterReorder',
            'Model.Social.afterDelete'               => 'afterDelete',
            'Model.Social.afterUpdateSharingButtons' => 'afterUpdateSharingButtons',
        );
    }
    public function afterSave($event, $social, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of a New Social Media';
            $detail = ' add '.$social.' as a new social media.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Social Media';
            $detail = ' change '.$social.' url.';
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
    public function afterDelete($event, $social, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Social Media',
            'detail'   => ' delete '.$social.' from social media.',
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
    public function afterReorder($event, $admin) 
    {
        $options = [
            'title'    => 'Reorder Social Media',
            'detail'   => ' reorder all social media.',
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
    public function afterUpdateSharingButtons($event, $admin, $data) 
    {
        $options = [
            'title'    => 'Setting Change for Content Sharing Buttons',
            'detail'   => ' change content sharing buttons theme: '.$data['theme'].', font size: '.$data['fontSize'].', label: '.$data['label'].', count: '.$data['count'].'.',
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