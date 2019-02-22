<?php
namespace App\View\Cell;
use Cake\View\Cell;
use App\Purple\PurpleProjectGlobal;

class CommentsCell extends Cell
{
    public function totalComment($blogId, $read) 
    {
    	$this->loadModel('Comments');
	    $purpleGlobal = new PurpleProjectGlobal();

        $comments = $this->Comments->postComments($blogId, $read, 'count');
        $this->set('totalComments', $comments);
        $this->set('formattedTotalComments', $purpleGlobal->shortenNumber($comments));
    }
    public function totalReplies($blogId, $commentId, $type)
    {
    	$this->loadModel('Comments');
        $replies = $this->Comments->totalReplies($blogId, $commentId, $type);
        $this->set('totalReplies', $replies);
    }
    public function fetchReplies($blogId, $commentId, $type)
    {
        $this->loadModel('Comments');
        $replyComments = $this->Comments->replyComments($blogId, $commentId, $type);
        $this->set('type', $type);
        $this->set('replyComments', $replyComments);
    }
}