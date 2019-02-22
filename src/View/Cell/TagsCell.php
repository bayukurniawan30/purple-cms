<?php
namespace App\View\Cell;
use Cake\View\Cell;

class TagsCell extends Cell
{
    public function tagsInPost($postId) 
    {
    	$this->loadModel('Tags');
        $tags = $this->Tags->postTags($postId);
        $this->set('tagsInPost', $tags);
    }
    public function totalPostsInTag($tagId)
    {
    	$this->loadModel('Blogs');
        $blogs = $this->Blogs->taggedPosts($tagId);
    	$total = $blogs->count();
        $this->set('totalPosts', $total);
    }
}