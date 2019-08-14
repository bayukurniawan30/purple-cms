<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'posts') && $decode->posts != NULL) {
        foreach ($decode->posts as $post) {
            if ($post->featured == NULL || $post->featured == '') {
                $post->featured = NULL;
            }
            else {
                if (strpos($post->featured, ',') !== false) {
                    $explodeFeatured = explode(',', $post->featured);
                    $newFeatured = [];
                    $initFeatured = 0;
                    foreach ($explodeFeatured as $featured) {
                        $newFeatured[$initFeatured]['original'] = $baseUrl . 'uploads/images/original/' . $featured;
                        $newFeatured[$initFeatured]['300x300']  = $baseUrl . 'uploads/images/thumbnails/300x300/' . $featured;
                        $newFeatured[$initFeatured]['480x270']  = $baseUrl . 'uploads/images/thumbnails/480x270/' . $featured;

                        $initFeatured++;
                    }

                    $post->featured = $newFeatured;
                }
                else {
                    $post->featured = [
                        'original' => $baseUrl . 'uploads/images/original/' . $post->featured,
                        '300x300'  => $baseUrl . 'uploads/images/thumbnails/300x300/' . $post->featured,
                        '480x270'  => $baseUrl . 'uploads/images/thumbnails/480x270/' . $post->featured
                    ];
                }
            }

            if ($post->admin->photo != NULL) {
                $post->admin->photo = $baseUrl . 'uploads/images/original/' . $post->admin->photo;
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>