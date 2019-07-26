<?php
namespace App\View\Cell;
use Cake\View\Cell;

class MediasCell extends Cell
{
    public function previousId($id)
    {
        $this->loadModel('Medias');
        $query = $this->Medias->find('previousRow', ['id' => $id]);
        if ($query == false) {
            $this->set('id', '0');
        }
        else {
            $this->set('id', $query->id);
        }
    }
    public function nextId($id)
    {
        $this->loadModel('Medias');
        $query = $this->Medias->find('nextRow', ['id' => $id]);
        if ($query == false) {
            $this->set('id', '0');
        }
        else {
            $this->set('id', $query->id);
        }
    }   
}