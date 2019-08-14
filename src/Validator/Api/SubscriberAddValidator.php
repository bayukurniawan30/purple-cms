<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class SubscriberAddValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['email' => [
                        'message' => 'Email is required',
                    ]])
                  ->notEmpty('email', 'Email is required')
                  ->add('email', [
                        'validFormat' => [
                            'rule'    => 'email',
                            'message' => 'Email must be in valid format',
                        ]
                    ]);

        return $validator;
    }
}