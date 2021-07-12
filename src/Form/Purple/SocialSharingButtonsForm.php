<?php
namespace App\Form\Purple;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;

class SocialSharingButtonsForm extends Form
{
    protected function _buildValidator(Validator $validator)
    {
        $validator->requirePresence('theme')
                  ->notEmptyString('theme', 'Please select theme')
                  ->requirePresence('fontsize')
                  ->notEmptyString('theme', 'Please select font size')
                  ->requirePresence('label')
                  ->notEmptyString('theme', 'Please select label')
                  ->requirePresence('count')
                  ->notEmptyString('theme', 'Please select count');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}