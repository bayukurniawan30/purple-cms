<div class="row">
    <?php
        if ($logo->value == ''):
    ?>
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Upload Images</h4>
                <p class="card-description">
                    Upload image files with format .jpg, .jpeg, or .png
                </p>
                <div id="drag-and-drop-zone" class="dm-uploader text-center p-5 purple-medias-dmuploader">
                    <h3 class="mb-5 mt-5 text-muted">Drag &amp; drop files here</h3>

                    <div class="btn btn-primary  mb-5">
                        <span>Open<span class="d-none d-sm-block d-sm-none d-md-block dm-uploader-browser-inline"> the file Browser</span></span>
                        <input type="file" title='Click to add Files' />
                    </div>
                </div>
                <!-- /uploader -->
                <p id="show-progress-button" class="">
                    <button id="button-toggle-progress" type="button" class="btn btn-gradient-primary btn-icon-text btn-sm" data-toggle="collapse" href="#collapse-uploader-result" role="button" aria-expanded="false" aria-controls="collapse-uploader-result">
                        <i class="mdi mdi-format-list-bulleted-type btn-icon-prepend"></i>
                        Show Progress
                    </button>
                </p>
            </div>
        </div>
    </div>
    <div id="collapse-uploader-result" class="col-md-12 grid-margin stretch-card collapse">
        <div class="card">
            <div class="card-body" id="demo-files">
                <h4 class="card-title">Progress</h4>
            </div>
        </div>
    </div>
    
    <div id="collapse-croppie" class="col-md-12 grid-margin stretch-card collapse">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title bind-new-result-title">Crop Images</h4>
                
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Use Ctrl + Mousewheel to zoom in or zoom out.</p>
                </div>
                
                <div id="croppie-editor" style="min-height: 300px; max-height: auto">
                </div>
                
                <button id="button-without-crop-image" type="button" class="btn btn-primary" uk-tooltip="Save without Crop">
                    <i class="mdi mdi-content-save"></i> Save without Crop
                </button>
                <button id="button-crop-image" type="button" class="btn btn-success uk-margin-left" uk-tooltip="Crop Image">
                    <i class="mdi mdi-crop"></i> Crop Image
                </button>
                <button type="button" class="btn btn-inverse-primary btn-icon uk-margin-left image-rotate" data-deg="-90" uk-tooltip="Rotate Left">
                    <i class="mdi mdi-rotate-left"></i>
                </button>
                <button type="button" class="btn btn-inverse-primary btn-icon uk-margin-left image-rotate" data-deg="90" uk-tooltip="Rotate Right">
                    <i class="mdi mdi-rotate-right"></i>
                </button>
                
                <div id="croppie-result">
                    <img class="img-fluid" src="" width="300">
                </div>
            </div>
        </div>
    </div>
    <?php
        else:
    ?>
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div>
                    <img class="img-fluid" src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $logo->value; ?>" width="300">
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-danger button-delete-logo" data-purple-id="<?= $logo->id ?>" data-purple-name="<?= $logo->value ?>" uk-tooltip="Delete Logo">
                    Delete
                </button>
            </div>
        </div>
    </div>
    <?php
        endif;
    ?>
</div>

<?= $this->element('Dashboard/Modal/delete_modal', [
        'action'     => 'logo',
        'form'       => $appearanceDelete,
        'formAction' => 'ajax-delete'
]) ?>

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($logo->value == ''):
        ?>
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxImagesUpload"]); ?>',
            maxFileSize: 3000000,
            multiple: false,
            extFilter: ['jpg', 'jpeg', 'png'],
            headers: {
                'X-CSRF-TOKEN': <?= json_encode($this->request->getParam('_csrfToken')); ?>
            },
            onInit: function() {
                var console_response = 'Plugin initialized correctly';
            },
            onBeforeUpload: function(id) {
                $('#collapse-uploader-result').collapse({
                    toggle: true
                });

                var console_response = 'Starting the upload of #' + id;
                $.danidemo.addLog('#demo-debug', 'default', console_response);

                $.danidemo.updateFileStatus(id, 'default', 'Uploading...');
            },
            onNewFile: function(id, file) {
                var extArray = ['jpg', 'jpeg', 'png'];
                $.danidemo.addFile('#demo-files', id, file);

                /*** Begins Image preview loader ***/
                if (typeof FileReader !== "undefined") {

                    var reader = new FileReader();

                    // Last image added
                    var img = $('#demo-files').find('.demo-image-preview').eq(0);

                    reader.onload = function(e) {
                        // img.attr('src', e.target.result);
                        img.attr('src', '<?= $this->Url->image('/master-assets/img/icons/icon-image.png') ?>');
                        
                        setTimeout(function() {
                            $('#collapse-croppie').collapse({
                                toggle: true
                            });
                            
                            $('html, body').animate({
                                scrollTop: $("#collapse-croppie").offset().top
                            }, 1000);
                        }, 4000);
                        
                        setTimeout(function() {                            
                            var cropImage = $("#croppie-editor").croppie({
                                viewport: {
                                    width: 300,
                                    height: 200
                                },
                                boundary: { height: 250 },
                                showZoomer: false,
                                enableResize: true,
                                enableOrientation: true,
                                mouseWheelZoom: 'ctrl'
                            });

                            cropImage.croppie('bind', {
                                url: e.target.result,
                            });
                            
                            $('.image-rotate').on('click', function(ev) {
                                var value = parseInt($(this).data('deg'));
                                cropImage.croppie('rotate', value);
                            });
                            
                            $("#button-crop-image").click(function () {
                                var btn = $(this);
                                $(this).attr('disabled','disabled');
                                $(this).html('<i class="fa fa-circle-o-notch fa-spin"></i> Cropping...');
                                $(".image-rotate").attr('disabled','disabled');
                                
                                setTimeout(function() {
                                    cropImage.croppie('result', {
                                        type: 'rawcanvas',
                                        circle: false,
                                        format: 'png'
                                    }).then(function (canvas) {
                                        var token  = <?= json_encode($this->request->getParam('_csrfToken')); ?>,
                                            id     = '<?= $logo->id ?>',
                                            base64 = canvas.toDataURL(),
                                            type   = 'logo';
                                        $.ajax({
                                            type: "POST",
                                            url: "<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxSave"]); ?>",
                                            headers : {
                                                'X-CSRF-Token': token
                                            },
                                            data: { id:id, base64:base64, type:type },
                                            cache: false,
                                            beforeSend: function(){ 
                                            },
                                            success: function(data){
                                                console.log(data);
                                                $("#croppie-editor").hide();
                                                $(".image-rotate").hide();
                                                $(".uk-alert-primary").hide();
                                                $("#button-without-crop-image").hide();
                                                btn.html('Result');
                                                btn.hide();
                                                $(".bind-new-result-title").html('Result');
                                                $("#croppie-result").find('img').attr('src', canvas.toDataURL());

                                            }
                                        })
                                    });
                                }, 1000);
                                
                                return false;
                            });
                        }, 5000);
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
                    $('#demo-files').find('.demo-image-preview').remove();
                }
                /*** Ends Image preview loader ***/

            },
            onComplete: function() {
                var console_response = 'All pending tranfers completed';
                $.danidemo.addLog('#demo-debug', 'default', console_response);
                
                $("#show-progress-button").hide();
            },
            onUploadProgress: function(id, percent) {
                $('html, body').animate({
                    scrollTop: $("#collapse-uploader-result").offset().top
                }, 1000);

                $("#show-progress-button").show();

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

                console.log(console_response);

                var json    = $.parseJSON(data),
                    status  = (json.status),
                    image   = (json.name);

                if (status == 'ok') {
                    $("#button-without-crop-image").click(function () {
                        var btn    = $(this),
                            token  = <?= json_encode($this->request->getParam('_csrfToken')); ?>,
                            id     = '<?= $logo->id ?>',
                            type   = 'logo';

                        $(this).attr('disabled','disabled');
                        $("#button-crop-image").attr('disabled','disabled');
                        $(this).html('<i class="fa fa-circle-o-notch fa-spin"></i> Saving...');
                        $(".image-rotate").attr('disabled','disabled');

                        $.ajax({
                            type: "POST",
                            url: "<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxSaveWithoutCrop"]); ?>",
                            headers : {
                                'X-CSRF-Token': token
                            },
                            data: { id:id, image:image, type:type },
                            cache: false,
                            beforeSend: function(){ 
                            },
                            success: function(data){
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            }
                        })
                        return false;
                    })
                }

                var createToast = notifToast('Preparing File', 'Upload Complete. Loading image to crop...', 'success', true);
            },
            onUploadError: function(id, message) {
                console.log(message);
                var console_response = 'Failed to Upload file #' + id + ': ' + message;
                $.danidemo.updateFileStatus(id, 'error', message);

                $.danidemo.addLog('#demo-debug', 'error', console_response);

                var createToast = notifToast('File Uploading', 'Failed to Upload file #' + id + ': ' + message, 'error');
            },
            onFileTypeError: function(file) {
                var console_response = 'File \'' + file.name + '\' cannot be added: must be an image';
                $.danidemo.addLog('#demo-debug', 'error', console_response);
                var createToast = notifToast('File Uploading', console_response, 'error');
            },
            onFileSizeError: function(file) {
                var console_response = 'File \'' + file.name + '\' cannot be added: size excess limit';
                $.danidemo.addLog('#demo-debug', 'error', console_response);
                var createToast = notifToast('File Uploading', console_response, 'error');
            },
            onFallbackMode: function(message) {
                $.danidemo.addLog('#demo-debug', 'info', 'Browser not supported(do something else here!): ' + message);
            }
        });
            
        <?php
            else:
        ?>
        // Trigger delete button to fire modal
        var deleteBtn = deleteButton('logo');
            
        var mediaDelete = {
            form            : 'form-delete-logo', 
            button          : 'button-delete-logo',
            action          : 'delete', 
            redirectType    : 'redirect', 
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>', 
            btnNormal       : false, 
            btnLoading      : false 
        };
        
        var targetButton = $("#"+mediaDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(mediaDelete.form, mediaDelete.action, mediaDelete.redirectType, mediaDelete.redirect, mediaDelete.btnNormal, mediaDelete.btnLoading);
        })
        <?php
            endif;  
        ?>
    })
</script>