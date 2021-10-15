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
    public function getConnectingCollections($id)
    {
    	$this->loadModel('Collections');
        $collection = $this->Collections->get($id);
        $fields     = $collection->fields;

        $this->set('data', $fields);
    }
    public function getConnectingCollectionDatasForSelectbox($collectionId)
    {
    	$this->loadModel('CollectionDatas');
        $collectionDatas = $this->CollectionDatas->find('list', [
            'keyField' => 'id',
            'valueField' => 'content'
        ])->where(['collection_id' => $collectionId])->toArray();

        if (count($collectionDatas) > 0) {
            $this->set('data', json_encode($collectionDatas));
        }
        else {
            $this->set('data', '0');
        }
    }
    public function getConnectingCollectionDataValue($collectionDataId, $fieldUid, $selectedCollectionId)
    {
        $this->loadModel('Collections');
        $this->loadModel('CollectionDatas');
        $collectionData = $this->CollectionDatas->get($collectionDataId, [
            'contain' => ['Collections']
        ]);
        $selectedCollection = $this->Collections->get($selectedCollectionId);

        $selectedCollectionField = $selectedCollection->fields;
        $collectionDataContent   = $collectionData->content;

        $uid = '';
        $decodeCollectionField = json_decode($selectedCollectionField, true);
        foreach ($decodeCollectionField as $field) {
            $decodeField = json_decode($field, true);
            foreach ($decodeField as $key => $value) {
                if ($value == $fieldUid) {
                    $uid = $decodeField['options']['showFieldUid'];
                }
            }
        }

        $decodeContent = json_decode($collectionDataContent, true);
        $text = $decodeContent[$uid]['value'];

        $this->set('data', $text);
    }
    public function countDatas($collectionId) 
    {
    	$this->loadModel('CollectionDatas');
        $collectionDatas = $this->CollectionDatas->find()->where(['collection_id' => $collectionId]);
        $this->set('total', $collectionDatas->count());
    }
}