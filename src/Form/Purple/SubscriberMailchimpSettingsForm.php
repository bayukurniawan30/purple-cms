<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SubscriberMailchimpSettingsForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('key')
                  ->notEmptyString('key', 'Please fill this field')
                  ->requirePresence('list')
                  ->notEmptyString('list', 'Please fill this field');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}