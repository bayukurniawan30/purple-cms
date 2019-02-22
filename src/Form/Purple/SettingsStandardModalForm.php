<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SettingsStandardModalForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('value')
                  ->notEmpty('value', 'Please fill this field')
                  ->add('value', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 500],
                            'message' => 'Value is maximum 500 characters'
                        ]
                    ])
                  ->requirePresence('id')
                  ->notEmpty('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Setting id must be an integer value'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}