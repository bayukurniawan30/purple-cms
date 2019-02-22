<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class ThemeApplyForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('folder')
                  ->notEmpty('folder', 'Please fill this field');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}