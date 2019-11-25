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
                        $newFeatured[$initFeatured]['original'] = $this->cell('Medias::mediaPath', [$featured, 'image', 'original']);
                        $newFeatured[$initFeatured]['300x300']  = $this->cell('Medias::mediaPath', [$featured, 'image', 'thumbnail::300']);
                        $newFeatured[$initFeatured]['480x270']  = $this->cell('Medias::mediaPath', [$featured, 'image', 'thumbnail::480']);

                        $initFeatured++;
                    }

                    $post->featured = $newFeatured;
                }
                else {
                    $post->featured = [
                        'original' => $this->cell('Medias::mediaPath', [$post->featured, 'image', 'original']),
                        '300x300'  => $this->cell('Medias::mediaPath', [$post->featured, 'image', 'thumbnail::300']),
                        '480x270'  => $this->cell('Medias::mediaPath', [$post->featured, 'image', 'thumbnail::480'])
                    ];
                }
            }

            if ($post->admin->photo != NULL) {
                $post->admin->photo = $this->cell('Medias::mediaPath', [$post->admin->photo, 'image', 'original']);
            }
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>