<div class="card">
    <div class="card-header">
        <h4 class="card-title uk-margin-remove-bottom"><?= $widgetTitle ?></h4>
    </div>
    <div class="card-body">
        <p class="card-description">
            Upload image files with format .jpg, .jpeg, or .png
        </p>

        <div id="drag-and-drop-zone" class="dm-uploader text-center">
            <h4 class="uk-margin-bottom uk-margin-top text-muted">Drag &amp; drop files here</h4>

            <div class="btn btn-primary btn-sm uk-margin-bottom">
                <span>Open File Browser</span>
                <input type="file" title='Click to add Files' />
            </div>
        </div>
        <!-- /uploader -->
        
        <div class="upload-image-progress uk-margin-top"></div>

        <button type="button" class="btn btn-gradient-primary btn-sm btn-icon-text btn-block button-browse-images" data-purple-target="#modal-browse_images" data-purple-browse-content="<?= $modalParams['browseContent'] ?>" data-purple-browse-action="<?= $modalParams['browseAction'] ?>" data-purple-browse-target="<?= $modalParams['browseTarget'] ?>">
            <i class="mdi mdi-file-find btn-icon-prepend"></i>
            Browse Uploaded Image
        </button>

        <div class="browse-image-preview uk-margin-top">
            <?php
                if ($selected != NULL):
                    if (strpos($selected, ',') !== false):
                        $imageArray = explode(',', $selected);
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
                <img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $selected ?>" class="img-fluid">
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
        'multiple'     => $multiple
]) ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Trigger browse image button to fire modal
        var browseImageBtn = browseImageButton();

        $('#drag-and-drop-zone').dmUploader({
            url: '<?= $this->Url->build(["_name" => 'adminMediasAction', "action" => "ajaxUploadImages"]); ?>',
            maxFileSize: 3000000,
            multiple: <?php if ($multiple == true): ?>true<?php else: ?>false<?php endif; ?>,
            extFilter: ['jpg', 'jpeg', 'png'],
            headers: {
                'X-CSRF-TOKEN': <?= json_encode($this->request->getParam('_csrfToken')); ?>
            },
            onInit: function() {
                var console_response = 'Plugin initialized correctly';
            },
            onBeforeUpload: function(id) {
                var console_response = 'Starting the upload of #' + id;
                $.danidemo.addLog('#demo-debug', 'default', console_response);

                $.danidemo.updateFileStatus(id, 'default', 'Uploading...');
                $('.upload-image-progress').css('padding-bottom', '20px');
            },
            onNewFile: function(id, file) {
                $('button').attr('disabled','disabled');

                var extArray = ['jpg', 'jpeg', 'png'];
                $.danidemo.addFile('.upload-image-progress', id, file);

                /*** Begins Image preview loader ***/
                if (typeof FileReader !== "undefined") {

                    var reader = new FileReader();

                    // Last image added
                    var img = $('.upload-image-progress').find('.demo-image-preview').eq(0);

                    reader.onload = function(e) {
                        // img.attr('src', e.target.result);
                        img.attr('src', '<?= $this->Url->image('/master-assets/img/icons/icon-image.png') ?>');
                    }

                    reader.readAsDataURL(file);
                    
                    var extension = file.name.split('.').pop().toLowerCase();
                    if($.inArray(extension, extArray) !== -1) {
                        // Allowed
                        console.log('Allowed');
                        var createToast = notifToast('File Uploading', 'Now uploading...', 'info', true);
                    }
                    else {
                        // Not Allowed
                        console.log('Not Allowed');
                        var console_response = 'File \'' + file.name + '\' cannot be added: must be an image';
                        var createToast = notifToast('File Uploading', console_response, 'error');
                    }

                } else {
                    // Hide/Remove all Images if FileReader isn't supported
                    $('.upload-image-progress').find('.demo-image-preview').remove();
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

                $.danidemo.addLog('#demo-debug', 'success', console_response);

                $.danidemo.addLog('#demo-debug', 'info', console_response2);

                $.danidemo.updateFileStatus(id, 'success', 'Upload Complete');

                $.danidemo.updateFileProgress(id, '100%');

                var json    = $.parseJSON(data),
                    status  = (json.status),
                    image   = (json.name);

                    console.log(image);
                    console.log(status);

                if (status == 'ok') {
                    var checkValue = $('input[name=<?= $inputTarget ?>]').val();
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
                            var newValue = image;
                        }
                        else {
                            var newValue = checkValue + ',' + image;
                        }
                        var split       = newValue.split(",");
                        var arrayLength = split.length;
                        var init        = '';

                        for (var i = 0; i < arrayLength; i++) {
                            init += '<li><img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' ?>' + split[i] + '" alt="' + split[i] + '" uk-cover></li>';
                        }

                        var slideshow = '<div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow>' +
                                    '<ul class="uk-slideshow-items">' + init +
                                    '</ul>' +
                                    '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>' +
                                    '<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>' +
                                '</div>';
                        $('.browse-image-preview').html(slideshow);
                        $('input[name=<?= $inputTarget ?>]').val(newValue);

                    }
                    else {
                        $('input[name=<?= $inputTarget ?>]').val(image);
                        $('.browse-image-preview').html('<img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' ?>' + image + '" class="img-fluid">');
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