<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class HistoryFilterForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
    	$validator->requirePresence('month')
    			  ->requirePresence('year');
    	return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}