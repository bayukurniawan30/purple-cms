<?php
    $newArray = array();
    foreach ($medias as $media):
        $fullImage   = $this->request->getAttribute("webroot") . 'uploads/images/thumbnails/300x300/' . $media->name;

        $newArray[]['url']   = $fullImage;
        $newArray[]['name']  = $media->title;
        $newArray[]['id']    = $media->id;
    endforeach;
    foreach ($medias as $media):
        $fullImage   = $this->request->getAttribute("webroot") . 'uploads/images/original/' . $media->name;

        $newArray[]['url']   = $fullImage;
        $newArray[]['name']  = $media->title;
        $newArray[]['id']    = $media->id;
    endforeach;

    $json = json_encode($newArray);
    echo $json;
?>