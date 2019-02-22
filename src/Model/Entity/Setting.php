<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Setting extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}