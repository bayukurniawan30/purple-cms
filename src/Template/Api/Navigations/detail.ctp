<?php
    $decode = json_decode($json);

    if (property_exists($decode, 'navigation') && $decode->navigation != NULL) {
        foreach ($decode->navigation as $navigation) {
            if ($navigation->page != NULL) {
                $navigation->target = $baseUrl . $navigation->target;
            }

            if ($navigation->child != "") {
                foreach ($navigation->child as $child) {
                    if ($child->page != NULL) {
                        $child->target = $baseUrl . $child->target;
                    }
                }
            }

        }

        echo json_encode($decode, JSON_PRETTY_PRINT);
    }
    else {
        echo $json;
    }
?>