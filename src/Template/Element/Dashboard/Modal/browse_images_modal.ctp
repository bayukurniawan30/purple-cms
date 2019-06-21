<?php
    if (isset($multiple)) {
        $multiSelect = $multiple;
        if ($multiple == true) {
            $dataMultiple = 'true';
        }
        else {
            $dataMultiple = 'false';
        }
    }
    else {
        $multiSelect  = false;
        $dataMultiple = 'false';
    }
?>

<div id="modal-browse-images" class="uk-modal-full purple-modal" uk-modal style="z-index: 99999999">
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
        <div class="uk-grid-collapse uk-child-width-1-1 uk-flex-middle" uk-grid>
            <div class="uk-animation-slide-top" uk-height-viewport>
                <div class="uk-modal-header">
                    <h3 class="uk-modal-title">Browse Images</h3>
                </div>
                <div id="load-media-list" class="uk-modal-body uk-height-max-large" style="overflow-y: scroll; position: relative;" data-purple-url="<?= $this->Url->build(["_name" => 'adminMediasAction', "action" => "ajaxBrowseImages"]); ?>" data-purple-page-total="<?= $this->Paginator->total() ?>" data-purple-page-limit="<?= $mediaImageLimit ?>" data-purple-multiple="<?= $dataMultiple ?>">
                    <?php
                        if ($browseMedias->count() == 0):
                    ?>
                    <div class="uk-alert-danger" uk-alert>
                        <a class="uk-alert-close" uk-close></a>
                        <p>No media image found. Please upload your files in <a href="<?= $this->Url->build(['_name' => 'adminMediasAction', 'action' => 'images']); ?>">Media Images</a> page.</p>
                    </div>
                    <?php
                        else:
                    ?>
                    <div class="uk-child-width-1-2 uk-child-width-1-6@s uk-grid-small browse-image-container" uk-grid>
                        <?php foreach ($browseMedias as $media): ?>
                        <?php
                            $thumbSquare = '/uploads/images/thumbnails/300x300/' . $media->name;
                        ?>
                        <div class="media-image choose-image" data-purple-image="<?= $media->name ?>">
                            <div class="uk-card uk-card-default">
                                <?= $this->Html->image($thumbSquare, ['alt' => $media->title, 'width' => '100%']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="uk-modal-footer">
                    <div class="uk-child-width-expand@s uk-text-center" uk-grid>
                        <?php
                            if ($mediaImageTotal > $mediaImageLimit):
                        ?>
                        <div class="">
                            <ul class="uk-pagination purple-pagination">
                                <?php
                                    if ($this->Paginator->current() - 1 <= 0) {
                                        $previousUrl = [
                                            'controller' => 'Medias',
                                            'action'     => 'ajaxBrowseImages',
                                            'id'         => $this->Paginator->current() - 0
                                        ];
                                    }
                                    else {
                                        $previousUrl = [
                                            'controller' => 'Medias',
                                            'action'     => 'ajaxBrowseImages',
                                            'id'         => $this->Paginator->current() - 1
                                        ];
                                    }

                                    if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                        $nextUrl = [
                                            'controller' => 'Medias',
                                            'action'     => 'ajaxBrowseImages',
                                            'id'         => $this->Paginator->current() + 0
                                        ];
                                    }
                                    else {
                                        $nextUrl = [
                                            'controller' => 'Medias',
                                            'action'     => 'ajaxBrowseImages',
                                            'id'         => $this->Paginator->current() + 1
                                        ];
                                    }

                                    echo $this->Paginator->prev('<button type="button" class="btn btn-outline-primary btn-icon"><i class="mdi mdi-chevron-left"></i></button>', [
                                        'escape' => false,
                                    ]);
                                    // echo $this->Paginator->numbers();
                                    echo $this->Paginator->next('<button type="button" class="btn btn-outline-primary btn-icon"><i class="mdi mdi-chevron-right"></i></button>', [
                                        'escape' => false,
                                    ]);
                                ?>
                            </ul>
                        </div>
                        <?php
                            endif;
                        ?>
                        <div class="uk-text-right">
                            <button class="btn btn btn-outline-primary uk-modal-close">Cancel</button>
                            <?php
                                if ($browseMedias->count() > 0):
                            ?>
                            <button class="btn btn-gradient-primary button-select-image uk-margin-left" data-purple-image="" disabled>Select Image</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    if ($browseMedias->count() > 0):
?>
<script>
    $(document).ready(function() {
        <?php
            if ($mediaImageTotal > $mediaImageLimit):
        ?>
        $('.purple-pagination .prev').hide();
        $('.purple-pagination .prev button').attr('data-purple-url', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next button').attr('data-purple-url', '<?= $this->Url->build($nextUrl) ?>')

        <?php
            endif;
        ?>

        function clickImage() {
            function toggleClick1() {
                var image     = $(this),
                    container = image.parent(),
                    modal     = $("#modal-browse-images"),
                    filename  = image.data('purple-image');

                <?php
                    if ($multiSelect == false):
                ?>
                    container.find(".media-image .selected-overlay").remove();
                <?php
                    endif;
                ?>
                image.find(".uk-card").append('<div class="uk-overlay-default uk-position-cover selected-overlay">' +
                                '<div class="uk-position-center">' +
                                    '<span uk-overlay-icon="icon: check; ratio: 2"></span>' +
                                '</div>' +
                             '</div>');
                modal.find(".button-select-image").removeAttr('disabled');

                <?php
                    if ($multiSelect == true):
                ?>
                    var imageList = modal.find(".button-select-image").attr('data-purple-image');
                    if (imageList == '') {
                        modal.find(".button-select-image").attr('data-purple-image', filename);
                    }
                    else {
                        modal.find(".button-select-image").attr('data-purple-image', imageList + ',' + filename);   
                    }
                <?php
                    else:
                ?>
                    modal.find(".button-select-image").attr('data-purple-image', filename);
                <?php
                    endif;
                ?>

                $(this).one("click", toggleClick2);
            }

            function toggleClick2() {
                var image     = $(this),
                    container = image.parent(),
                    modal     = $("#modal-browse-images"),
                    filename  = image.data('purple-image'),
                    imageList = modal.find(".button-select-image").attr('data-purple-image');

                image.find(".selected-overlay").remove();
                var newImageList  = imageList.replace(',' + filename, '');
                var newImageList2 = newImageList.replace(filename, '');
                modal.find(".button-select-image").attr('data-purple-image', newImageList2);

                if (modal.find(".button-select-image").attr('data-purple-image') == '') {
                    modal.find(".button-select-image").attr('disabled', 'disabled');
                }
                else {
                    modal.find(".button-select-image").removeAttr('disabled');
                }

                $(this).one("click", toggleClick1);
            }
            $(".media-image").one("click", toggleClick1);
        }

        clickImage();

        $(".button-select-image").on({
            mouseenter: function () {
                var selectAction = $(this).data('purple-action'),
                    actionTable  = $(this).data('purple-table'),
                    actionId     = $(this).data('purple-id'),
                    actionTarget = $(this).data('purple-target'),
                    image        = $(".choose-image"),
                    filename     = $(this).attr('data-purple-image'),
                    modal        = $("#modal-browse-images");

                if (selectAction == 'update') {
                    if (actionTable == 'settings') {
                        var sendData = { id:actionId, value:filename };

                        var selectImage = {
                            button          : '.button-select-image',
                            ajaxType        : 'POST',
                            sendData        : sendData,
                            action          : 'edit',
                            url             : '<?= $this->Url->build(['controller' => 'Settings', 'action' => 'ajaxUpdate']); ?>',
                            redirectType    : 'redirect',
                            redirect        : '<?= $this->Url->build(['controller' => $this->request->getParam('controller'), 'action' => $this->request->getParam('action')]); ?>',
                            btnNormal       : false,
                            btnLoading      : false
                        };

                        ajaxButton(selectImage.button, selectImage.ajaxType, selectImage.sendData, selectImage.action, selectImage.url, selectImage.redirectType, selectImage.redirect, selectImage.btnNormal, selectImage.btnLoading);
                    }
                }
                else if (selectAction == 'send-to-input') {
                    $(".button-select-image").click(function() {
                        $('input[name=' + actionTarget + ']').val(filename);
                        <?php
                            if ($multiSelect == true):
                        ?>
                        var init = '';
                        var findSeparator = filename.search(",");
                        if (findSeparator == -1) {
                            init = '<li><img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' ?>' + filename + '" alt="' + filename + '" uk-cover></li>';
                        }
                        else {
                            var split = filename.split(",");
                            var arrayLength = split.length;
                            for (var i = 0; i < arrayLength; i++) {
                                init += '<li><img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' ?>' + split[i] + '" alt="' + split[i] + '" uk-cover></li>';
                            }
                        }

                        var slideshow = '<div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>' +
                                '<ul class="uk-slideshow-items">' + init +
                                '</ul>' +
                                '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>' +
                                '<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>' +
                            '</div>';
                        $('.browse-image-preview').html(slideshow);
                        <?php
                            else:
                        ?>
                        $('.browse-image-preview').html('<img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' ?>' + filename + '" class="img-fluid">');
                        <?php
                            endif;
                        ?>
                        UIkit.modal(modal).hide();
                        return false;
                    })
                }
                else if (selectAction == 'froala-section-bg') {
                    var block = $(".button-select-image").attr('data-purple-froala-block');
                    $(".button-select-image").click(function() {
                        $(block).css('background-image', 'url(<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' ?>'+filename+')');
                        UIkit.modal(modal).hide();
                        return false;
                    })
                }
                else if (selectAction == 'froala-block-bg') {
                    var block = $(".button-select-image").attr('data-purple-froala-block');
                    $(".button-select-image").click(function() {
                        $(block).css('background-image', 'url(<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' ?>'+filename+')');
                        UIkit.modal(modal).hide();
                        return false;
                    })
                }
            },
            mouseleave: function () {

            }
        })
    })
</script>
<?php endif; ?>