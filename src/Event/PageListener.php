<?php
namespace App\Event;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectApi;

class PageListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Notification.afterSaveContactMessage' => 'afterSaveContactMessage',
            'Model.Page.afterSentContactMessage'         => 'afterSentContactMessage'
        );
    }
    public function afterSentContactMessage($event, $data)
    {
        $tableAdmins   = TableRegistry::getTableLocator()->get('Admins');
        $tableSettings = TableRegistry::getTableLocator()->get('Settings');
        
        // Send Email to User to Notify user
        $users     = $tableAdmins->find()->where(['username <> ' => 'creatifycore'])->order(['id' => 'ASC']);
        $totalUser = $users->count();

        $emailStatus = [];
        $counter = 0;
        foreach ($users as $user) {
            $key           = $tableSettings->settingsPublicApiKey();
            $dashboardLink = $data['link'];
            $userData      = array(
                'sitename'    => $tableSettings->settingsSiteName(),
                'email'       => $user->email,
                'displayName' => $user->display_name,
                'level'       => $user->level
            );
            $senderData   = array(
                'subject' => $data['subject'],
                'name'    => $data['name'],
                'email'   => $data['email'],
                'domain'  => $data['domain']
            );

            $purpleApi  = new PurpleProjectApi();
            $notifyUser = $purpleApi->sendEmailContactMessage($key, $dashboardLink, json_encode($userData), json_encode($senderData));

            if ($notifyUser == true) {
                $counter++;
                $emailStatus[$user->email] = true; 
            }
            else {
                $emailStatus[$user->email] = false; 
            }
        }

        $result = $emailStatus;
        $event->setResult($result);
    } 
    public function afterSaveContactMessage($event, $data)
    {
        $tableNotifications = TableRegistry::getTableLocator()->get('Notifications');

        // Save notification
        $notification = $tableNotifications->newEntity();
        $notification->type       = 'message';
        $notification->content    = $data['name'].' sent a message to you. Click to view the message.';
        $notification->message_id = $data['message_id'];

        if ($tableNotifications->save($notification)) {
            $result = true;
        }
        else {
            $result = false;
        }
        
        $event->setResult($result);
    }
}