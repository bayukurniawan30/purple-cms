<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Http\ServerRequest;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class CommentsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('comments');
		$this->setPrimaryKey('id');
        $this->hasMany('Notifications', [
                'dependent' => true,
                'cascadeCallbacks' => true,
             ])
             ->setForeignKey('comment_id')
             ->setJoinType('LEFT');
		$this->belongsTo('Blogs')
		     ->setForeignKey('blog_id')
             ->setJoinType('INNER');
        $this->belongsTo('Admins')
		     ->setForeignKey('admin_id')
             ->setJoinType('LEFT');
    }
    public function beforeSave($event, $entity, $options)
    {
    	$purpleSettings = new PurpleProjectSettings();
		$timezone       = $purpleSettings->timezone();
		$date           = Carbon::now($timezone);

		// Sanitize name, email, and content
		$entity->name    = trim(strip_tags($entity->name));
		$entity->email   = trim($entity->email);

		if ($entity->isNew()) {
			$entity->created  = $date;
			$entity->content = trim(htmlentities(nl2br($entity->content)));
		}
    }
    protected function _getCreated($created)
    {
        $serverRequest   = new ServerRequest();
        $session         = $serverRequest->getSession();
        $timezone        = $session->read('Purple.timezone');
        $settingTimezone = $session->read('Purple.settingTimezone');

        $date = new \DateTime($created, new \DateTimeZone($settingTimezone));
        $date->setTimezone(new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }
    public function dashboardStatistic($read = 'all')
    {
        $query = $this->find('all')->contain(['Admins']);
        if ($read == 'all') {
            $query->where(['Comments.admin_id IS' => NULL]);
        }
        elseif ($read == 'unread') {
            $query->where(['Comments.is_read IS' => NULL, 'Comments.admin_id IS' => NULL]);
        }
        elseif ($read == 'read') {
            $query->where(['Comments.is_read' => '1', 'Comments.admin_id IS' => NULL]);
        }

        return $query->count();
    }
	public function postComments($blogId, $read = 'all', $type = 'fetch')
	{
		$query = $this->find('all')->contain(['Admins']);

		if ($read == 'all') {
			$query->where(['Comments.blog_id' => $blogId, 'Comments.admin_id IS' => NULL]);
		}
		elseif ($read == 'unread') {
			$query->where(['Comments.blog_id' => $blogId, 'Comments.is_read IS' => NULL, 'Comments.admin_id IS' => NULL]);
		}
		elseif ($read == 'read') {
			$query->where(['Comments.blog_id' => $blogId, 'Comments.is_read' => '1', 'Comments.admin_id IS' => NULL]);
		}
       
        if ($type == 'fetch') {
        	return $query;
        }
        elseif ($type == 'count') {
        	return $query->count();
        }
	}
	public function publishedComments($blogId, $type = 'fetch')
	{
        $query = $this->find('all', [
            'order' => ['Comments.created' => 'DESC']
        ])->contain(['Admins']);
        if ($type == 'fetch') {
        	return $query->where(['Comments.status <>' => '0', 'Comments.blog_id' => $blogId, 'Comments.admin_id IS' => NULL]);
        }
        elseif ($type == 'count') {
        	return $query->where(['Comments.status <>' => '0', 'Comments.blog_id' => $blogId, 'Comments.admin_id IS' => NULL])->count();
        }
        elseif ($type == 'countall') {
            return $query->where(['Comments.status <>' => '0', 'Comments.blog_id' => $blogId])->count();
        }
        else {
        	return false;
        }
	}
    public function replyComments($blogId, $commentId, $type = 'fetch')
    {
        $query = $this->find('all')->contain(['Admins'])->where(['Comments.blog_id' => $blogId, 'Comments.reply' => $commentId]);
        if ($type == 'fetch') {
            return $query;
        }
        elseif ($type == 'count') {
            return $query->count();
        }
        else {
            return false;
        }
    }
	public function totalReplies($blogId, $replyId, $type = 'number')
	{
        $query = $this->find()->where(['blog_id' => $blogId, 'reply' => $replyId]);
        $total = $query->count();
        if ($type == 'number') {
        	return $total;
        }
        elseif ($type == 'text') {
        	if ($total == 0) {
        		return 'No reply';
        	}
        	elseif ($total == 1) {
        		return '1 reply';
        	}
        	elseif ($total > 1) {
        		return $total.' replies';
        	}
        }

	}
}