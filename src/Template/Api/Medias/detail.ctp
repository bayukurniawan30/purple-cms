<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'media') && $decode->media != NULL) {
        if ($decode->type == 'images') {
            $decode->media->full_path = $baseUrl . 'uploads/images/original/' . $decode->media->name;
            $decode->media->thumbnail = [
                '300x300' => $baseUrl . 'uploads/images/thumbnails/300x300/' . $decode->media->name,
                '480x270' => $baseUrl . 'uploads/images/thumbnails/480x270/' . $decode->media->name,
            ];
        }
        elseif ($decode->type == 'videos') {
            $decode->media->full_path = $baseUrl . 'uploads/videos/' . $decode->media->name;
        }
        elseif ($decode->type == 'documents') {
            $decode->media->full_path = $baseUrl . 'uploads/documents/' . $decode->media->name;
        }

        $decode->media->readable_size = $this->Purple->readableFileSize($decode->media->size);

        if ($decode->media->admin->photo != NULL) {
            $decode->media->admin->photo = $baseUrl . 'uploads/images/original/' . $decode->media->admin->photo;
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>