<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'post') && $decode->post != NULL) {
        if (property_exists($decode, 'comments') && $decode->comments != NULL) {
            foreach ($decode->comments as $comment) {
                if ($comment->total_replies > 0) {
                    foreach ($comment->comment_replies as $reply) {
                        if ($reply->admin->photo != NULL) {
                            $reply->admin->photo = $baseUrl . 'uploads/images/original/' . $reply->admin->photo;
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
                $newFeatured[$init]['original'] = $baseUrl . 'uploads/images/original/' . $featured;
                $newFeatured[$init]['300x300']  = $baseUrl . 'uploads/images/thumbnails/300x300/' . $featured;
                $newFeatured[$init]['480x270']  = $baseUrl . 'uploads/images/thumbnails/480x270/' . $featured;

                $init++;
            }

            $decode->post->featured = $newFeatured;
        }
        else {
            $decode->post->featured = [
                'original' => $baseUrl . 'uploads/images/original/' . $decode->post->featured,
                '300x300'  => $baseUrl . 'uploads/images/thumbnails/300x300/' . $decode->post->featured,
                '480x270'  => $baseUrl . 'uploads/images/thumbnails/480x270/' . $decode->post->featured
            ];
        }

        if ($decode->post->admin->photo != NULL) {
            $decode->post->admin->photo = $baseUrl . 'uploads/images/original/' . $decode->post->admin->photo;
        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>