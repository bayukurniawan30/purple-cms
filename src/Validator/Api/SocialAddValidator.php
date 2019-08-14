<?php
namespace App\Validator\Api;

use Cake\Validation\Validator;

class SocialAddValidator
{
    public function validate()
    {
        $validator = new Validator();
        $validator->requirePresence(['name' => [
                        'message' => 'Social media is required',
                    ]])
                  ->notEmpty('name', 'Social media is required')
                  ->requirePresence(['link' => [
                        'message' => 'Link is required',
                    ]])
                  ->notEmpty('link', 'Link is required')
                  ->add('link', [
                        'url' => [
                            'rule'    => ['url'],
                            'message' => 'Link must be a valid URL'
                        ]
                    ]);

        return $validator;
    }
}