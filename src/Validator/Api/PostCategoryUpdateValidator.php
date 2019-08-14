<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class PostCategoryUpdateValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['name' => [
                        'message' => 'Category name is required',
                    ]])
                  ->notEmpty('name', 'Category name is required')
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
                  ->allowEmpty('page_id')
                  ->requirePresence(['id' => [
                        'message' => 'Category id is required',
                    ]])
                  ->notEmpty('id', 'Category id is required')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Category id must be an integer value'
                        ]
                    ]);

        return $validator;
    }
}