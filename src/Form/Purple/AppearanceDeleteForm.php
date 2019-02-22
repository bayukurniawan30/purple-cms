<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class AppearanceDeleteForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('id')
                  ->notEmpty('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'ID must be an integer value'
                        ]
                    ])
                  ->requirePresence('type')
                  ->notEmpty('type', 'Please fill this field');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}