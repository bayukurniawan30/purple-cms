<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class BlogTypesTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('blog_types');
		$this->setPrimaryKey('id');
	}
}