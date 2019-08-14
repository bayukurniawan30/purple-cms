<div class="row">
    <div class="col-md-7 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">User Detail</h4>
            </div>
            <?php
                echo $this->Form->create($adminEdit, [
                    'id'                    => 'form-update-user',
                    'class'                 => '',
                    'data-parsley-validate' => '',
                    'url'                   => ['action' => 'ajax-update']
                ]);

                echo $this->Form->hidden('id', ['value' => $adminData->id]);

                if ($adminData->photo == '' || $adminData->photo == NULL) {
                    echo $this->Form->hidden('photo', ['value' => 'NULL']);
                }
                else {
                    echo $this->Form->hidden('photo', ['value' => $adminData->photo]);
                }
            ?>
            <div class="card-body">
                <div class="form-group">
                    <?php
                        echo $this->Form->text('username', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Username',
                            'data-parsley-type'      => 'alphanum',
                            'data-parsley-minlength' => '6',
                            'data-parsley-maxlength' => '15',
                            'uk-tooltip'             => 'title: Required. Alpha numeric. Lowercase. 6-15 chars.; pos: bottom',
                            'autofocus'              => 'autofocus',
                            'required'               => 'required',
                            'value'                  => $adminData->username
                        ]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->text('email', [
                            'type'        => 'email',
                            'class'       => 'form-control',
                            'placeholder' => 'Email',
                            'uk-tooltip'  => 'title: Required. Enter a valid email address; pos: bottom',
                            'required'    => 'required',
                            'value'       => $adminData->email
                        ]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->text('display_name', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Display Name',
                            'data-parsley-type'      => 'alphanum',
                            'data-parsley-minlength' => '3',
                            'data-parsley-maxlength' => '50',
                            'uk-tooltip'             => 'title: Required. Alpha numeric. 3-50 chars.; pos: bottom',
                            'required'               => 'required',
                            'value'                  => $adminData->display_name
                        ]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->select(
                            'level',
                            [
                                '1' => 'Administrator', 
                                '2' => 'Editor',
                            ],
                            [
                                'empty'    => 'Select User Level', 
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->textarea('about', [
                            'class'                  => 'form-control', 
                            'placeholder'            => 'About user...',
                            'data-parsley-maxlength' => '500',
                            'rows'                   => '4'
                        ]);
                    ?>
                </div>
             </div>
             <div class="card-footer">
                <?php
                    echo $this->Form->button('Save', [
                        'id'    => 'button-update-user',
                        'class' => 'btn btn-gradient-primary'
                    ]);

                    echo $this->Form->button('Cancel', [
                        'class'   => 'btn btn-outline-primary uk-margin-left',
                        'type'    => 'button',
                        'onclick' => 'location.href = \''.$this->Url->build(['_name' => 'adminUsers']).'\''
                    ]);
                ?>
             </div>
             <?php
                echo $this->Form->end();
            ?>
        </div>
    </div>

    <div class="col-md-5 grid-margin">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title uk-margin-remove-bottom">User Key</h4>
                    </div>
                    <div class="card-body">
                        <div class="uk-alert-primary" uk-alert>
                            <p>User key is used for API request from external application.</p>
                        </div>
                        <div class="uk-inline" style="width: 100%">
                            <a title="Copy User Key" class="uk-form-icon uk-form-icon-flip icon-copy-key" href="#" uk-icon="icon: copy" data-clipboard-target="#purple-user-key" uk-tooltip="title: Copy User Key; pos: bottom"></a>
                            <input id="purple-user-key" class="uk-input" type="text" value="<?= $adminData->api_key_plain ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title uk-margin-remove-bottom">Profile Picture</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($adminData->photo != NULL): ?>
                        <!-- <p class="card-description">
                            Current photo
                        </p> -->

                        <div class="existing-profile-picture" style="width: 100%">
                            <img src="<?= $this->request->webroot . 'uploads/images/original/' . $adminData->photo ?>">
                        </div>
                        <?php endif; ?>

                        <p class="card-description">
                            Or upload image files with format .jpg, .jpeg, or .png
                        </p>

                        <div id="drag-and-drop-zone" class="dm-uploader text-center">
                            <h3 class="mb-5 mt-5 text-muted">Drag &amp; drop files here</h3>

                            <div class="btn btn-primary  mb-5">
                                <span>Open the file Browser</span>
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
        </div>
    </div>
</div>

<div class="row">
    <div id="collapse-uploader-result" class="col-md-12 grid-margin stretch-card collapse">
        <div class="card">
            <div class="card-body" id="demo-files">
                <h4 class="card-title">Progress</h4>
            </div>
        </div>
    </div>

    <div id="collapse-croppie" class="col-md-12 grid-margin stretch-card collapse">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title bind-new-result-title uk-margin-remove-bottom">Crop Images</h4>
            </div>
            <div class="card-body">
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Use Ctrl + Mousewheel to zoom in or zoom out.</p>
                </div>
                
                <div id="croppie-editor" style="min-height: 300px; max-height: auto">
                </div>
                
                <button id="button-crop-image" type="button" class="btn btn-primary" uk-tooltip="Crop Image">
                    <i class="mdi mdi-crop"></i> Crop Image
                </button>
                <button type="button" class="btn btn-inverse-primary btn-icon uk-margin-left image-rotate" data-deg="-90" uk-tooltip="Rotate Left">
                    <i class="mdi mdi-rotate-left"></i>
                </button>
                <button type="button" class="btn btn-inverse-primary btn-icon uk-margin-left image-rotate" data-deg="90" uk-tooltip="Rotate Right">
                    <i class="mdi mdi-rotate-right"></i>
                </button>
                
                <div id="croppie-result">
                    <img class="img-fluid" src="" width="128" height="128">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-update-user').find('select[name=level] option[value="<?= $adminData->level ?>"]').attr("selected","selected");

        $('#drag-and-drop-zone').dmUploader({
            url: '<?= $this->Url->build(["controller" => 'Appearance', "action" => "ajaxImagesUpload"]); ?>',
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
                        img.attr('src', e.target.result);
                        
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
                                    width: 200,
                                    height: 200
                                },
                                boundary: { height: 250 },
                                showZoomer: false,
                                enableResize: false,
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
                                            base64 = canvas.toDataURL();
                                        $.ajax({
                                            type: "POST",
                                            url: "<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxSaveProfilePicture"]); ?>",
                                            headers : {
                                                'X-CSRF-Token': token
                                            },
                                            data: { base64:base64 },
                                            cache: false,
                                            beforeSend: function(){ 
                                            },
                                            success: function(msg){
                                                console.log(msg);
                                                var json    = $.parseJSON(msg),
                                                    status  = (json.status),
                                                    image   = (json.image);

                                                if(status == 'error') {
                                                    var error = (json.error);
                                                }

                                                if (status == 'ok') {
                                                    $("input[name=photo]").val(image);
                                                    $("#croppie-editor").hide();
                                                    $(".image-rotate").hide();
                                                    $(".uk-alert-primary").hide();
                                                    $(".existing-profile-picture").hide();
                                                    btn.html('Result');
                                                    btn.hide();
                                                    $(".bind-new-result-title").html('Result');
                                                    $("#croppie-result").find('img').attr('src', canvas.toDataURL());
                                                }
                                                else if(status == 'error') {
                                                    alert('Error. ' + error);
                                                }

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

        var userEdit = {
            form            : 'form-update-user',
            button          : 'button-update-user',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'index']); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+userEdit.button);
        targetButton.one('click',function() {
            ajaxSubmit(userEdit.form, userEdit.action, userEdit.redirectType, userEdit.redirect, userEdit.btnNormal, userEdit.btnLoading);
        })
    })
</script>