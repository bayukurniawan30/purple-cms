<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class MediaDoc extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}