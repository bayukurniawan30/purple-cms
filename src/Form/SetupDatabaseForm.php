<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SetupDatabaseForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('name')
                  ->notEmpty('name', 'Please fill this field')
                  ->add('name', [
                        'minLength' => [
                            'rule'    => ['minLength', 1],
                            'message' => 'Database name need to be at least 1 character'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 30],
                            'message' => 'Database name is maximum 30 characters'
                        ]
                    ])
                  ->requirePresence('username')
                  ->notEmpty('username', 'Please fill this field')
                  ->allowEmpty('password');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}