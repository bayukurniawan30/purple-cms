<?php
    $totalTags = $blogTags->count();

    if ($blog->social_share == 'enable'):
?>
<?= $this->Html->css('/master-assets/plugins/jssocials/css/jssocials.css') ?>
<?= $this->Html->css('/master-assets/plugins/jssocials/css/jssocials-theme-'.$socialTheme.'.css') ?>
<?= $this->Html->script('/master-assets/plugins/jssocials/js/jssocials.min.js'); ?>
<?php endif; ?>

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
                <div class="blog-post uk-margin-top">
                    <?php
                        $postDate = date($dateFormat, strtotime($blog->created));
                    ?>

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
                    <img src="<?= $this->request->getAttribute("webroot").'uploads/images/original/'.$blog->featured ?>" alt="<?= $blog->title ?>" class="img-fluid">
                    <?php 
                            endif;
                        endif; 
                    ?>

                    <!-- <h3 class="blog-post-title <?php if (!empty($blog->featured)) echo 'uk-margin-small-top' ?>"><?= $blog->title ?></h3> -->
                    <p class="blog-post-meta"><?= $postDate ?> by <a href="#"><?= $blog->admin->display_name ?></a> | <a href="<?= $this->Url->build(['_name'    => 'postsInCategory','category' => $blog->blog_category->slug]) ?>"><?= $blog->blog_category->name ?></a> | <i class="fa fa-eye"></i> <?= $totalVisitors ?> | <i class="fa fa-comment"></i> <?= $totalComments ?></p>

                    <?= $blog->content ?>

                    <?php
                        if ($blog->social_share == 'enable'):
                    ?>
                    <div id="content-sharing-butons" style="font-size: <?= $socialFontSize ?>px"></div>
                    <?php endif; ?>

                    <hr>
                    <?php
                        if ($totalTags > 0) {
                            echo $this->element('Post/tags', [
                                'blog_id' => $blog->id,
                                'tags'    => $blogTags
                            ]);
                        }
                    ?>
                </div>

                <?php
                    if ($blog->comment == 'yes') {
                        if ($totalComments > 0):
                            echo $this->element('Post/Comment/comments', [
                                'blog_id' => $blog->id
                            ]);
                        endif;

                        echo $this->element('Post/Comment/comment_form', [
                            'blog_id' => $blog->id
                        ]);
                    }
                ?>  
            </div>

            <aside class="col-md-4 blog-sidebar">
                <?= $this->element('Post/Sidebar/search') ?>

                <?= $this->element('Post/Sidebar/about', [
                    'author' => $blog->admin->display_name,
                    'about'  => $blog->admin->about
                ]) ?>

                <?= $this->element('Post/Sidebar/category', [
                    'page'       => $pageSlug,
                    'categories' => $categories
                ]) ?>

                <?= $this->element('Post/Sidebar/archives', [
                    'page' => $pageSlug
                ]) ?>

                <?= $this->element('Post/Sidebar/tags', [
                    'tags' => $tagsSidebar
                ]) ?>
            </aside><!-- /.blog-sidebar -->
        </div>
    </main>
</div>

<?php
    if ($blog->social_share == 'enable'):
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#content-sharing-butons").jsSocials({
            showCount: <?php if ($socialCount == 'true') echo 'true'; else echo 'false' ?>,
            showLabel: <?php if ($socialLabel == 'true') echo 'true'; else echo 'false' ?>,
            url: "<?= $this->Url->build($this->request->getRequestTarget(), true) ?>",
            shares: [<?= str_replace('\\', '', $socialShare) ?>]
        });
    });
</script>
<?php endif; ?>