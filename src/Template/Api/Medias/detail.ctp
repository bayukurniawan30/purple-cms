<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'media') && $decode->media != NULL) {
        if ($decode->type == 'images') {
            $decode->media->full_path = $this->cell('Medias::mediaPath', [$decode->media->name, 'image', 'original']);
            $decode->media->thumbnail = [
                '300x300' => $this->cell('Medias::mediaPath', [$decode->media->name, 'image', 'thumbnail::300']),
                '480x270' => $this->cell('Medias::mediaPath', [$decode->media->name, 'image', 'thumbnail::480']),
            ];
        }
        elseif ($decode->type == 'videos') {
            $decode->media->full_path = $this->cell('Medias::mediaPath', [$decode->media->name, 'video']);
        }
        elseif ($decode->type == 'documents') {
            $decode->media->full_path = $this->cell('Medias::mediaPath', [$decode->media->name, 'document']);
        }

        $decode->media->readable_size = $this->Purple->readableFileSize($decode->media->size);

        if ($decode->media->admin->photo != NULL) {
            $decode->media->admin->photo = $this->cell('Medias::mediaPath', [$decode->media->admin->photo, 'image', 'original']);
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>