<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class BlogAddForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('title')
                  ->notEmpty('title', 'Please fill this field')
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
                  ->requirePresence('content')
                  ->notEmpty('content', 'Please fill this field')
                  ->requirePresence('blog_category_id')
                  ->notEmpty('blog_category_id', 'Please fill this field')
                  ->add('blog_category_id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Category id must be an integer value'
                        ]
                    ])
                  ->requirePresence('comment')
                  ->notEmpty('comment', 'Please fill this field')
                  ->allowEmpty('featured')
                  ->allowEmpty('tags')
                  ->allowEmpty('meta_keywords')
                  ->allowEmpty('meta_description')
                  ->add('meta_description', [
                        'maxLength' => [
                            'rule'    => ['maxLength', 150],
                            'message' => 'Meta description is maximum 150 characters'
                        ]
                    ])
                  ->requirePresence('status')
                  ->notEmpty('status', 'Please select status of the post')
                  ->requirePresence('social_share')
                  ->notEmpty('status', 'Please select social share option');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}