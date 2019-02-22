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
                  ->notEmpty('theme', 'Please select theme')
                  ->requirePresence('fontsize')
                  ->notEmpty('theme', 'Please select font size')
                  ->requirePresence('label')
                  ->notEmpty('theme', 'Please select label')
                  ->requirePresence('count')
                  ->notEmpty('theme', 'Please select count');

        return $validator;
    }

    protected function _execute(array $data)
    {
        return true;
    }
}