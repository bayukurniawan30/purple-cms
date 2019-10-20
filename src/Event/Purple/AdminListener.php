<?php
namespace App\Event\Purple;

use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectApi;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class AdminListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Admin.checkLastSignIn'         => 'checkLastSignIn',
            'Model.Admin.afterSignIn'             => 'afterSignIn',
            'Model.Admin.afterSignOut'            => 'afterSignOut',
            'Model.Admin.sendEmailForgotPassword' => 'sendEmailForgotPassword',
            'Model.Admin.sendEmailResetPassword'  => 'sendEmailResetPassword',
            'Model.Admin.afterSave'               => 'afterSave',
            'Model.Admin.afterUpdatePassword'     => 'afterUpdatePassword',
            'Model.Admin.afterDelete'             => 'afterDelete',
        );
    }
    public function checkLastSignIn($event, $admin, $data)
    {
        $purpleSettings = new PurpleProjectSettings();
        $timezone       = $purpleSettings->timezone();

        $lastLogin = Carbon::parse($data['last_login']);
        $now       = Carbon::now($timezone);
        $diff      = $lastLogin->diffInDays($now);

        if ($diff >= 7) {
            $serverRequest = new ServerRequest();
            $session       = $serverRequest->getSession();

            // Generate random code
            $randomCode = rand(100000, 999999);
            $session->write('Admin.verify', $randomCode);

            $tableSettings = TableRegistry::get('Settings');
            $key           = $tableSettings->settingsPublicApiKey();
            $userData = array(
                'sitename'    => $tableSettings->settingsSiteName(),
                'username'    => $admin['username'],
                'email'       => $admin['email'],
                'displayName' => $admin['display_name'],
                'level'       => $admin['level'],
                'code'        => $randomCode
            );
            $senderData = array(
                'name'   => $admin['display_name'],
                'domain' => $data['domain']
            );

            $purpleApi  = new PurpleProjectApi();
            $notifyUser = $purpleApi->sendEmailSignInVerification($key, json_encode($userData), json_encode($senderData));
            if ($notifyUser) {
                $result = ['diff' => $diff, $admin['email'] => true];
            }
            else {
                $result = ['diff' => $diff, $admin['email'] => false];
            }
        }
        else {
            $result = ['diff' => $diff];
        }

        $event->setResult($diff);
    }
    public function afterSignIn($event, $admin) 
    {
        $options = [
            'title'    => 'User Login',
            'detail'   => ' login to Purple.',
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
    public function afterSignOut($event, $admin) 
    {
        $options = [
            'title'    => 'User Logout',
            'detail'   => ' logout from Purple.',
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
    public function sendEmailForgotPassword($event, $admin, $data)
    {
        $tableSettings = TableRegistry::get('Settings');
        $key           = $tableSettings->settingsPublicApiKey();
        $resetLink     = $data['link'].'/reset-password/token/'.md5(trim($admin['email']));
        $userData = array(
            'sitename'    => $tableSettings->settingsSiteName(),
            'username'    => $admin['username'],
            'email'       => $admin['email'],
            'displayName' => $admin['display_name'],
            'level'       => $admin['level']
        );
        $senderData = array(
            'name'   => $admin['display_name'],
            'domain' => $data['domain']
        );

        $purpleApi  = new PurpleProjectApi();
        $notifyUser = $purpleApi->sendEmailForgotPassword($key, $resetLink, json_encode($userData), json_encode($senderData));
        if ($notifyUser) {
            $result = [$admin['email'] => true];
        }
        else {
            $result = [$admin['email'] => false];
        }
        
        $event->setResult($result);
    }
    public function sendEmailResetPassword($event, $admin, $data)
    {
        $tableSettings = TableRegistry::get('Settings');
        $key           = $tableSettings->settingsPublicApiKey();
        $dashboardLink = $data['link'];
        $userData      = array(
            'sitename'    => $tableSettings->settingsSiteName(),
            'username'    => $admin['username'],
            'password'    => $data['password'],
            'email'       => $admin['email'],
            'displayName' => $admin['display_name'],
            'level'       => $admin['level']
        );
        $senderData   = array(
            'name'   => $admin['display_name'],
            'domain' => $data['domain']
        );

        $purpleApi  = new PurpleProjectApi();
        $notifyUser = $purpleApi->sendEmailNewPassword($key, $dashboardLink, json_encode($userData), json_encode($senderData));
        if ($notifyUser) {
            $result = [$admin['email'] => true];
        }
        else {
            $result = [$admin['email'] => false];
        }
        
        $event->setResult($result);
    }
    public function afterSave($event, $user, $admin, $save) 
    {
        if ($save == 'new') {
            $title  = 'Addition of a New User';
            $detail = ' add '.$user['username'].'('.$user['email'].') as a new user.';
        }
        elseif ($save == 'update') {
            $title  = 'Data Change of a User';
            $detail = ' change '.$user['username'].'('.$user['email'].') data.';
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
    public function afterUpdatePassword($event, $user, $admin) 
    {
        $options = [
            'title'    => 'Password Change of a User',
            'detail'   => ' change '.$user['username'].'('.$user['email'].') password.',
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
    public function afterDelete($event, $user, $admin) 
    {
        $options = [
            'title'    => 'Deletion of a User',
            'detail'   => ' delete '.$user.' from users data.',
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