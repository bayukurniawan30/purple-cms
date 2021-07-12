<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Singleton extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
	protected function _getTitle($title)
    {
        return html_entity_decode($title);
    }
    protected function _getTextStatus()
    {
        if ($this->status == '0') {
            return 'Draft';
        }
        elseif ($this->status == '1') {
            return 'Publish';
        }
        elseif ($this->status == '2') {
            return 'Deleted';
        }
    }
}