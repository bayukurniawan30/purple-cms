<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class MediaVideo extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
}