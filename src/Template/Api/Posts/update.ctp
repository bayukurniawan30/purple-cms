<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post') && $decode->post != NULL) {
        if ($decode->post->featured == NULL || $decode->post->featured == '') {
            $decode->post->featured = NULL;
        }
        else {
            if (strpos($decode->post->featured, ',') !== false) {
                $explodeFeatured = explode(',', $decode->post->featured);
                $newFeatured = [];
                $init = 0;
                foreach ($explodeFeatured as $featured) {
                    $newFeatured[$init]['original'] = $this->cell('Medias::mediaPath', [$featured, 'image', 'original']);
                    $newFeatured[$init]['300x300']  = $this->cell('Medias::mediaPath', [$featured, 'image', 'thumbnail::300']);
                    $newFeatured[$init]['480x270']  = $this->cell('Medias::mediaPath', [$featured, 'image', 'thumbnail::480']);

                    $init++;
                }

                $decode->post->featured = $newFeatured;
            }
            else {
                $decode->post->featured = [
                    'original' => $this->cell('Medias::mediaPath', [$decode->post->featured, 'image', 'original']),
                    '300x300'  => $this->cell('Medias::mediaPath', [$decode->post->featured, 'image', 'thumbnail::300']),
                    '480x270'  => $this->cell('Medias::mediaPath', [$decode->post->featured, 'image', 'thumbnail::480'])
                ];
            }
        }

        if ($decode->post->admin->photo != NULL) {
            $decode->post->admin->photo = $this->cell('Medias::mediaPath', [$decode->post->admin->photo, 'image', 'original']);
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>