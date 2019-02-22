<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Visitor extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}