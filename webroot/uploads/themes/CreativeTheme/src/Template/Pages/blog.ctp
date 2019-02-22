<div class="row">
    <div class="col-xl-12">
        <div class="content-column-content">
            <h1 class="non-uikit"><?= $pageTitle ?></h1>
        </div>
    </div>
</div>

<?php
    if ($blogs->count() > 0):
?>
<div class="grid row">
    <?php
        $i = 1;
        foreach ($blogs as $blog):
            $postDate = date($dateFormat, strtotime($blog->created));
            $url      = $this->Url->build([
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
            $i++;
        endforeach;
    ?>
</div>

<?php
        if ($postsTotal > $postsLimit):
?>
<div class="row">
    <div class="col-xl-12">
        <!-- Pagination -->
        <ul class="uk-pagination purple-pagination">
            <?php
                $this->Paginator->setTemplates([
                    'nextActive'   => '<li><a class="next btn btn-outline-primary non-uikit" href="{{url}}">{{text}}</a></li>',
                    'nextDisabled' => '<li><a class="next btn btn-outline-primary non-uikit disabled" href="{{url}}">{{text}}</a></li>',
                    'prevActive'   => '<li><a class="prev btn btn-outline-primary non-uikit uk-margin-small-right" href="{{url}}">{{text}}</a></li>',
                    'prevDisabled' => '<li><a class="prev btn btn-outline-primary non-uikit uk-margin-small-right disabled" href="{{url}}">{{text}}</a></li>',
                ]);

                if ($this->Paginator->current() - 1 <= 0) {
                    $previousUrl = [
                        '_name'  => 'blogPagination',
                        'page'   => $this->request->getParam('page'),
                        'paging' => $this->Paginator->current() - 0
                    ];
                }
                else {
                    $previousUrl = [
                        '_name'  => 'blogPagination',
                        'page'   => $this->request->getParam('page'),
                        'paging' => $this->Paginator->current() - 1
                    ];
                }

                if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                    $nextUrl = [
                        '_name'  => 'blogPagination',
                        'page'   => $this->request->getParam('page'),
                        'paging' => $this->Paginator->current() + 0
                    ];
                }
                else {
                    $nextUrl = [
                        '_name'  => 'blogPagination',
                        'page'   => $this->request->getParam('page'),
                        'paging' => $this->Paginator->current() + 1
                    ];
                }
                if ($this->Paginator->current() > 1) {
                    echo $this->Paginator->prev('Previous', [
                        'escape' => false,
                    ]);
                }
                // echo $this->Paginator->numbers();
                if ($this->Paginator->current() != $this->Paginator->total()) {
                    echo $this->Paginator->next('Next', [
                        'escape' => false,
                    ]);
                }
            ?>
        </ul>
    </div>
</div>
<?php
        endif;
    else:
?>
<div class="row">
    <div class="col-xl-12">
        <div class="content-column-content">
            <div class="uk-alert-danger" uk-alert>
                <p>Can't find post for this page.</p>
            </div>
        </div>
    </div>
</div>
<?php
    endif;
?>

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($postsTotal > $postsLimit):
        ?>
        $('.purple-pagination .prev').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>
    })
</script>