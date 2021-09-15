<?php
    $randomModalBrowseImageId = rand(100000, 999999);

    if (!isset($allowedFormat)) {
        $allowedUploadFormat = ['jpg', 'jpeg', 'png'];
    }
    else {
        if (strpos($allowedFormat, ',') !== false) {
            $allowedUploadFormat = explode(',', $allowedFormat);
        }
        else {
            $allowedUploadFormat = [$allowedFormat];
        }
    }
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title uk-margin-remove-bottom"><?= $widgetTitle ?></h4>
    </div>
    <div class="card-body">
        <p class="card-description">
            Upload image files with format <?= $this->Text->toList($allowedUploadFormat, 'or') ?>
        </p>

        <div id="drag-and-drop-zone" class="dm-uploader text-center drag-and-drop-zone-<?= $randomModalBrowseImageId ?>">
            <h4 class="uk-margin-bottom uk-margin-top text-muted">Drag &amp; drop files here</h4>

            <div class="btn btn-primary btn-sm uk-margin-bottom">
                <span>Open File Browser</span>
                <input type="file" title='Click to add Files' />
            </div>
        </div>
        <!-- /uploader -->
        
        <div class="upload-image-progress upload-image-progress-<?= $randomModalBrowseImageId ?>  uk-margin-top"></div>

        <button type="button" class="btn btn-gradient-primary btn-sm btn-icon-text btn-block button-browse-images" data-purple-target="#modal-browse-images-<?= $randomModalBrowseImageId ?>" data-purple-browse-content="<?= $modalParams['browseContent'] ?>" data-purple-browse-action="<?= $modalParams['browseAction'] ?>" data-purple-browse-target="<?= $modalParams['browseTarget'] ?>">
            <i class="mdi mdi-file-find btn-icon-prepend"></i>
            Browse Uploaded Image
        </button>

        <div class="browse-image-preview-<?= $randomModalBrowseImageId ?> uk-margin-top">
            <?php
                if ($selected != NULL):
                    if (strpos($selected, ',') !== false):
                        $imageArray = explode(',', $selected);

            ?>
                <div class="uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)">
                    <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>
                        <ul class="uk-slideshow-items">
                            <?php
                                foreach ($imageArray as $image):
                                    $fullImage = $this->cell('Medias::mediaPath', [$image, 'image', 'original']);
                            ?>
                            <li>
                                <img src="<?= $fullImage ?>" alt="<?= $image ?>" uk-cover>
                            </li>
                            <?php
                                endforeach;
                            ?>
                        </ul>
                        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
                    </div>
                </div>
            <?php 
                    else:
                        $fullSelectedImage = $this->cell('Medias::mediaPath', [$selected, 'image', 'original']);
            ?>
                <div class="uk-flex uk-flex-center uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)" uk-lightbox><a href="<?= $fullSelectedImage ?>"><img src="<?= $fullSelectedImage ?>" class="img-fluid"></a></div>
            <?php
                    endif; 
                endif; 
            ?>
        </div>
    </div>
</div>

<?= $this->element('Dashboard/Modal/browse_images_modal', [
        'selected'     => $selected, 
        'browseMedias' => $browseMedias,
        'multiple'     => $multiple,
        'uniqueId'     => $randomModalBrowseImageId
]) ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Trigger browse image button to fire modal
        var browseImageBtn = browseImageButton();

        $('.drag-and-drop-zone-<?= $randomModalBrowseImageId ?>').dmUploader({
            url: '<?= $this->Url->build(["_name" => 'adminMediasAction', "action" => "ajaxUploadImages"]); ?>',
            maxFileSize: 3000000,
            multiple: <?php if ($multiple == true): ?>true<?php else: ?>false<?php endif; ?>,
            extFilter: [<?php foreach ($allowedUploadFormat as $allowed) { if (end($allowedUploadFormat) === $allowed) echo '"' . $allowed . '"'; else echo '"' . $allowed . '",';} ?>],
            headers: {
                'X-CSRF-TOKEN': <?= json_encode($this->request->getParam('_csrfToken')); ?>
            },
            onInit: function() {
                var console_response = 'Plugin initialized correctly';
                console.log(console_response)
            },
            onBeforeUpload: function(id) {
                var console_response = 'Starting the upload of #' + id;
                $.danidemo.addLog('#demo-debug', 'default', console_response);

                $.danidemo.updateFileStatus(id, 'default', '<i class="fa fa-circle-o-notch fa-spin"></i> Uploading...');
                $('.upload-image-progress-<?= $randomModalBrowseImageId ?>').css('padding-bottom', '20px');
            },
            onNewFile: function(id, file) {
                $('button').attr('disabled','disabled');

                var extArray = ['jpg', 'jpeg', 'png'];
                $.danidemo.addFile('.upload-image-progress-<?= $randomModalBrowseImageId ?>', id, file);

                /*** Begins Image preview loader ***/
                if (typeof FileReader !== "undefined") {

                    var reader = new FileReader();

                    // Last image added
                    var img = $('.upload-image-progress-<?= $randomModalBrowseImageId ?>').find('.demo-image-preview').eq(0);

                    reader.onload = function(e) {
                        // img.attr('src', e.target.result);
                        img.attr('src', '<?= $this->Url->image('/master-assets/img/icons/icon-image.png') ?>');
                    }

                    reader.readAsDataURL(file);
                    
                    var extension = file.name.split('.').pop().toLowerCase();
                    if($.inArray(extension, extArray) !== -1) {
                        // Allowed
                        console.log('Allowed');
                        var createToast = notifToast('File Uploading', '<i class="fa fa-circle-o-notch fa-spin"></i> Now uploading...', 'info', true);
                    }
                    else {
                        // Not Allowed
                        console.log('Not Allowed');
                        var console_response = 'File \'' + file.name + '\' cannot be added: must be an image';
                        var createToast = notifToast('File Uploading', console_response, 'error');
                    }

                } else {
                    // Hide/Remove all Images if FileReader isn't supported
                    $('.upload-image-progress-<?= $randomModalBrowseImageId ?>').find('.demo-image-preview').remove();
                }
                /*** Ends Image preview loader ***/
            },
            onComplete: function() {
                var console_response = 'All pending tranfers completed';
                $.danidemo.addLog('#demo-debug', 'default', console_response);
                
                $('button').removeAttr('disabled','disabled');
            },
            onUploadProgress: function(id, percent) {
                var percentStr = percent + '%';

                $.danidemo.updateFileProgress(id, percentStr);
            },
            onUploadSuccess: function(id, data) {
                var console_response = 'Upload of file #' + id + ' completed';
                var console_response2 = 'Server Response for file #' + id + ': ' + JSON.stringify(data);

                $('#demo-file' + id).find('.progress-bar').removeClass('progress-bar-animated').addClass('bg-success');

                $.danidemo.addLog('#demo-debug', 'success', console_response);

                $.danidemo.addLog('#demo-debug', 'info', console_response2);

                $.danidemo.updateFileStatus(id, 'success', 'Upload Complete');

                $.danidemo.updateFileProgress(id, '100%');

                var json    = $.parseJSON(data),
                    status  = (json.status),
                    path    = (json.path),
                    image   = (json.name);

                    console.log(image);
                    console.log(path);
                    console.log(status);

                if (status == 'ok') {
                    var checkValue     = $('input[name=<?= $inputTarget ?>]').val();
                    var checkValuePath = $('input[name=<?= $inputTarget ?>]').attr('data-purple-path');
                    <?php
                        if ($multiple == true):
                    ?>
                    var multiUpload = true;
                    <?php
                        else:
                    ?>
                    var multiUpload = false;
                    <?php
                        endif;
                    ?>

                    if (multiUpload == true) {
                        if (checkValue == '') {
                            var newValue   = path;
                            var inputValue = image;
                        }
                        else {
                            var newValue   = checkValuePath + ',' + path;
                            var inputValue = checkValue + ',' + image;
                        }
                        var split       = newValue.split(",");
                        var arrayLength = split.length;
                        var init        = '';

                        var getPreviewWidth = $('.browse-image-preview-<?= $randomModalBrowseImageId ?>').outerWidth();

                        if (getPreviewWidth < 768) {
                            for (var i = 0; i < arrayLength; i++) {
                                init += '<li><img src="' + split[i] + '" alt="' + split[i] + '" uk-cover></li>';
                            }

                            var slideshow = '<div class="uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)"><div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>' +
                                        '<ul class="uk-slideshow-items">' + init +
                                        '</ul>' +
                                        '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>' +
                                        '<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>' +
                                    '</div></div>';
                            $('.browse-image-preview-<?= $randomModalBrowseImageId ?>').html(slideshow);
                        }
                        else {
                            for (var i = 0; i < arrayLength; i++) {
                                var newFilePath = split[i].replace("original/", "thumbnails/300x300/");
                                init += '<div><a href="' + newFilePath + '"><img src="' + newFilePath + '" alt="' + split[i] + '"></a></div>';
                            }

                            var grid = '<div class="uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)"><div class="uk-child-width-1-1 uk-child-width-1-6@m" uk-grid uk-lightbox>' + init + '</div></div>';
                            $('.browse-image-preview-<?= $randomModalBrowseImageId ?>').html(grid);
                        }

                        $('input[name=<?= $inputTarget ?>]').val(inputValue);
                        $('input[name=<?= $inputTarget ?>]').attr('data-purple-path', newValue);

                    }
                    else {
                        $('input[name=<?= $inputTarget ?>]').val(image);
                        $('input[name=<?= $inputTarget ?>]').attr('data-purple-path', path);
                        $('.browse-image-preview-<?= $randomModalBrowseImageId ?>').html('<div class="uk-flex uk-flex-center uk-padding" style="background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQYV2NkQAJXrlz5zwjjgzg6OjqMYAEYB8RmROaABAD+tQ+ACU9dmwAAAABJRU5ErkJggg==)" uk-lightbox><a href="' + path + '"><img src="' + path + '" class="img-fluid"></a></div>');
                    }
                }

                $('button').removeAttr('disabled','disabled');
                var createToast = notifToast('Upload Complete', 'Image has been uploaded. ', 'success', true);

            },
            onUploadError: function(id, message) {
                console.log(message);
                var console_response = 'Failed to Upload file #' + id + ': ' + message;
                $.danidemo.updateFileStatus(id, 'error', message);

                $.danidemo.addLog('#demo-debug', 'error', console_response);

                $('button').removeAttr('disabled','disabled');
                var createToast = notifToast('File Uploading', 'Failed to Upload file #' + id + ': ' + message, 'error');
            },
            onFileTypeError: function(file) {
                var console_response = 'File \'' + file.name + '\' cannot be added: must be an image';
                $.danidemo.addLog('#demo-debug', 'error', console_response);
                $('button').removeAttr('disabled','disabled');
                var createToast = notifToast('File Uploading', console_response, 'error');
            },
            onFileSizeError: function(file) {
                var console_response = 'File \'' + file.name + '\' cannot be added: size excess limit';
                $.danidemo.addLog('#demo-debug', 'error', console_response);
                $('button').removeAttr('disabled','disabled');
                var createToast = notifToast('File Uploading', console_response, 'error');
            },
            onFallbackMode: function(message) {
                $.danidemo.addLog('#demo-debug', 'info', 'Browser not supported(do something else here!): ' + message);
            }
        });
    });
</script>