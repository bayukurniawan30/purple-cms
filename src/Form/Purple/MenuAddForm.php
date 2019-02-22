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
                  ->notEmpty('title', 'Please fill this field')
                  ->add('title', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 100],
                            'message' => 'Navigation title is maximum 100 characters'
                        ]
                    ])
                  ->allowEmpty('parent')
                  ->requirePresence('status')
                  ->notEmpty('status', 'Please select status of the menu')
                  ->requirePresence('point')
                  ->notEmpty('point', 'Please select where the menu will be pointed')
                  ->requirePresence('target')
                  ->notEmpty('target', 'Please select page or create custom link');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}