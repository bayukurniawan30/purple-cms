<?php
namespace App\View\Cell;
use Cake\View\Cell;

class CategoriesCell extends Cell
{
    public function totalPost($categoryId) 
    {
    	$this->loadModel('Blogs');
        $blogs = $this->Blogs->find()->where(['blog_category_id' => $categoryId ]);
        $this->set('postInCategory', $blogs->count());
    }
}