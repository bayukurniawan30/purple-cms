<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SetupAdministrativeForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('sitename')
                  ->notEmpty('sitename', 'Please fill this field')
                  ->add('sitename', [
                        'minLength' => [
                            'rule'    => ['minLength', 1],
                            'message' => 'Site name need to be at least 1 character'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 30],
                            'message' => 'Site name is maximum 30 characters'
                        ]
                    ])
                  ->requirePresence('username')
                  ->notEmpty('username', 'Please fill this field')
                  ->add('username', [
                        'minLength' => [
                            'rule'    => ['minLength', 6],
                            'message' => 'Username need to be at least 6 characters'
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
                  ->notEmpty('password', 'Please fill this field')
                  ->add('password', [
                        'size' => [
                            'rule'    => ['lengthBetween', 6, 20],
                            'message' => 'Password need to be at least 6 characters and maximum 20 characters'
                        ]
                    ])
                  ->requirePresence('repeatpassword')
                  ->notEmpty('repeatpassword', 'Please fill this field')
                  ->add('repeatpassword', [
                        'size'    => [
                            'rule'    => ['lengthBetween', 6, 20],
                            'message' => 'Password need to be at least 6 characters and maximum 20 characters'
                        ]
                    ])
                  ->requirePresence('email')
                  ->notEmpty('email', 'Please fill this field')
                  ->add('email', [
                        'validFormat' => [
                            'rule'    => 'email',
                            'message' => 'Email must be in valid format',
                        ]
                    ])
                  ->requirePresence('timezone')
                  ->notEmpty('timezone', 'Please fill this field');

        return $validator;
    }
    protected function _execute(array $data)
    {
        return true;
    }
}