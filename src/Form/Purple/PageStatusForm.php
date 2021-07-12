<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class PageStatusForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('status')
                  ->notEmptyString('status', 'Please select status of the menu')
                  ->requirePresence('id')
                  ->notEmptyString('id', 'Please fill this field')
                  ->add('id', [
                        'isInteger' => [
                            'rule'    => ['isInteger'],
                            'message' => 'Page id must be an integer value'
                        ]
                    ]);

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}