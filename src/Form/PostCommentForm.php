<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class PostCommentForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('name')
                  ->notEmpty('name', 'Please fill this field')
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
                  ->requirePresence('email')
                  ->notEmpty('email', 'Please fill this field')
                  ->add('email', [
                        'validFormat' => [
                            'rule'    => 'email',
                            'message' => 'Email must be in valid format',
                        ]
                    ])
                  ->requirePresence('content')
                  ->notEmpty('content', 'Please fill this field')
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
                  ->requirePresence('blog_id')
                  ->notEmpty('blog_id', 'Please fill this field')
                  ->add('blog_id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Post id must be an integer value'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}