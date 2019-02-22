<div class="row">
    <div class="col-xl-12">
        <div class="content-column-content">
            <h1 class="has-subtitle non-uikit"><?= $pageTitle ?></h1>
            <p class="subtitle"><em><?= $searchText ?></em></p>
        </div>
    </div>
</div>

<?php
    if ($searchResult->count() > 0):
?>
<div class="grid row">
    <?php
        $i = 1;
        foreach ($searchResult as $result):
            $postDate = date($dateFormat, strtotime($result->created));
            $url      = $this->Url->build([
                '_name' => 'specificPost',
                'year'  => date('Y', strtotime($result->created)),
                'month' => date('m', strtotime($result->created)),
                'date'  => date('d', strtotime($result->created)),
                'post'  => $result->slug
            ]);
    ?>
    <div class="col-md-6 col-lg-6 grid-item"> 
        <div class="box-masonry"> 
            <?php
                if (!empty($result->featured)):
                    if (strpos($result->featured, ',') !== false):
                        $imageArray = explode(',', $result->featured);
            ?>
            <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow="animation: fade; autoplay: true; autoplay-interval: 5000; pause-on-hover: true">
                <ul class="uk-slideshow-items">
                    <?php
                        foreach ($imageArray as $image):
                    ?>
                    <li>
                        <img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $image ?>" alt="<?= $image ?>" uk-cover>
                    </li>
                    <?php
                        endforeach;
                    ?>
                </ul>
                <a class="uk-position-center-left uk-position-small non-uikit uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                <a class="uk-position-center-right uk-position-small non-uikit uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
            </div>
            <?php
                    else:
            ?>
            <a href="<?= $url ?>" title="" class="box-masonry-image with-hover-overlay with-hover-icon"><img src="<?= $this->request->getAttribute("webroot").'uploads/images/original/'.$blog->featured ?>" alt="<?= $blog->title ?>" class="img-fluid"></a>
            <?php 
                    endif;
                endif; 
            ?>
            <div class="box-masonry-text"> 
                <h4 class="non-uikit">
                    <a class="non-uikit" href="<?= $url ?>"><?= $result->title ?></a>
                </h4>
                <div class="box-masonry-desription">
                    <p>
                        <?php
                            echo $this->Text->truncate(
                                strip_tags($result->content),
                                300,
                                [
                                    'ellipsis' => '...',
                                    'exact'    => false,
                                ]
                            );
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
            $i++;
        endforeach;
    ?>
</div>
<?php
    else:
?>
<div class="row">
    <div class="col-xl-12">
        <div class="content-column-content">
            <div class="uk-alert-danger" uk-alert>
                <p>Can't find post containing the words you searched for.</p>
            </div>
        </div>
    </div>
</div>
<?php
    endif;
?>