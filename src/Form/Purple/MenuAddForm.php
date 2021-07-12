<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class MenuAddForm extends Form
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
                  ->allowEmptyString('parent')
                  ->requirePresence('status')
                  ->notEmptyString('status', 'Please select status of the menu')
                  ->requirePresence('point')
                  ->notEmptyString('point', 'Please select where the menu will be pointed')
                  ->requirePresence('target')
                  ->notEmptyString('target', 'Please select page or create custom link');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}