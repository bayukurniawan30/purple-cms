<?php
namespace App\View\Cell;
use Cake\View\Cell;
use App\Purple\PurpleProjectComponents;

class CollectionsCell extends Cell
{
    public function fieldTypeName($key)
    {
    	$purpleProjectCollections = new PurpleProjectComponents();
        $fieldTypes = $purpleProjectCollections->fieldTypes();

        if (array_key_exists($key, $fieldTypes)) {
            $fieldName = $fieldTypes[$key]['text'];
            $this->set('fieldName', $fieldName);
        }
        else {
            $this->set('fieldName', '0');
        }
    }
    public function countDatas($collectionId) 
    {
    	$this->loadModel('CollectionDatas');
        $collectionDatas = $this->CollectionDatas->find()->where(['collection_id' => $collectionId]);
        $this->set('total', $collectionDatas->count());
    }
}