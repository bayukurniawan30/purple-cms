<?php
    if ($medias->count() == 0):
?>
<div class="uk-alert-danger" uk-alert>
    <a class="uk-alert-close" uk-close></a>
    <p>No media image found. Please upload your files in <a href="<?= $this->Url->build(['_name' => 'adminMediasAction', 'action' => 'images']); ?>">Media Images</a> page.</p>
</div>
<?php
    else:
?>
<div class="uk-child-width-1-2 uk-child-width-1-6@s uk-grid-small browse-image-container" uk-grid>
    <?php foreach ($medias as $media): ?>
    <?php
        $thumbSquare = '/uploads/images/thumbnails/300x300/' . $media->name;
    ?>
    <div class="media-image choose-image" data-purple-image="<?= $media->name ?>">
        <div class="uk-card uk-card-default">
            <?= $this->Html->image($thumbSquare, ['alt' => $media->title, 'width' => '100%']) ?>
            <?php
                if (in_array($media->name, $imageArray)):
            ?>
            <div class="uk-overlay-default uk-position-cover selected-overlay">
                <div class="uk-position-center">
                    <span uk-overlay-icon="icon: check; ratio: 2"></span>
                </div>
            </div>
            <?php
                endif;
            ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        <?php
            if ($multiSelect == false):
        ?>
        console.log('multi = false');
        <?php
            else:
        ?>
        console.log('multi = true');
        <?php endif; ?>

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
                console.log('toggleClick1');
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

                console.log('toggleClick2');
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
                            url             : '<?= $this->Url->build(['controller' => 'Settings', 'action' => 'ajaxReadNotification']); ?>',
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