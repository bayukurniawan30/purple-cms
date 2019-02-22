<div class="row">
    <div class="col-md-12">
        <?php
            echo $this->Form->create($sidebarSearch, [
                'id'                    => 'form-search',
                'class'                 => 'form-search',
                'data-parsley-validate' => '',
                'url'                   => ['controller' => 'Search', 'action' => 'index'],
            ]);
        ?>
        <div class="controls">
            <div class="form-group">
                <div class="uk-inline" style="width: 100%;">
                    <span class="uk-form-icon" uk-icon="icon: search"></span>
                    <?php
                        echo $this->Form->text('search', [
                            'class'                  => 'form-control uk-input search-input-home',
                            'placeholder'            => 'Search Post...',
                            'data-parsley-minlength' => '2',
                            'data-parsley-maxlength' => '100',
                            'value'                  => '',
                            'required'               => 'required'
                        ]);
                    ?>
                </div>
            </div>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<div class="grid row">
    <?php
        if ($homeBlogs->count() > 0):
            foreach ($homeBlogs as $blog):
                $postDate = date($dateFormat, strtotime($blog->created));
                
                $url = $this->Url->build([
                    '_name' => 'specificPost',
                    'year'  => date('Y', strtotime($blog->created)),
                    'month' => date('m', strtotime($blog->created)),
                    'date'  => date('d', strtotime($blog->created)),
                    'post'  => $blog->slug
                ]);
    ?>
    <div class="col-md-6 col-lg-6 grid-item"> 
        <div class="box-masonry"> 
            <?php
                if (!empty($blog->featured)):
                    if (strpos($blog->featured, ',') !== false):
                        $imageArray = explode(',', $blog->featured);
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
                <h4 class="non-uikit uk-margin-remove-bottom">
                    <a class="non-uikit" href="<?= $url ?>"><?= $blog->title ?></a> 
                </h4>
                <p class="post-info-card"><?= $postDate ?> by <?= $blog->admin->display_name ?></p>
                <div class="box-masonry-desription">
                    <p>
                        <?php
                            echo $this->Text->truncate(
                                strip_tags($blog->content),
                                200,
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
            endforeach;
        endif;
    ?>
</div>