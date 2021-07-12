<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class NewPasswordForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('password')
                  ->notEmptyString('password', 'Please fill this field')
                  ->add('password', [
                        'size' => [
                            'rule'    => ['lengthBetween', 6, 20],
                            'message' => 'Password need to be at least 6 characters and maximum 20 characters'
                        ]
                    ])
                  ->requirePresence('repeatpassword')
                  ->notEmptyString('repeatpassword', 'Please fill this field')
                  ->add('repeatpassword', [
                        'size'    => [
                            'rule'    => ['lengthBetween', 6, 20],
                            'message' => 'Password need to be at least 6 characters and maximum 20 characters'
                        ]
                    ])
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