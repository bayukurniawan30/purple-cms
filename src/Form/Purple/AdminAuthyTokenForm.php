<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class AdminAuthyTokenForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('token')
                  ->notEmpty('token', 'Please fill this field')
                  ->requirePresence('id')
                  ->notEmpty('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'User id must be an integer value'
                        ]
                    ]);

        return $validator;
    }
    protected function _execute(array $data)
    {
        return true;
    }
}