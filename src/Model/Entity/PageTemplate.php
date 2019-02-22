<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class PageTemplate extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}