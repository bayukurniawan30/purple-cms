<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class ProductionUserVerificationForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('email')
                  ->notEmpty('email', 'Please fill this field')
                  ->add('email', [
                        'validFormat' => [
                            'rule'    => 'email',
                            'message' => 'Email must be in valid format',
                        ]
                    ])
                  ->requirePresence('key')
                  ->notEmpty('key', 'Please fill this field');

        return $validator;
    }
    protected function _execute(array $data)
    {
        return true;
    }
}