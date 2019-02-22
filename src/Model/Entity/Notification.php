<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Notification extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
	protected function _getRead()
    {
    	if ($this->is_read == '1') {
    		return 'Read';
    	}
    	else {
    		return 'Unread';
    	}
    }
}