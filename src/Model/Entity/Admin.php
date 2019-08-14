<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

class Admin extends Entity
{
	protected $_accessible = [
		'*'  => true,
		'id' => false,
	];
	protected function _setPassword($password)
   	{
       	return (new DefaultPasswordHasher())->hash($password);
	}
    protected function _getDisplayName($displayName)
    {
        return ucwords($displayName);
    }
}