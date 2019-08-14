<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class PostCommentChangeStatusValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['id' => [
                        'message' => 'Comment id is required',
                    ]])
                  ->notEmpty('id', 'Comment id is required')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Comment id must be an integer value'
                        ]
                    ])
                  ->requirePresence(['status' => [
                        'message' => 'Comment status is required',
                    ]])
                  ->notEmpty('status', 'Comment status is required')
                  ->add('status', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Status must be an integer value'
                        ]
                    ]);

        return $validator;
    }
}