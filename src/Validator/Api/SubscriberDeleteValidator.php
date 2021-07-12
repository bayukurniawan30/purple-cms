<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class SubscriberDeleteValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['id' => [
                        'message' => 'Subscriber id is required',
                    ]])
                  ->notEmptyString('id', 'Subscriber id is required')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Subscriber id must be an integer value'
                        ]
                    ]);

        return $validator;
    }
}