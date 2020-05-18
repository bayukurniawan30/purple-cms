<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'medias') && $decode->medias != NULL) {
        foreach ($decode->medias as $media) {
            if ($decode->type == 'images') {
                $media->full_path = $this->cell('Medias::mediaPath', [$media->name, 'image', 'original'])->render();
                $media->thumbnail = [
                    '300x300' => $this->cell('Medias::mediaPath', [$media->name, 'image', 'thumbnail::300'])->render(),
                    '480x270' => $this->cell('Medias::mediaPath', [$media->name, 'image', 'thumbnail::480'])->render(),
                ];
            }
            elseif ($decode->type == 'videos') {
                $media->full_path = $this->cell('Medias::mediaPath', [$media->name, 'video'])->render();
            }
            elseif ($decode->type == 'documents') {
                $media->full_path = $this->cell('Medias::mediaPath', [$media->name, 'document'])->render();
            }

            $media->readable_size = $this->Purple->readableFileSize($media->size);

            if ($media->admin->photo != NULL) {
                $media->admin->photo = $this->cell('Medias::mediaPath', [$media->admin->photo, 'image', 'original'])->render();
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>