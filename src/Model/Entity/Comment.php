<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Comment extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
    protected function _getContent($content)
    {
        return html_entity_decode($content);
    }
    protected function _getTextStatus()
    {
    	if ($this->status == '0') {
	        return 'Unpublish';
	    }
	    elseif ($this->status == '1') {
	    	return 'Publish';
	    }
    }
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