<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post') && $decode->post != NULL) {
        if (property_exists($decode, 'comments') && $decode->comments != NULL) {
            foreach ($decode->comments as $comment) {
                if ($comment->total_replies > 0) {
                    foreach ($comment->comment_replies as $reply) {
                        if ($reply->admin->photo != NULL) {
                            $reply->admin->photo = $this->cell('Medias::mediaPath', [$reply->admin->photo, 'image', 'original']);
                        }
                    }
                }

            }
        }

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

        if ($decode->post->admin->photo != NULL) {
            $decode->post->admin->photo = $this->cell('Medias::mediaPath', [$decode->post->admin->photo, 'image', 'original']);
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>