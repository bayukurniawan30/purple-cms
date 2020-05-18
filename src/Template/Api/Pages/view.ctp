<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'pages') && $decode->pages != NULL) {
        foreach ($decode->pages as $page) {
            $page->permalink = $baseUrl . $page->slug;

            if ($page->admin->photo != NULL) {
                $page->admin->photo = $this->cell('Medias::mediaPath', [$page->admin->photo, 'image', 'original'])->render();
            }

            if ($page->child != NULL) {
                $page->child->permalink = $baseUrl . $page->slug . '/' . $page->child->slug;
                if ($page->child->admin->photo != NULL) {
                    $page->child->admin->photo = $this->cell('Medias::mediaPath', [$page->child->admin->photo, 'image', 'original'])->render();
                }
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>