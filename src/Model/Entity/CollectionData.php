<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CollectionData extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}