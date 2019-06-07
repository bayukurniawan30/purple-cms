<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Active Theme</h4> 
            </div>
            <?php
                if ($themePreview != ''):
            ?>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text" uk-toggle="target: #modal-preview-active-theme">
                    <i class="mdi mdi-image-area btn-icon-prepend"></i>
                    Preview
                </button>
            </div>
            <?php
                endif;
            ?>
            <div class="card-body <?php if ($readStatus == false) echo 'uk-padding-remove' ?>">
                <?php 
                    if ($readStatus == true):
                ?>
                <ul class="uk-comment-list">
                    <li>
                        <article class="uk-comment uk-visible-toggle">
                            <header class="uk-comment-header uk-position-relative">
                                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                    <div class="uk-width-1-1 uk-width-1-3@m">
                                        <?php 
                                            if ($themeImage == ''):
                                        ?>
                                        <img class="uk-comment-avatar" width="142" src="<?= $this->request->getAttribute("webroot") . 'master-assets/img/default-theme-image.svg' ?>">
                                        <?php
                                            else:
                                                echo $this->Html->image('EngageTheme.'.$themeImage, ['class' => 'uk-comment-avatar', 'width' => '142', 'alt' => $themeName]);
                                            endif;
                                        ?>
                                    </div>
                                    <div class="uk-width-1-1 uk-width-2-3@m">
                                        <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#"><?= $themeName ?></a></h4>
                                        <p class="uk-comment-meta uk-margin-remove-top">
                                            <i class="fa fa-user"></i> <a class="uk-link-reset" href=""><?= $themeAuthor ?></a>
                                            <i class="fa fa-gear uk-margin-left"></i> <a class="uk-link-reset" href=""><?= $themeVersion ?></a>
                                        </p>
                                        <p><?= $themeDesc ?></p>
                                    </div>
                                </div>
                            </header>
                        </article>
                    </li>
                </ul>
                <?php
                    if ($themePreview != ''):
                        $explodePreview = explode(',', $themePreview);
                ?>
                <div id="modal-preview-active-theme" class="uk-flex-top purple-modal" uk-modal>
                    <div class="uk-modal-dialog uk-margin-auto-vertical" style="width: 75%">
                        <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow="animation: push;">
                            <ul class="uk-slideshow-items">
                                <?php
                                    foreach ($explodePreview as $slider):
                                ?>
                                <li>
                                    <?= $this->Html->image('EngageTheme.preview/'.$slider, ['alt' => $themeName, 'width' => '100%']) ?>
                                </li>
                                <?php
                                    endforeach;
                                ?>
                            </ul>

                            <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                            <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
                        </div>
                    </div>
                </div>
                <?php
                    endif;
                ?>
                <?php
                    else:
                ?>
                <div class="uk-alert-danger" uk-alert>
                    <p>Can't read theme information. Please ensure the theme have a <strong>detail.json</strong> file.</p>
                </div>
                <?php 
                    endif; 
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Upload Theme</h4> 
            </div>
            <div class="card-body">
                <div id="drag-and-drop-zone" class="dm-uploader text-center p-3">
                    <h4 class="mb-4 mt-4 text-muted">Drag &amp; drop files here</h4>

                    <div class="btn btn-primary btn-sm mb-4">
                        <span>Open<span class="d-none d-sm-block d-sm-none d-md-block dm-uploader-browser-inline"> the file Browser</span></span>
                        <input type="file" title='Click to add Files' />
                    </div>
                </div>
                <!-- /uploader -->
                <p id="show-progress-button" class="">
                    <button id="button-toggle-progress" type="button" class="btn btn-gradient-primary btn-icon-text btn-sm btn-block" data-toggle="collapse" href="#collapse-uploader-result" role="button" aria-expanded="false" aria-controls="collapse-uploader-result">
                        <i class="mdi mdi-format-list-bulleted-type btn-icon-prepend"></i>
                        Show Progress
                    </button>
                </p>

                <div id="collapse-uploader-result" class="collapse">
                    <div id="demo-files">
                        <h4 class="card-title">Progress</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    if (count($arrayList) > 0):
?>
<div class="uk-child-width-1-3@m" uk-grid="masonry: true">
    <?php
        $i = 1;
        foreach ($arrayList as $list):
            $preview = $list['themePreview'];

            if ($themeName == $list['themeName'] && $themeVersion == $list['themeVersion']):
                echo '';
            else:
    ?>
    <div>
        <div class="uk-card uk-card-default">
            <div class="uk-card-media-top uk-inline">
                <img class="" src="<?php if ($list['themeImage'] == '') echo $this->request->getAttribute("webroot") . 'master-assets/img/default-theme-image.svg'; else echo $this->request->getAttribute("webroot") .'uploads/themes/' . $list['themeFolder'] . '/webroot/img/' . $list['themeImage'] ?>" alt="<?= $list['themeName'].' by '.$list['themeAuthor'] ?>" width="100%">

                <?php
                    if ($list['themeName'] != 'Engage'):
                ?>
                <div class="uk-position-small uk-position-top-right">
                    <button type="button" uk-close uk-tooltip="Delete <?= $list['themeName'] ?>" class="button-delete-purple" data-purple-name="<?= $list['themeName'] ?>" data-purple-folder="<?= $list['themeFolder'] ?>" data-purple-modal="#modal-delete-theme"></button>
                </div>
                <?php
                    endif;
                ?>
            </div>
            <div class="uk-card-body">
                <h4 class="uk-card-title uk-margin-remove"><?= $list['themeName'] ?></h4>
                <p class="uk-margin-remove-top">
                    <i class="fa fa-user"></i> <a class="uk-link-reset" href=""><?= $list['themeAuthor'] ?></a>
                    <i class="fa fa-gear uk-margin-left"></i> <a class="uk-link-reset" href=""><?= $list['themeVersion'] ?></a>
                </p>
                <p><?= $list['themeDesc'] ?></p>
            </div>
            <div class="uk-card-footer text-center">
                <div class="uk-child-width-1-<?php if ($preview != '') echo '2'; else echo '1' ?>" uk-grid="">
                    <div>
                        <a href="#" class="uk-button uk-button-text button-apply-theme" data-purple-folder="<?= $list['themeFolder'] ?>" data-purple-name="<?= $list['themeName'] ?>" data-purple-modal="#modal-apply-theme">Apply</a>
                    </div>

                    <?php
                        if ($preview != ''):
                    ?>
                    <div>
                        <a href="#" uk-toggle="target: #modal-preview-<?= $i ?>" class="uk-button uk-button-text">Preview</a>
                    </div>
                    <?php 
                        endif; 
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
        if ($preview != ''):
            $explodePreview = explode(',', $preview);
    ?>
    <div id="modal-preview-<?= $i ?>" class="uk-flex-top purple-modal" uk-modal>
        <div class="uk-modal-dialog uk-margin-auto-vertical" style="width: 75%">
            <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slideshow="animation: push;">
                <ul class="uk-slideshow-items">
                    <?php
                        foreach ($explodePreview as $slider):
                    ?>
                    <li>
                        <img src="<?= $this->request->getAttribute("webroot") .'uploads/themes/' . $list['themeFolder'] . '/webroot/img/preview/' . $slider ?>" alt="Theme Preview" width="100%">
                    </li>
                    <?php
                        endforeach;
                    ?>
                </ul>

                <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
                <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
            </div>
        </div>
    </div>
    <?php
        endif;
    ?>

    <?php
                $i++;
            endif;
        endforeach;
    ?>
</div>
<?php
    endif;
?>

<div id="modal-apply-theme" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($themeApply, [
                'id'                    => 'form-apply-theme',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-apply-theme']
            ]);

            echo $this->Form->hidden('name');
            echo $this->Form->hidden('folder');
            echo $this->Form->hidden('active', ['value' => $themeName]);
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to set <span class="bind-title"></span> as active theme?</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);

                echo $this->Form->button('Yes, Apply Now', [
                    'id'    => 'button-apply-theme',
                    'class' => 'btn btn-gradient-primary uk-margin-left'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<div id="modal-delete-theme" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($themeDelete, [
                'id'                    => 'form-delete-theme',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-delete']
            ]);

            echo $this->Form->hidden('folder');
            echo $this->Form->hidden('name');
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to delete <span class="bind-title"></span>?</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);

                echo $this->Form->button('Yes, Delete it', [
                    'id'    => 'button-delete-theme',
                    'class' => 'btn btn-gradient-danger uk-margin-left'
                ]);
            ?>
        </div>
        <?php

            echo $this->Form->end();
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxUploadTheme"]); ?>',
            maxFileSize: 20000000,
            extFilter: ['zip'],
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
                var extArray = ['zip'];
                $.danidemo.addFile('#demo-files', id, file);

                /*** Begins Image preview loader ***/
                if (typeof FileReader !== "undefined") {

                    var reader = new FileReader();

                    // Last image added
                    var img = $('#demo-files').find('.demo-image-preview').eq(0);

                    reader.onload = function(e) {
                        img.attr('src', '<?= $this->Url->image('/master-assets/img/icons/icon-document.png') ?>');
                    }

                    reader.readAsDataURL(file);
                    var extension = file.name.split('.').pop().toLowerCase();
                    if($.inArray(extension, extArray) !== -1) {
                        console.log('Allowed');
                        var createToast = notifToast('File Uploading', 'Now uploading...', 'info', true);
                    }
                    else {
                        // Not Allowed
                        console.log('Not Allowed');
                        var console_response = 'File \'' + file.name + '\' cannot be added: must be a zip file';
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
                // console.log(console_response2);

                var createToast = notifToast('Preparing File', 'Upload Complete. Refresing page...', 'success', true);

                setTimeout(function() {
                    window.location='<?= $this->Url->build(['_name' => 'adminThemes']); ?>'
                }, 2000);
            },
            onUploadError: function(id, message) {
                console.log(message);
                var console_response = 'Failed to Upload file #' + id + ': ' + message;
                $.danidemo.updateFileStatus(id, 'error', message);

                $.danidemo.addLog('#demo-debug', 'error', console_response);

                var createToast = notifToast('File Uploading', 'Failed to Upload file #' + id + ': ' + message, 'error');
            },
            onFileTypeError: function(file) {
                var console_response = 'File \'' + file.name + '\' cannot be added: must be a document file';
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

        var applyTheme = {
            form            : 'form-apply-theme',
            button          : 'button-apply-theme',
            action          : 'apply-theme',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(['_name' => 'adminThemes']); ?>',
            btnNormal       : false,
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Applying...'
        };

        var targetButton = $("#"+applyTheme.button);
        targetButton.one('click',function() {
            ajaxSubmit(applyTheme.form, applyTheme.action, applyTheme.redirectType, applyTheme.redirect, applyTheme.btnNormal, applyTheme.btnLoading);
        })

        <?php
            if (count($arrayList) > 0):
        ?>
        var themeDelete = {
            form            : 'form-delete-theme',
            button          : 'button-delete-theme',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+themeDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(themeDelete.form, themeDelete.action, themeDelete.redirectType, themeDelete.redirect, themeDelete.btnNormal, themeDelete.btnLoading);
        })
        <?php
            endif;
        ?>
    })
</script>