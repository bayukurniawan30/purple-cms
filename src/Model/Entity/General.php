<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class General extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
	/*
	protected function _getContent($content)
    {
        return html_entity_decode($content);
    }
    */
}