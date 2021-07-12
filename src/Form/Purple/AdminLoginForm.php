<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class AdminLoginForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('username')
                  ->notEmptyString('username', 'Please fill this field')
                  ->add('username', [
                        'minLength' => [
                            'rule'    => ['minLength', 6],
                            'message' => 'Username need to be at least 1 character'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 15],
                            'message' => 'Username is maximum 15 characters'
                        ],
                        'alphaNumeric' => [
                            'rule'    => 'alphaNumeric',
                            'message' => 'Username is alpha numeric'
                        ]
                    ])
                  ->requirePresence('password')
                  ->notEmptyString('password', 'Please fill this field')
                  ->add('password', [
                        'size' => [
                            'rule'    => ['lengthBetween', 6, 20],
                            'message' => 'Password need to be at least 6 characters and maximum 20 characters'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}