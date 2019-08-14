<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Upload Documents</h4>
            </div>
            <div class="card-body">
                <p class="card-description">
                    Upload document files
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
    
    <div id="bind-media-load" class="col-md-12 grid-margin stretch-card">
        <?php
            if ($docs->count() > 0):
        ?>
        <div class="card">
            <div class="card-body">
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-divider uk-table-middle purple-datatable">
                        <thead>
                            <?php
                                echo $this->Html->tableHeaders([
                                    ['No' => ['width' => '30']],
                                    'Name',
                                    'Size',
                                    'Uploaded By',
                                    ['Action' => ['class' => 'uk-width-small text-center']]
                                ]);
                            ?>
                        </thead>
                        <tbody> 
                            <?php
                                $i = 1;
                                foreach ($docs as $doc):
                                    $filePath = $this->request->getAttribute("webroot") . 'uploads/documents/' . $doc->name;
                            ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><span uk-tooltip="<?= $doc->name ?>"><?= $doc->title ?></span></td>
                                <td><?= $this->Purple->readableFileSize($doc->size) ?></td>
                                <td><?= ucwords($doc->admin->display_name) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-inverse-primary btn-rounded btn-icon button-edit-media" data-purple-id="<?= $doc->id ?>" data-purple-by="<?= $doc->admin->get('display_name') ?>" data-purple-host="<?= $protocol . $this->request->host() ?>" data-purple-file="<?= $filePath ?>" data-purple-created="<?= date('F d, Y H:i', strtotime($doc->created)) ?>" data-purple-title="<?= $doc->title ?>" data-purple-description="<?= $doc->description ?>" uk-tooltip="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-inverse-success btn-rounded btn-icon button-download-media" data-purple-url="<?= $this->Url->build('/uploads/documents/'.$doc->name, true) ?>" data-purple-name="<?= $doc->name ?>" uk-tooltip="Download">
                                        <i class="mdi mdi-download"></i>
                                    </button>
                                    <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon button-delete-media" data-purple-id="<?= $doc->id ?>" data-purple-name="<?= $doc->name ?>" uk-tooltip="Delete">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                                    $i++;
                                endforeach;
                                unset($i);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="modal-edit-media" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($mediaDocumentModal, [
                'id'                    => 'form-edit-media', 
                'class'                 => 'pt-3', 
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-update-documents']
            ]);

            echo $this->Form->hidden('id');
        ?>
        <div class=" uk-modal-body">
            <ul class="uk-breadcrumb">
                <li class="uk-disabled"><a class="bind-by"></a></li>
                <li class="uk-disabled"><a class="bind-created"></a></li>
            </ul>
            <div class="form-group">
                <?php
                    echo $this->Form->label('title', 'Title');
                    echo $this->Form->text('title', [
                        'class'                  => 'form-control', 
                        'placeholder'            => 'Title',
                        'data-parsley-minlength' => '1',
                        'data-parsley-maxlength' => '100',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('path', 'URL');
                ?>
                <div class="input-group">
                <?php
                    echo $this->Form->text('path', [
                        'id'                      => 'target-to-copy',
                        'class'                   => 'form-control', 
                        'placeholder'             => 'URL',
                        'readonly'                => 'readonly',

                    ]);
                ?>
                    <div class="input-group-append">
                        <?php
                            echo $this->Form->button('<i class="mdi mdi-content-copy"></i>', [
                                'id'                    => 'button-clipboard-js',
                                'class'                 => 'btn btn-sm btn-gradient-primary',
                                'type'                  => 'button',
                                'data-clipboard-target' => '#target-to-copy'
                            ]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('description', 'Description');
                    echo $this->Form->textarea('description', [
                        'class'                  => 'form-control', 
                        'placeholder'            => 'Description',
                        'data-parsley-maxlength' => '200'
                    ]);
                ?>
            </div>
            
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php   
                echo $this->Form->button('Save', [
                    'id'    => 'button-edit-media',
                    'class' => 'btn btn-gradient-primary'
                ]);

                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-margin-left uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<div id="modal-delete-media" class="uk-flex-top purple-modal" uk-modal="bg-close: false">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($mediaDocumentDelete, [
                'id'                    => 'form-delete-media', 
                'class'                 => 'pt-3', 
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-delete-documents']
            ]);

            echo $this->Form->hidden('id');
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
                    'id'    => 'button-delete-media',
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
        <?php
            if ($docs->count() > 0):
        ?>
        $('.purple-datatable').DataTable({
            "columnDefs": [{
                "targets": -1,
                "orderable": false
            }]
        });
        <?php endif; ?>
        
        $('#drag-and-drop-zone').dmUploader({
            url: '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxUploadDocuments"]); ?>',
            maxFileSize: 5000000,
            extFilter: ['txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'md', 'pdf'],
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
                var extArray = ['txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'md', 'pdf'];
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
                        var console_response = 'File \'' + file.name + '\' cannot be added: must be a document file';
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

                var createToast = notifToast('Preparing File', 'Upload Complete. Loading file list...', 'success', true);

                setTimeout(function() {
                    $.ajax({
                        type: 'GET',
                        url: '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxDocumentsDetail"]); ?>',
                        success: function(data) {
                            $("#bind-media-load").html(data);
                        }
                    });
                }, 1000);
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
            
        var mediaUpdate = {
            form            : 'form-edit-media', 
            button          : 'button-edit-media',
            action          : 'edit', 
            redirectType    : 'redirect', 
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>', 
            btnNormal       : false, 
            btnLoading      : false 
        };
        
        var targetButton = $("#"+mediaUpdate.button);
        targetButton.one('click',function() {
            ajaxSubmit(mediaUpdate.form, mediaUpdate.action, mediaUpdate.redirectType, mediaUpdate.redirect, mediaUpdate.btnNormal, mediaUpdate.btnLoading);
        });
            
        var mediaDelete = {
            form            : 'form-delete-media', 
            button          : 'button-delete-media',
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
    })
</script>