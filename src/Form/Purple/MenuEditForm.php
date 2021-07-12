<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class MenuEditForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('title')
                  ->notEmptyString('title', 'Please fill this field')
                  ->add('title', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 100],
                            'message' => 'Navigation title is maximum 100 characters'
                        ]
                    ])
                  ->requirePresence('status')
                  ->notEmptyString('status', 'Please select status of the menu')
                  ->requirePresence('point')
                  ->notEmptyString('point', 'Please select where the menu will be pointed')
                  ->requirePresence('target')
                  ->notEmptyString('target', 'Please select page or create custom link')
                  ->requirePresence('id')
                  ->notEmptyString('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Navigation id must be an integer value'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}