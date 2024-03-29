<div class="container">
  	<div class="row">
        <div class="col-md-6 uk-margin-top page-title-container">
        	<h3 class="page-title"><?= $pageTitle ?></h3>
        </div>
        <div class="col-md-6 breadcrumb-container">
        	<?= $this->element('breadcrumb', [
		        'breadcrumb' => $breadcrumb
			]) ?>
        </div>
    </div>

    <?php
        if ($searchResult->count() > 0):
    ?>
    <main role="main">
        <div class="row">
            <div class="col-md-8 blog-main">
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
                <div class="blog-post">
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
                                <img src="<?= $this->cell('Medias::mediaPath', [$image, 'image', 'original']) ?>" alt="<?= $image ?>" uk-cover>
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
                    <a href="<?= $url ?>" title="" class="box-masonry-image with-hover-overlay with-hover-icon"><img src="<?= $this->cell('Medias::mediaPath', [$result->featured, 'image', 'original']) ?>" alt="<?= $result->title ?>" class="img-fluid"></a>
                    <?php 
                            endif;
                        endif; 
                    ?>

                    <h3 class="blog-post-title <?php if (!empty($result->featured)) echo 'uk-margin-small-top' ?>"><a href="<?= $url ?>"><?= $result->title ?></a></h3>
                    <p class="blog-post-meta"><?= $postDate ?> by <a href="#"><?= $result->admin->display_name ?></a></p>

                    <?php
                        echo $this->Text->truncate(
                            $result->content,
                            500,
                            [
                                'ellipsis' => '...',
                                'exact'    => false,
                                'html'     => true
                            ]
                        );
                    ?>
                    <p>
                        <a href="<?= $url ?>">Continue reading...</a>
                    </p>

                </div><!-- /.blog-post -->
                <?php
                        $i++;
                    endforeach;
                ?>
            </div><!-- /.blog-main -->

            <aside class="col-md-4 blog-sidebar">
                <?= $this->element('Post/Sidebar/search') ?>
                
                <?= $this->element('Post/Sidebar/archives') ?>
                
                <?= $this->element('Post/Sidebar/tags', [
                    'tags' => $tagsSidebar
                ]) ?>
            </aside><!-- /.blog-sidebar -->
        </div><!-- /.row -->
    </main><!-- /.container -->
    <?php
        else:
    ?>
    <main role="main">
        <div class="row">
            <div class="col-md-12">
                <div class="uk-alert-danger" uk-alert>
                    <p>Can't find post containing the words you searched for.</p>
                </div>
            </div>
        </div>
    </main>
    <?php
        endif;
    ?>
</div>