<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class SocialDeleteValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['id' => [
                        'message' => 'Social media id is required',
                    ]])
                  ->notEmpty('id', 'Social media id is required')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Social media id must be an integer value'
                        ]
                    ]);
        
        return $validator;
    }
}