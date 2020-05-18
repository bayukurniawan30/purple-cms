<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post') && $decode->post != NULL) {
        if (property_exists($decode, 'comments') && $decode->comments != NULL) {
            foreach ($decode->comments as $comment) {
                if ($comment->total_replies > 0) {
                    foreach ($comment->comment_replies as $reply) {
                        if ($reply->admin->photo != NULL) {
                            $reply->admin->photo = $this->cell('Medias::mediaPath', [$reply->admin->photo, 'image', 'original'])->render();
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
                $newFeatured[$init]['original'] = $this->cell('Medias::mediaPath', [$featured, 'image', 'original'])->render();
                $newFeatured[$init]['300x300']  = $this->cell('Medias::mediaPath', [$featured, 'image', 'thumbnail::300'])->render();
                $newFeatured[$init]['480x270']  = $this->cell('Medias::mediaPath', [$featured, 'image', 'thumbnail::480'])->render();

                $init++;
            }

            $decode->post->featured = $newFeatured;
        }
        else {
            $decode->post->featured = [
                'original' => $this->cell('Medias::mediaPath', [$decode->post->featured, 'image', 'original'])->render(),
                '300x300'  => $this->cell('Medias::mediaPath', [$decode->post->featured, 'image', 'thumbnail::300'])->render(),
                '480x270'  => $this->cell('Medias::mediaPath', [$decode->post->featured, 'image', 'thumbnail::480'])->render()
            ];
        }

        if ($decode->post->admin->photo != NULL) {
            $decode->post->admin->photo = $this->cell('Medias::mediaPath', [$decode->post->admin->photo, 'image', 'original'])->render();
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>