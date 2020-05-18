<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'media') && $decode->media != NULL) {
        if ($decode->type == 'images') {
            $decode->media->full_path = $this->cell('Medias::mediaPath', [$decode->media->name, 'image', 'original'])->render();
            $decode->media->thumbnail = [
                '300x300' => $this->cell('Medias::mediaPath', [$decode->media->name, 'image', 'thumbnail::300'])->render(),
                '480x270' => $this->cell('Medias::mediaPath', [$decode->media->name, 'image', 'thumbnail::480'])->render(),
            ];
        }
        elseif ($decode->type == 'videos') {
            $decode->media->full_path = $this->cell('Medias::mediaPath', [$decode->media->name, 'video'])->render();
        }
        elseif ($decode->type == 'documents') {
            $decode->media->full_path = $this->cell('Medias::mediaPath', [$decode->media->name, 'document'])->render();
        }

        $decode->media->readable_size = $this->Purple->readableFileSize($decode->media->size);

        if ($decode->media->admin->photo != NULL) {
            $decode->media->admin->photo = $this->cell('Medias::mediaPath', [$decode->media->admin->photo, 'image', 'original'])->render();
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>