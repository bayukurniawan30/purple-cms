<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class PostCategoryAddValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['name' => [
                        'message' => 'Category name is required',
                    ]])
                  ->notEmptyString('name', 'Category name is required')
                  ->add('name', [
                        'minLength' => [
                            'rule'    => ['minLength', 2],
                            'message' => 'Category name need to be at least 2 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 100],
                            'message' => 'Category name is maximum 100 characters'
                        ]
                        
                    ])
                  ->allowEmptyString('page_id');

        return $validator;
    }
}