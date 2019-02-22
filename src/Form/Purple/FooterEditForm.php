<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class FooterEditForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->allowEmpty('left')
                  ->add('left', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 255],
                            'message' => 'Content of left column footer is maximum 255 characters'
                        ]
                    ])
                  ->allowEmpty('right')
                  ->add('right', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 255],
                            'message' => 'Content of Right column footer is maximum 255 characters'
                        ]
                    ])
                  ->requirePresence('id')
                  ->notEmpty('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Setting id must be an integer value'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}