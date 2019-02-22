<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class CommentReplyForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('blog_id')
                  ->notEmpty('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Post id must be an integer value'
                        ]
                    ])
                  ->requirePresence('reply')
                  ->notEmpty('reply', 'Please fill this field')
                  ->add('reply', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Comment id to reply must be an integer value'
                        ]
                    ])
                  ->requirePresence('content')
                  ->notEmpty('content', 'Please fill this field')
                  ->add('title', [
                        'minLength' => [
                            'rule'    => ['minLength', 3],
                            'message' => 'Comment need to be at least 3 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 1000],
                            'message' => 'Comment is maximum 1000 characters'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}