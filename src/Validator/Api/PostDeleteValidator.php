<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class PostDeleteValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['id' => [
                        'message' => 'Post id is required',
                    ]])
                  ->notEmpty('id', 'Post id is required')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Post id must be an integer value'
                        ]
                    ]);

        return $validator;
    }
}