<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post_categories') && $decode->post_categories != NULL) {
        foreach ($decode->post_categories as $category) {
            if ($category->admin->photo != NULL) {
                $category->admin->photo = $baseUrl . 'uploads/images/original/' . $category->admin->photo;
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>