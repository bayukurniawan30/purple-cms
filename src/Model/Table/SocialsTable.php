<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class SocialsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('socials');
		$this->setPrimaryKey('id');
	}
}