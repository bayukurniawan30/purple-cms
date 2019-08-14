<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class PostCategoryDeleteValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['id' => [
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