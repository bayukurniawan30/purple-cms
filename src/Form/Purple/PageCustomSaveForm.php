<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class PageCustomSaveForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('id')
                  ->notEmptyString('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Page id must be an integer value'
                        ]
                    ])
                  ->requirePresence('title')
                  ->notEmptyString('title', 'Please fill this field')
                  ->add('title', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 100],
                            'message' => 'Page title is maximum 100 characters'
                        ]
                    ])
                  ->allowEmptyString('content')
                  ->allowEmptyString('meta_keywords')
                  ->allowEmptyString('meta_description')
                  ->add('meta_description', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 150],
                            'message' => 'Meta description is maximum 150 characters'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}