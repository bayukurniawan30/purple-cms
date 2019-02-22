<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Blog extends Entity
{
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];
	protected function _getTitle($title)
    {
        return html_entity_decode($title);
    }
    protected function _getContent($content)
    {
        return html_entity_decode($content);
    }
    protected function _getTextStatus()
    {
        if ($this->status == '0') {
            return 'Draft';
        }
        elseif ($this->status == '1') {
            return 'Publish';
        }
    }
}