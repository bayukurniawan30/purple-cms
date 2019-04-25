<?php
namespace App\View\Cell;
use Cake\View\Cell;

class PagesCell extends Cell
{
    public function childPages($page) 
    {
        $parent = $page->id;
    	$this->loadModel('Pages');
        $childPages = $this->Pages->find('all', [
            'order' => ['Pages.id' => 'DESC']])->contain('PageTemplates')->contain('Admins')->where(['Pages.parent' => $parent]);
        $this->set('childPages', $childPages);
        $this->set('page', $page);
    }
}