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

    <main role="main">
        <div class="row">
            <div class="col-md-8 blog-main">
                <?php
                    if ($blogs->count() > 0):
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
                <div class="blog-post">
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
                    <a href="<?= $url ?>" title="" class=""><img src="<?= $this->request->getAttribute("webroot").'uploads/images/original/'.$blog->featured ?>" alt="<?= $blog->title ?>" class="img-fluid"></a>
                    <?php 
                            endif;
                        endif; 
                    ?>

                    <h3 class="blog-post-title <?php if (!empty($blog->featured)) echo 'uk-margin-small-top' ?>"><a href="<?= $url ?>"><?= $blog->title ?></a></h3>
                    <p class="blog-post-meta"><?= $postDate ?> by <a href="#"><?= $blog->admin->display_name ?></a></p>

                    <?php
                        echo $this->Text->truncate(
                            $blog->content,
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

                <?php
                    if ($postsTotal > $postsLimit):
                ?>
                <hr>

                <!-- Pagination -->
                <ul class="uk-pagination purple-pagination">
                    <?php
                        if ($this->Paginator->current() - 1 <= 0) {
                            $previousUrl = [
                                '_name'  => 'archivesPostPagination',
                                'year'   => $this->request->getParam('year'),
                                'month'  => $this->request->getParam('month'),
                                'paging' => $this->Paginator->current() - 0
                            ];
                        }
                        else {
                            $previousUrl = [
                                '_name'  => 'archivesPostPagination',
                                'year'   => $this->request->getParam('year'),
                                'month'  => $this->request->getParam('month'),
                                'paging' => $this->Paginator->current() - 1
                            ];
                        }

                        if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                            $nextUrl = [
                                '_name'  => 'archivesPostPagination',
                                'year'   => $this->request->getParam('year'),
                                'month'  => $this->request->getParam('month'),
                                'paging' => $this->Paginator->current() + 0
                            ];
                        }
                        else {
                            $nextUrl = [
                                '_name'  => 'archivesPostPagination',
                                'year'   => $this->request->getParam('year'),
                                'month'  => $this->request->getParam('month'),
                                'paging' => $this->Paginator->current() + 1
                            ];
                        }
                        if ($this->Paginator->current() > 1) {
                            echo $this->Paginator->prev('<span uk-pagination-previous class="uk-margin-small-right"></span> Previous', [
                                'escape' => false,
                            ]);
                        }
                        // echo $this->Paginator->numbers();
                        if ($this->Paginator->current() != $this->Paginator->total()) {
                            echo $this->Paginator->next('Next <span uk-pagination-next class="uk-margin-small-left"></span>', [
                                'escape' => false,
                            ]);
                        }
                    ?>
                </ul>
                <?php endif; ?>
                <?php
                    else:
                ?>
                <div class="uk-alert-danger" uk-alert>
                    <p>Can't find post for this archive.</p>
                </div>
                <?php
                    endif;
                ?>
            </div>
            <aside class="col-md-4 blog-sidebar">
                <?= $this->element('Post/Sidebar/search') ?>
                
                <?= $this->element('Post/Sidebar/archives', [
                    'page' => NULL
                ]) ?>

                <?= $this->element('Post/Sidebar/tags', [
                    'tags' => $tagsSidebar
                ]) ?>
            </aside><!-- /.blog-sidebar -->
        </div>
    </main>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($postsTotal > $postsLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>
    })
</script>