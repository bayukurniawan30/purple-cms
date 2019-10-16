<?php
namespace App\Event;

use Cake\Log\Log;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use App\Purple\PurpleProjectApi;

class BlogListener implements EventListenerInterface 
{
    public function implementedEvents() {
        return array(
            'Model.Notification.afterSaveComment' => 'afterSaveComment',
            'Model.Blog.afterSentComment'         => 'afterSentComment'
        );
    }
    public function afterSentComment($event, $data)
    {
        $tableAdmins   = TableRegistry::get('Admins');
        $tableBlogs    = TableRegistry::get('Blogs');
        $tableSettings = TableRegistry::get('Settings');

        // Send Email to author to Notify author
        $blog   = $tableBlogs->get($data['blog_id']);
        $author = $tableAdmins->get($blog->admin_id);
        $key    = $tableSettings->settingsPublicApiKey();
        $dashboardLink = $data['link'];
        $userData      = array(
            'sitename'    => $tableSettings->settingsSiteName(),
            'email'       => $author->email,
            'displayName' => $author->display_name,
            'level'       => $author->level
        );
        $post          = $blog->title;
        $commentData   = array(
            'name'   => $data['name'],
            'email'  => $data['email'],
            'blogId' => $data['blog_id'],
            'domain' => $data['domain']
        );

        $purpleApi  = new PurpleProjectApi();
        $notifyUser = $purpleApi->sendEmailPostComment($key, $dashboardLink, json_encode($userData), $post, json_encode($commentData));

        if ($notifyUser == true) {
            $emailNotification = [$author->email => true];
        }
        else {
            $emailNotification = [$author->email => false];
        }

        $result = $emailNotification;
        $event->setResult($result);
    }
    public function afterSaveComment($event, $data)
    {
        $tableNotifications = TableRegistry::get('Notifications');

        // Save notification
        $notification = $tableNotifications->newEntity();
        $notification->type       = 'comment';
        $notification->content    = $data['name'].' sent a comment to your post. Click to view the comment.';
        $notification->comment_id = $data['comment_id'];
        $notification->blog_id    = $data['blog_id'];

        if ($tableNotifications->save($notification)) {
            $result = true;
        }
        else {
            $result = false;
        }
        
        $event->setResult($result);
    }
}