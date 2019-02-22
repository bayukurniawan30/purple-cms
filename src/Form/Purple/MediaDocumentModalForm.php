<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class MediaDocumentModalForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('title')
                  ->notEmpty('title', 'Please fill this field')
                  ->add('title', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 100],
                            'message' => 'Document title is maximum 100 characters'
                        ]
                    ])
                  ->allowEmpty('description')
                  ->requirePresence('id')
                  ->notEmpty('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Document id must be an integer value'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}