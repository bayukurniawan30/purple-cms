<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class CollectionAddForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('name')
                  ->notEmptyString('name', 'Please fill this field')
                  ->add('name', [
                        'minLength' => [
                            'rule'    => ['minLength', 2],
                            'message' => 'Name need to be at least 2 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 255],
                            'message' => 'Name is maximum 255 characters'
                        ]
                    ])
                  ->requirePresence('status')
                  ->notEmptyString('status', 'Please select status of the post')
                  ->requirePresence('sorting')
                  ->notEmptyString('sorting', 'Please select sorting option')
                  ->requirePresence('sorting_order')
                  ->notEmptyString('sorting_order', 'Please select sorting order option');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}