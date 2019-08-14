<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class CommentStatusForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('id')
                  ->notEmpty('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Comment id must be an integer value'
                        ]
                    ])
                  ->requirePresence('status')
                  ->notEmpty('status', 'Please fill this field')
                  ->add('status', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Status must be an integer value'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}