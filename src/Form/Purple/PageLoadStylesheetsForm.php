<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class PageLoadStylesheetsForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->allowEmptyString('stylesheet');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}