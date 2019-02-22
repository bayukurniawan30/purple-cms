<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class PageTemplatesTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('page_templates');
		$this->setPrimaryKey('id');
        $this->hasMany('Pages')
		     ->setForeignKey('page_template_id');
	}
}