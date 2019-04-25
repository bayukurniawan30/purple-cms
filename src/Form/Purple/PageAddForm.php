<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class PageAddForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('title')
                  ->notEmpty('title', 'Please fill this field')
                  ->add('title', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 100],
                            'message' => 'Page title is maximum 100 characters'
                        ]
                    ])
                  ->requirePresence('status')
                  ->notEmpty('status', 'Please select status of the menu')
                  ->requirePresence('page_template_id')
                  ->notEmpty('page_template_id', 'Please select template')
                  ->allowEmpty('parent');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}