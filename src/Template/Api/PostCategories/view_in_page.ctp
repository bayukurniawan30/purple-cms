<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post_categories') && $decode->post_categories != NULL) {
        foreach ($decode->post_categories as $category) {
            $category->permalink = $this->Url->build([
                '_name'    => 'postsInCategory',
                'category' => $category->slug
            ], true);

            if ($category->admin->photo != NULL) {
                $category->admin->photo = $this->cell('Medias::mediaPath', [$category->admin->photo, 'image', 'original']);
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>