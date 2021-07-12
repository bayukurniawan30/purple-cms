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

    if ($selected != NULL) {
        if (strpos($selected, ',') !== false) {
            $explodeSelected = explode(',', $selected);
        }
        else {
            $explodeSelected = [$selected];
        }

        $arrayPathOriginal = [];
        foreach ($explodeSelected as $eSl) {
            $originalPathSelected = $this->cell('Medias::mediaPath', [$eSl, 'image', 'original']);
            array_push($arrayPathOriginal, $originalPathSelected);
        }
        $implodePathOriginal = implode(',', $arrayPathOriginal);
    }
    else {
        $explodeSelected   = NULL;
        $arrayPathOriginal = NULL;
    }
?>

<div id="modal-browse-images-<?= $uniqueId ?>" class="uk-modal-full purple-modal" uk-modal style="z-index: 99999999">
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
        <div class="uk-grid-collapse uk-child-width-1-1 uk-flex-middle" uk-grid>
            <div class="uk-animation-slide-top" uk-height-viewport>
                <div class="uk-modal-header">
                    <h3 class="uk-modal-title">Browse Images</h3>
                </div>
                <div id="load-media-list-<?= $uniqueId ?>" class="uk-modal-body" style="overflow-y: scroll; position: relative; height: calc(100vh - 135px)" data-purple-url="<?= $this->Url->build(["_name" => 'adminMediasAction', "action" => "ajaxBrowseImages", "plugin" => false]); ?>" data-purple-page-total="<?= $this->Paginator->total() ?>" data-purple-page-limit="<?= $mediaImageLimit ?>" data-purple-multiple="<?= $dataMultiple ?>">
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
                            $thumbSquare = $this->cell('Medias::mediaPath', [$media->name, 'image', 'thumbnail::300']);
                            $original    = $this->cell('Medias::mediaPath', [$media->name, 'image', 'original']);
                        ?>
                        <div class="media-image media-image-<?= $uniqueId ?> choose-image" data-purple-image="<?= $media->name ?>" data-purple-path="<?= $original ?>">
                            <div class="uk-card uk-card-default">
                                <?= $this->Html->image($thumbSquare, ['alt' => $media->title, 'width' => '100%']) ?>
                                <?php
                                    if ($explodeSelected != NULL && in_array($media->name, $explodeSelected)):
                                ?>
                                <div class="uk-overlay-default uk-position-cover selected-overlay"><div class="uk-position-center"><span uk-overlay-icon="icon: check; ratio: 2"></span></div></div>
                                <?php
                                    endif;
                                ?>
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
                                            'id'         => $this->Paginator->current() - 0,
                                            'plugin'     => false
                                        ];
                                    }
                                    else {
                                        $previousUrl = [
                                            'controller' => 'Medias',
                                            'action'     => 'ajaxBrowseImages',
                                            'id'         => $this->Paginator->current() - 1,
                                            'plugin'     => false
                                        ];
                                    }

                                    if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                        $nextUrl = [
                                            'controller' => 'Medias',
                                            'action'     => 'ajaxBrowseImages',
                                            'id'         => $this->Paginator->current() + 0,
                                            'plugin'     => false
                                        ];
                                    }
                                    else {
                                        $nextUrl = [
                                            'controller' => 'Medias',
                                            'action'     => 'ajaxBrowseImages',
                                            'id'         => $this->Paginator->current() + 1,
                                            'plugin'     => false
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
                            <button class="btn btn-gradient-primary button-select-image button-select-image-<?= $uniqueId ?> uk-margin-left" data-purple-image="<?= $explodeSelected != NULL ? $selected : '' ?>" data-purple-path="<?= $explodeSelected != NULL ? $implodePathOriginal : '' ?>" disabled>Select Image</button>
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

        if ($("#modal-browse-images-<?= $uniqueId ?> .uk-modal-body").find('.selected-overlay').length > 0) {
            $("#modal-browse-images-<?= $uniqueId ?>").find(".button-select-image-<?= $uniqueId ?>").removeAttr('disabled');
            $("#modal-browse-images-<?= $uniqueId ?>").find(".button-select-image-<?= $uniqueId ?>").prop('disabled', false);
        }

        function clickImage() {
            function toggleClick1() {
                var image     = $(this),
                    container = image.parent(),
                    modal     = $("#modal-browse-images-<?= $uniqueId ?>"),
                    filename  = image.data('purple-image'),
                    filePath  = image.data('purple-path');

                if (image.find('.selected-overlay').length > 0) {
                    var image     = $(this),
                        container = image.parent(),
                        modal     = $("#modal-browse-images-<?= $uniqueId ?>"),
                        filename  = image.data('purple-image'),
                        filePath  = image.data('purple-path'),
                        imageList = modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image');
                        pathList  = modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path');

                    image.find(".selected-overlay").remove();
                    var newImageList  = imageList.replace(',' + filename, '');
                    var newImageList2 = newImageList.replace(filename, '');
                    modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image', newImageList2);

                    var newPathList  = pathList.replace(',' + filePath, '');
                    var newPathList2 = newPathList.replace(filePath, '');
                    modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path', newPathList2);

                    if (modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image') == '') {
                        modal.find(".button-select-image-<?= $uniqueId ?>").attr('disabled', 'disabled');
                    }
                    else {
                        modal.find(".button-select-image-<?= $uniqueId ?>").removeAttr('disabled');
                        modal.find(".button-select-image-<?= $uniqueId ?>").prop('disabled', false);
                        modal.find("button").removeAttr('disabled');
                        modal.find("button").prop('disabled', false);
                    }

                    $(this).one("click", toggleClick1);
                }
                else {
                    <?php
                        if ($multiSelect == false):
                    ?>
                        container.find(".media-image-<?= $uniqueId ?> .selected-overlay").remove();
                    <?php
                        endif;
                    ?>
                    image.find(".uk-card").append('<div class="uk-overlay-default uk-position-cover selected-overlay">' +
                                    '<div class="uk-position-center">' +
                                        '<span uk-overlay-icon="icon: check; ratio: 2"></span>' +
                                    '</div>' +
                                '</div>');

                    modal.find(".button-select-image-<?= $uniqueId ?>").removeAttr('disabled');
                    modal.find(".button-select-image-<?= $uniqueId ?>").prop('disabled', false);
                    modal.find("button").removeAttr('disabled');
                    modal.find("button").prop('disabled', false);

                    <?php
                        if ($multiSelect == true):
                    ?>
                        var imageList = modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image');
                        var pathList  = modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path');
                        if (imageList == '') {
                            modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image', filename);
                            modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path', filePath);
                        }
                        else {
                            modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image', imageList + ',' + filename);   
                            modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path', pathList + ',' + filePath);   
                        }
                    <?php
                        else:
                    ?>
                        modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image', filename);
                        modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path', filePath);
                    <?php
                        endif;
                    ?>

                    $(this).one("click", toggleClick2);
                }
            }

            function toggleClick2() {
                var image     = $(this),
                    container = image.parent(),
                    modal     = $("#modal-browse-images-<?= $uniqueId ?>"),
                    filename  = image.data('purple-image'),
                    filePath  = image.data('purple-path'),
                    imageList = modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image');
                    pathList  = modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path');

                image.find(".selected-overlay").remove();
                var newImageList  = imageList.replace(',' + filename, '');
                var newImageList2 = newImageList.replace(filename, '');
                modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image', newImageList2);

                var newPathList  = pathList.replace(',' + filePath, '');
                var newPathList2 = newPathList.replace(filePath, '');
                modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-path', newPathList2);

                if (modal.find(".button-select-image-<?= $uniqueId ?>").attr('data-purple-image') == '') {
                    modal.find(".button-select-image-<?= $uniqueId ?>").attr('disabled', 'disabled');
                }
                else {
                    modal.find(".button-select-image-<?= $uniqueId ?>").removeAttr('disabled');
                    modal.find(".button-select-image-<?= $uniqueId ?>").prop('disabled', false);
                    modal.find("button").removeAttr('disabled');
                    modal.find("button").prop('disabled', false);
                }

                $(this).one("click", toggleClick1);
            }
            $(".media-image-<?= $uniqueId ?>").one("click", toggleClick1);
        }

        clickImage();

        $(".button-select-image-<?= $uniqueId ?>").on({
            mouseenter: function () {
                var btn          = $(this),
                    selectAction = btn.data('purple-action'),
                    actionTable  = btn.data('purple-table'),
                    actionId     = btn.data('purple-id'),
                    actionTarget = btn.data('purple-target'),
                    image        = $(".choose-image"),
                    filename     = btn.attr('data-purple-image'),
                    filePath     = btn.attr('data-purple-path'),
                    modal        = $("#modal-browse-images-<?= $uniqueId ?>");

                    console.log(modal);

                if (selectAction == 'update') {
                    if (actionTable == 'settings') {
                        var sendData = { id:actionId, value:filename };

                        var selectImage = {
                            button          : '.button-select-image-<?= $uniqueId ?>',
                            ajaxType        : 'POST',
                            sendData        : sendData,
                            action          : 'edit',
                            url             : '<?= $this->Url->build(['plugin' => false, 'controller' => 'Settings', 'action' => 'ajaxUpdate']); ?>',
                            redirectType    : 'redirect',
                            redirect        : '<?= $this->Url->build(['controller' => $this->request->getParam('controller'), 'action' => $this->request->getParam('action')]); ?>',
                            btnNormal       : false,
                            btnLoading      : false
                        };

                        ajaxButton(selectImage.button, selectImage.ajaxType, selectImage.sendData, selectImage.action, selectImage.url, selectImage.redirectType, selectImage.redirect, selectImage.btnNormal, selectImage.btnLoading);
                    }
                }
                else if (selectAction == 'send-to-input') {
                    btn.click(function() {
                        var getPreviewWidth = $('.browse-image-preview-<?= $uniqueId ?>').outerWidth();
                        console.log(getPreviewWidth);
                        $('input[name=' + actionTarget + ']').val(filename);
                        <?php
                            if ($multiSelect == true):
                        ?>
                        if (getPreviewWidth < 768) {
                            var init = '';
                            var findSeparator = filePath.search(",");
                            if (findSeparator == -1) {
                                init = '<li><img src="' + filePath + '" alt="' + filename + '" uk-cover></li>';
                            }
                            else {
                                var split = filePath.split(",");
                                var arrayLength = split.length;
                                for (var i = 0; i < arrayLength; i++) {
                                    init += '<li><img src="' + split[i] + '" alt="' + split[i] + '" uk-cover></li>';
                                }
                            }

                            var slideshow = '<div class="uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)"><div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>' +
                                    '<ul class="uk-slideshow-items">' + init +
                                    '</ul>' +
                                    '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>' +
                                    '<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>' +
                                '</div></div>';
                            $('.browse-image-preview-<?= $uniqueId ?>').html(slideshow);
                        } 
                        else {
                            var init = '';
                            var findSeparator = filePath.search(",");
                            if (findSeparator == -1) {
                                var newFilePath = filePath.replace("original/", "thumbnails/300x300/");

                                init = '<div><a href="' + filePath + '"><img src="' + newFilePath + '" alt="' + filename + '"></a></div>';
                            }
                            else {
                                var split = filePath.split(",");
                                var arrayLength = split.length;
                                for (var i = 0; i < arrayLength; i++) {
                                    var newFilePath = split[i].replace("original/", "thumbnails/300x300/");
                                    init += '<div><a href="' + split[i] + '"><img src="' + newFilePath + '" alt="' + newFilePath + '"></a></div>';
                                }
                            }

                            var grid = '<div class="uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)"><div class="uk-child-width-1-1 uk-child-width-1-6@m" uk-grid uk-lightbox>' + init + '</div></div>';
                            $('.browse-image-preview-<?= $uniqueId ?>').html(grid);
                        }
                        <?php
                            else:
                        ?>
                        $('.browse-image-preview-<?= $uniqueId ?>').html('<div class="uk-flex uk-flex-center" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)" uk-lightbox><a href="' + filePath + '"><img src="' + filePath + '" class="img-fluid"></div></div>');
                        <?php
                            endif;
                        ?>
                        UIkit.modal(modal).hide();
                        return false;
                    })
                }
                else if (selectAction == 'froala-section-bg') {
                    var block = btn.attr('data-purple-froala-block');
                    btn.click(function() {
                        $(block).css('background-image', 'url(' + filePath + ')');
                        UIkit.modal(modal).hide();
                        return false;
                    })
                }
                else if (selectAction == 'froala-block-bg') {
                    var block = btn.attr('data-purple-froala-block');
                    btn.click(function() {
                        $(block).css('background-image', 'url(' + filePath + ')');
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