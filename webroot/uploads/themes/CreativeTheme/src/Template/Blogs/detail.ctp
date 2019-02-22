<?php
    $totalTags = $blogTags->count();

    $postDate = date($dateFormat, strtotime($blog->created));

    if ($blog->social_share == 'enable'):
?>
<?= $this->Html->css('/master-assets/plugins/jssocials/css/jssocials.css') ?>
<?= $this->Html->css('/master-assets/plugins/jssocials/css/jssocials-theme-'.$socialTheme.'.css') ?>
<?= $this->Html->script('/master-assets/plugins/jssocials/js/jssocials.min.js'); ?>
<?php endif; ?>

<div class="row">
    <div class="col-xl-12">
        <div class="content-column-content">
            <h1 class="non-uikit"><?= $blog->title ?></h1>

            <ul class="list-unstyled post-author uk-margin-bottom">
                <li class="media">
                    <?php if ($blog->admin->photo === NULL): ?>
                        <img class="initial-photo uk-border-circle mr-3" src="" alt="<?= $blog->admin->display_name ?>" data-name="<?= $blog->admin->display_name ?>" data-height="48" data-width="48" data-char-count="2" data-font-size="24">
                    <?php else: ?>
                        <img class="uk-border-circle mr-3" src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $blog->admin->photo ?>" alt="<?= $blog->admin->display_name ?>" width="48" height="48">
                    <?php endif; ?>
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Posted by <?= $blog->admin->display_name ?> in <a class="non-uikit" href="<?= $this->Url->build(['_name'    => 'postsInCategory','category' => $blog->blog_category->slug]) ?>"><?= $blog->blog_category->name ?></a></h5>
                        <i class="fa fa-calendar"></i> <?= $postDate ?> <i class="fa fa-comments-o uk-margin-left"></i> <?= $this->Purple->plural($totalComments, 'comment', 's', true) ?>
                    </div>
                </li>
            </ul>

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
            <img class="uk-margin" src="<?= $this->request->getAttribute("webroot").'uploads/images/original/'.$blog->featured ?>" data-src="<?= $this->request->getAttribute("webroot").'uploads/images/original/'.$blog->featured ?>" alt="<?= $blog->title ?>" uk-img>
            <?php 
                    endif;
                endif; 
            ?>

            <?= $blog->content ?>
            <p>&nbsp;</p>
            <?php
                if ($blog->social_share == 'enable'):
            ?>
            <h4>Share This Post</h4>

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

            <?php
                if ($blog->comment == 'yes') {
                    if ($totalComments > 0) {
                        echo $this->element('Post/Comment/comments', [
                            'blog_id' => $blog->id
                        ]);
                    }

                    echo $this->element('Post/Comment/comment_form', [
                        'blog_id' => $blog->id
                    ]);
                }
            ?>  
        </div>
    </div>
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