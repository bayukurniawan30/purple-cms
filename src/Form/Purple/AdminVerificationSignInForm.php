<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class AdminVerificationSignInForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('code')
                  ->notEmpty('code', 'Please fill this field')
                  ->add('code', [
                        'minLength' => [
                            'rule'    => ['minLength', 6],
                            'message' => 'Code need to be at least 6 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 6],
                            'message' => 'Code is maximum 6 characters'
                        ]
                    ])
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