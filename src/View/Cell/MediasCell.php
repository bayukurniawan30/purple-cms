<?php
namespace App\View\Cell;
use Cake\View\Cell;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

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
    public function colorExtract($image)
    {
        $palette    = Palette::fromFilename($image);
        $extractor  = new ColorExtractor($palette);
        $colorCount = count($palette);
        if ($colorCount > 5) {
            $colors = $extractor->extract(8);
            // $colors = $palette->getMostUsedColors(8);
        }
        else {
            $colors = $extractor->extract($colorCount);
            // $colors = $palette->getMostUsedColors($colorCount);
        }

        $setColors = [];
        foreach($colors as $color) {
            array_push($setColors, Color::fromIntToHex($color));
        }

        $this->set('colors', implode(',', $setColors));
    }
}