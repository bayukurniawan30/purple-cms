<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SocialAddForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('name')
                  ->notEmpty('name', 'Please select social media')
                  ->requirePresence('link')
                  ->notEmpty('link', 'Please fill this field')
                  ->add('link', [
                        'url' => [
                            'rule'    => ['url'],
                            'message' => 'Link must be a valid URL'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}