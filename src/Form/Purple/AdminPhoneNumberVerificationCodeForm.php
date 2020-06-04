<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class AdminPhoneNumberVerificationCodeForm extends Form
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
                    ->requirePresence('phone')
                    ->notEmpty('phone', 'Please fill this field');

        return $validator;
    }
    protected function _execute(array $data)
    {
        return true;
    }
}