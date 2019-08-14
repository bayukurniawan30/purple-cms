<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'page') && $decode->page != NULL) {
        if ($decode->page->parent == NULL) {
            $decode->page->link = $baseUrl . $decode->page->slug;
        }
        else {
            $decode->page->link = $baseUrl . $decode->page->parent_slug . '/' . $decode->page->slug;
        }

        if ($decode->page->admin->photo != NULL) {
            $decode->page->admin->photo = $baseUrl . 'uploads/images/original/' . $decode->page->admin->photo;
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>