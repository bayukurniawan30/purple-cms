<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class PostCommentSendValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['name' => [
                        'message' => 'Name is required',
                    ]])
                  ->notEmpty('name', 'Name is required')
                  ->add('name', [
                        'minLength' => [
                            'rule'    => ['minLength', 2],
                            'message' => 'Name need to be at least 2 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 50],
                            'message' => 'Name is maximum 50 characters'
                        ]
                    ])
                  ->requirePresence(['email' => [
                        'message' => 'Email is required',
                    ]])
                  ->notEmpty('email', 'Email is required')
                  ->add('email', [
                        'validFormat' => [
                            'rule'    => 'email',
                            'message' => 'Email must be in valid format',
                        ]
                    ])
                  ->requirePresence(['content' => [
                        'message' => 'Comment is required',
                    ]])
                  ->notEmpty('content', 'Comment is required')
                  ->add('content', [
                        'minLength' => [
                            'rule'    => ['minLength', 3],
                            'message' => 'Comment need to be at least 3 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 1000],
                            'message' => 'Comment is maximum 1000 characters'
                        ]
                    ])
                  ->requirePresence(['blog_id' => [
                        'message' => 'Post id is required',
                    ]])
                  ->notEmpty('blog_id', 'Post id is required')
                  ->add('blog_id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Post id must be an integer value'
                        ]
                    ]);

        return $validator;
    }
}