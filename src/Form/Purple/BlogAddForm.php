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
                  ->notEmptyString('title', 'Please fill this field')
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
                  ->notEmptyString('content', 'Please fill this field')
                  ->requirePresence('blog_category_id')
                  ->notEmptyString('blog_category_id', 'Please fill this field')
                  ->add('blog_category_id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Category id must be an integer value'
                        ]
                    ])
                  ->requirePresence('comment')
                  ->notEmptyString('comment', 'Please fill this field')
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
                  ->requirePresence('status')
                  ->notEmptyString('status', 'Please select status of the post')
                  ->requirePresence('social_share')
                  ->notEmptyString('status', 'Please select social share option');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}