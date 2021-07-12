<?php
namespace App\Event\Purple;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class SubscriberListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Subscriber.afterSave'                    => 'afterSave',
            'Model.Subscriber.afterDelete'                  => 'afterDelete',
            'Model.Subscriber.afterUpdateMailchimpSettings' => 'afterUpdateMailchimpSettings',
        );
    }
    public function afterSave($event, $subscriber, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of New Subscriber';
            $detail = ' add '.$subscriber.' as a new subscriber.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a Subscriber';
            $detail = ' update '.$subscriber.' data from subscriber.';
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
    public function afterDelete($event, $subscriber, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a Subscriber',
            'detail'   => ' delete '.$subscriber.' from subscriber.',
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
    public function afterUpdateMailchimpSettings($event, $admin)
    {
        $options = [
            'title'    => 'Addition of Mailchimp API Key and Audience ID',
            'detail'   => ' add Mailchimp API key and Audience ID.',
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