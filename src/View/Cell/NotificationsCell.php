<?php
namespace App\View\Cell;
use Cake\View\Cell;
use App\Purple\PurpleProjectGlobal;

class NotificationsCell extends Cell
{
	public function commentNotification($id)
	{
    	$this->loadModel('Blogs');
        $blog = $this->Blogs->get($id);
        $this->set('blogTitle', $blog->title);
	}
}