<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class BlogTypesTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('blog_types');
		$this->setPrimaryKey('id');
	}
}