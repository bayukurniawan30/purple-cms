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
                  ->notEmptyString('title', 'Please fill this field')
                  ->add('title', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 100],
                            'message' => 'Page title is maximum 100 characters'
                        ]
                    ])
                  ->requirePresence('status')
                  ->notEmptyString('status', 'Please select status of the menu')
                  ->requirePresence('page_template_id')
                  ->notEmptyString('page_template_id', 'Please select template')
                  ->allowEmptyString('parent');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}