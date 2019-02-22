<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;

class SocialsTable extends Table
{
	public function initialize(array $config)
	{
		$this->setTable('socials');
		$this->setPrimaryKey('id');
	}
}