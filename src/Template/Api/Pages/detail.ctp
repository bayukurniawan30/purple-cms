<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'page') && $decode->page != NULL) {
        if ($decode->page->parent == NULL) {
            $decode->page->permalink = $baseUrl . $decode->page->slug;
        }
        else {
            $decode->page->permalink = $baseUrl . $decode->page->parent_slug . '/' . $decode->page->slug;
        }

        if ($decode->page->admin->photo != NULL) {
            $decode->page->admin->photo = $this->cell('Medias::mediaPath', [$decode->page->admin->photo, 'image', 'original']);
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>