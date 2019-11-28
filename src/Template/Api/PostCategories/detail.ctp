<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post_category') && $decode->post_category != NULL) {
        $decode->post_category->permalink = $this->Url->build([
            '_name'    => 'postsInCategory',
            'category' => $decode->post_category->slug
        ], true);

        if ($decode->post_category->admin->photo != NULL) {
            $decode->post_category->admin->photo = $this->cell('Medias::mediaPath', [$decode->post_category->admin->photo, 'image', 'original']);
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>