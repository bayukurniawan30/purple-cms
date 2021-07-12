<?php
namespace App\View\Cell;
use Cake\View\Cell;
use App\Purple\PurpleProjectComponents;

class SingletonsCell extends Cell
{
    public function fieldTypeName($key)
    {
    	$purpleProjectSingletons = new PurpleProjectComponents();
        $fieldTypes = $purpleProjectSingletons->fieldTypes();

        if (array_key_exists($key, $fieldTypes)) {
            $fieldName = $fieldTypes[$key]['text'];
            $this->set('fieldName', $fieldName);
        }
        else {
            $this->set('fieldName', '0');
        }
    }
    public function countDatas($singletonId)
    {
    	$this->loadModel('SingletonDatas');
        $singletonDatas = $this->SingletonDatas->find()->where(['singleton_id' => $singletonId]);
        $this->set('total', $singletonDatas->count());
    }

}