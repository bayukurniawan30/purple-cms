<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'medias') && $decode->medias != NULL) {
        foreach ($decode->medias as $media) {
            if ($decode->type == 'images') {
                $media->full_path = $baseUrl . 'uploads/images/original/' . $media->name;
                $media->thumbnail = [
                    '300x300' => $baseUrl . 'uploads/images/thumbnails/300x300/' . $media->name,
                    '480x270' => $baseUrl . 'uploads/images/thumbnails/480x270/' . $media->name,
                ];
            }
            elseif ($decode->type == 'videos') {
                $media->full_path = $baseUrl . 'uploads/videos/' . $media->name;
            }
            elseif ($decode->type == 'documents') {
                $media->full_path = $baseUrl . 'uploads/documents/' . $media->name;
            }

            $media->readable_size = $this->Purple->readableFileSize($media->size);

            if ($media->admin->photo != NULL) {
                $media->admin->photo = $baseUrl . 'uploads/images/original/' . $media->admin->photo;
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>