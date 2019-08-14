<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'pages') && $decode->pages != NULL) {
        foreach ($decode->pages as $page) {
            $page->link = $baseUrl . $page->slug;

            if ($page->admin->photo != NULL) {
                $page->admin->photo = $baseUrl . 'uploads/images/original/' . $page->admin->photo;
            }

            if ($page->child != NULL) {
                $page->child->link = $baseUrl . $page->slug . '/' . $page->child->slug;
                if ($page->child->admin->photo != NULL) {
                    $page->child->admin->photo = $baseUrl . 'uploads/images/original/' . $page->child->admin->photo;
                }
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>