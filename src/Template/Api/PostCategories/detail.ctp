<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post_category') && $decode->post_category != NULL) {
        if ($decode->post_category->admin->photo != NULL) {
            $decode->post_category->admin->photo = $this->cell('Medias::mediaPath', [$decode->post_category->admin->photo, 'image', 'original']);
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>