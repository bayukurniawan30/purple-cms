<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class AdminEditForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('display_name')
                  ->notEmptyString('display_name', 'Please fill this field')
                  ->add('display_name', [
                        'minLength' => [
                            'rule'    => ['minLength', 3],
                            'message' => 'Username need to be at least 3 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 50],
                            'message' => 'Username is maximum 50 characters'
                        ],
                        'alphaNumeric' => [
                            'rule'    => 'alphaNumeric',
                            'message' => 'Username is alpha numeric'
                        ]
                    ])
                  ->requirePresence('username')
                  ->notEmptyString('username', 'Please fill this field')
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
                  ->allowEmptyString('password')
                  ->add('password', [
                        'size' => [
                            'rule'    => ['lengthBetween', 6, 20],
                            'message' => 'Password need to be at least 6 characters and maximum 20 characters'
                        ]
                    ])
                  ->allowEmptyString('repeatpassword')
                  ->add('repeatpassword', [
                        'size'    => [
                            'rule'    => ['lengthBetween', 6, 20],
                            'message' => 'Password need to be at least 6 characters and maximum 20 characters'
                        ]
                    ])
                  ->requirePresence('email')
                  ->notEmptyString('email', 'Please fill this field')
                  ->add('email', [
                        'validFormat' => [
                            'rule'    => 'email',
                            'message' => 'Email must be in valid format',
                        ]
                    ])
                  ->allowEmptyString('photo')
                  ->allowEmptyString('phone')
                  ->allowEmptyString('calling_code')
                  ->allowEmptyString('about')
                  ->requirePresence('level')
                  ->notEmptyString('level', 'Please fill this field')
                  ->requirePresence('id')
                  ->notEmptyString('id', 'Please fill this field')
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