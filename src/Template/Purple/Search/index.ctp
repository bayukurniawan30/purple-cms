<?php
    if ($searchResult->count() > 0):
?>
<div class="uk-child-width-1-1" uk-grid>
    <div>
        <div class="uk-alert-primary" uk-alert>
            <p>Found <?= $this->Purple->plural($searchResult->count(), ' post') ?> for <strong><?= $searchText ?></strong></p>
        </div>
    </div>
</div>
<div class="uk-child-width-1-2@s uk-child-width-1-3@m" uk-grid="masonry: true">
    <?php
        foreach ($searchResult as $result):
            if ($result->blog_category->page_id == NULL) {
                $editPostUrl = $this->Url->build([
                    '_name'  => 'adminBlogsEdit',
                    'blogid' => $result->id,
                ]);
            }
            else {
                $editPostUrl = $this->Url->build([
                    '_name'  => 'adminPagesBlogsEdit',
                    'id'     => $result->blog_category->page_id,
                    'slug'   => $result->blog_category->page->slug,
                    'blogid' => $result->id,
                ]);
            }
            
    ?>
    <div>
        <div class="uk-card uk-card-default">
            <?php
                if (!empty($result->featured)):
            ?>
            <div class="uk-card-media-top">
                <?php
                    if (strpos($result->featured, ',') !== false):
                        $imageArray = explode(',', $result->featured);
                ?>
                <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>
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
                    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
                </div>
                <?php
                    else:
                ?>
                <a href="<?= $editPostUrl ?>">
                    <img src="<?= $this->request->getAttribute("webroot").'uploads/images/original/'.$result->featured ?>" alt="">
                </a>
                <?php
                    endif;
                ?>
            </div>
            <?php endif; ?>
            <div class="uk-card-body">
                <h4 class="uk-card-title"><a href="<?= $editPostUrl ?>"><?= $result->title ?></a></h4>
                <p><?php
                        echo $this->Text->truncate(
                            strip_tags($result->content),
                            150,
                            [
                                'ellipsis' => ' ...',
                                'exact'    => false,
                            ]
                        );
                    ?></p>
            </div>
            <div class="uk-card-footer">
                <div class="uk-grid-small uk-flex-middle" uk-grid>
                    <div class="uk-width-auto">
                        <?php if ($result->admin->photo === NULL): ?>
                        <img class="initial-photo uk-border-circle" src="" alt="<?= $result->admin->get('display_name') ?>" data-name="<?= $result->admin->display_name ?>" data-height="40" data-width="40" data-char-count="2" data-font-size="12">
                        <?php else: ?>
                        <img class="uk-border-circle" src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $result->admin->photo ?>" alt="<?= $result->admin->display_name ?>" width="40" height="40" style="max-width: none;">
                        <?php endif; ?>
                    </div>
                    <div class="uk-width-expand">
                        <h5 class="uk-card-title uk-margin-remove-bottom"><?= $result->admin->display_name ?></h5>
                        <p class="uk-text-meta uk-margin-remove-top"><time><?= date($settingsDateFormat.' '.$settingsTimeFormat, strtotime($result->created)) ?></time></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        endforeach;
    ?>
</div>
<?php
    else:
?>
<div class="uk-child-width-1-1" uk-grid>
    <div>
        <div class="uk-alert-danger" uk-alert>
            <p>Can't find what you're looking for. Maybe try another search?</p>
        </div>
    </div>
</div>
<?php
    endif;
?>