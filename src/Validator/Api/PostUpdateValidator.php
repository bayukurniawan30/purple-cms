<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class PostUpdateValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['title' => [
                        'message' => 'Post title is required',
                    ]])
                  ->notEmptyString('title', 'Post title is required')
                  ->add('title', [
                        'minLength' => [
                            'rule'    => ['minLength', 2],
                            'message' => 'Title need to be at least 2 characters'
                        ],
                        'maxLength' => [
                            'rule'    => ['maxLength', 255],
                            'message' => 'Title is maximum 255 characters'
                        ]
                    ])
                  ->requirePresence(['content' => [
                        'message' => 'Post content is required',
                    ]])
                  ->notEmptyString('content', 'Post content is required')
                  ->requirePresence(['blog_category_id' => [
                        'message' => 'Post category id is required',
                    ]])
                  ->notEmptyString('blog_category_id', 'Post category id is required')
                  ->add('blog_category_id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Category id must be an integer value'
                        ]
                    ])
                  ->requirePresence(['comment' => [
                        'message' => 'Allow comment is required',
                    ]])
                  ->notEmptyString('comment', 'Allow comment is required')
                  ->allowEmptyString('featured')
                  ->allowEmptyString('tags')
                  ->allowEmptyString('meta_keywords')
                  ->allowEmptyString('meta_description')
                  ->add('meta_description', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 150],
                            'message' => 'Meta description is maximum 150 characters'
                        ]
                    ])
                  ->requirePresence(['status' => [
                        'message' => 'status of the post is required',
                    ]])
                  ->notEmptyString('status', 'Please select status of the post')
                  ->requirePresence(['social_share' => [
                        'message' => 'Social share option is required',
                    ]])
                  ->notEmptyString('status', 'Please select social share option')
                  ->requirePresence(['id' => [
                        'message' => 'Post id is required',
                    ]])
                  ->notEmptyString('id', 'Post id is required')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Post id must be an integer value'
                        ]
                    ]);

        return $validator;
    }
}