<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class SingletonData extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}