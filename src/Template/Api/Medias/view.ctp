<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'medias') && $decode->medias != NULL) {
        foreach ($decode->medias as $media) {
            if ($decode->type == 'images') {
                $media->full_path = $this->cell('Medias::mediaPath', [$media->name, 'image', 'original']);
                $media->thumbnail = [
                    '300x300' => $this->cell('Medias::mediaPath', [$media->name, 'image', 'thumbnail::300']),
                    '480x270' => $this->cell('Medias::mediaPath', [$media->name, 'image', 'thumbnail::480']),
                ];
            }
            elseif ($decode->type == 'videos') {
                $media->full_path = $this->cell('Medias::mediaPath', [$media->name, 'video']);
            }
            elseif ($decode->type == 'documents') {
                $media->full_path = $this->cell('Medias::mediaPath', [$media->name, 'document']);
            }

            $media->readable_size = $this->Purple->readableFileSize($media->size);

            if ($media->admin->photo != NULL) {
                $media->admin->photo = $this->cell('Medias::mediaPath', [$media->admin->photo, 'image', 'original']);
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>