<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Upload Images</h4>
            </div>
            <div class="card-body">
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

    <div id="uk-filtering" class="col-md-12 grid-margin" uk-filter="target: .js-filter" data-uk-filter>
        <!-- <div class="row" style="margin-left: 0; margin-right: 0; width: 100%">
            <?php
                if ($medias->count() > 0):
            ?>
            <div class="col-md-12 grid-margin">
               <ul class="" uk-tab>
                    <li class="uk-active" uk-filter-control="sort: data-date"><a href="#"><span uk-icon="arrow-down"></span></a></li>
                    <li uk-filter-control="sort: data-date; order: desc"><a href="#"><span uk-icon="arrow-up"></span></a></li>
                </ul>
            </div>
            <?php
                endif;
            ?>
        </div> -->
        
        <div id="bind-media-load" class="js-filter row">
            <?php foreach ($medias as $media): ?> 
            <?php
                    $thumbSquare = $this->cell('Medias::mediaPath', [$media->name, 'image', 'thumbnail::300']);
                    $fullImage   = $this->cell('Medias::mediaPath', [$media->name, 'image', 'original']);
                    $previousId  = $this->cell('Medias::previousId', [$media->id]);
                    $nextId      = $this->cell('Medias::nextId', [$media->id]);

                    if ($previousId == '0') {
                        $previousUrl = '#';
                    }
                    else {
                        $previousUrl = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]) . "?id=" . $previousId;
                    }

                    if ($nextId == '0') {
                        $nextUrl = '#';
                    }
                    else {
                        $nextUrl = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]) . "?id=" . $nextId;
                    }

                    $colorsUrl = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'ajaxGetImageColors']);
                ?>
                <div class="col-6 col-md-2 grid-margin" data-date="<?= date($settingsDateFormat.' '.$settingsTimeFormat, strtotime($media->created)) ?>">
                    <div>
                        <div class="uk-card uk-card-default">
                            <div class="uk-card-media-top">
                                <a class="media-link-to-image" href="#modal-full-content" data-purple-id="<?= $media->id ?>" data-purple-by="<?= $media->admin->get('display_name') ?>" data-purple-host="<?= $protocol . $this->request->host() ?>" data-purple-image="<?= $fullImage ?>" data-purple-created="<?= date('F d, Y H:i', strtotime($media->created)) ?>" data-purple-next-url="<?= $nextUrl ?>" data-purple-previous-url="<?= $previousUrl ?>" data-purple-colors-url="<?= $colorsUrl ?>" title="<?= $media->title ?>" data-purple-description="<?= $media->description ?>"><?= $this->Html->image($thumbSquare, ['alt' => $media->title, 'width' => '100%']) ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php
                if ($mediaImageTotal > $mediaImageLimit):
            ?>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="media-images-page-counter">Page <?= $this->Paginator->counter() ?></div>
                    </div>
                    <div class="col-12 col-md-6">
                        <ul class="uk-pagination purple-pagination uk-flex-right media-images-pageing">
                            <?php
                                if ($this->Paginator->current() - 1 <= 0) {
                                    $previousUrl = [
                                        '_name' => 'adminMediasImagesPagination',
                                        'id'    => $this->Paginator->current() - 0
                                    ];
                                }
                                else {
                                    $previousUrl = [
                                        '_name' => 'adminMediasImagesPagination',
                                        'id'    => $this->Paginator->current() - 1
                                    ];
                                }

                                if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                    $nextUrl = [
                                        '_name' => 'adminMediasImagesPagination',
                                        'id'    => $this->Paginator->current() + 0
                                    ];
                                }
                                else {
                                    $nextUrl = [
                                        '_name' => 'adminMediasImagesPagination',
                                        'id'    => $this->Paginator->current() + 1
                                    ];
                                }

                                echo $this->Paginator->prev('<span uk-pagination-previous class="uk-margin-small-right"></span> Previous', [
                                    'escape' => false,
                                ]);
                                // echo $this->Paginator->numbers();
                                echo $this->Paginator->next('Next <span uk-pagination-next class="uk-margin-small-left"></span>', [
                                    'escape' => false,
                                ]);
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
                endif;
            ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-full-content" class="uk-modal-full purple-modal" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
        <div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
            <div class="uk-background-contain uk-animation-slide-left bind-background" style="" uk-height-viewport>
                <a href="#" id="media-image-previous-url">
                    <div class="uk-position-center-left uk-overlay uk-overlay-default"><span uk-icon="chevron-left"></span></div>
                </a>
                <a href="#" id="media-image-next-url">
                    <div class="uk-position-center-right uk-overlay uk-overlay-default"><span uk-icon="chevron-right"></span></div>
                </a>
            </div>
            <div class="uk-padding-large uk-animation-slide-right">
                <ul class="uk-breadcrumb">
                    <li class="uk-disabled"><a class="bind-by"></a></li>
                    <li class="uk-disabled purple-mobile-media-breadcrumb"><a class="bind-created"></a></li>
                </ul>
                <p class="bind-colors"></p>
                <?php
                    echo $this->Form->create($mediaImageModal, [
                        'id'                    => 'form-image-detail', 
                        'class'                 => 'pt-3', 
                        'data-parsley-validate' => '',
                        'url'                   => ['action' => 'ajax-update-images']
                    ]);

                    echo $this->Form->hidden('id');
                ?>
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
                <div class="form-group purple-media-buttons">
                <?php   
                    echo $this->Form->button('Save', [
                        'id'    => 'button-image-detail',
                        'class' => 'btn btn-gradient-primary'
                    ]);
                
                    echo $this->Form->button('Cancel', [
                        'id'           => 'button-close-modal',
                        'class'        => 'btn btn-outline-primary uk-margin-left uk-modal-close',
                        'type'         => 'button',
                        'data-target'  => '.purple-modal'
                    ]);
                
                    echo $this->Form->button('Delete', [
                        'class'        => 'btn btn-outline-danger uk-margin-left button-delete-media-image',
                        'type'         => 'button',
                        'data-id'      => ''
                    ]);
                
                    echo $this->Form->end();
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    if ($this->request->getQuery('id') !== NULL):
        if ($detail !== NULL):
            $fullImageDetail   = $this->cell('Medias::mediaPath', [$detail->name, 'image', 'original']);
            $previousIdDetail  = $this->cell('Medias::previousId', [$detail->id]);
            $nextIdDetail      = $this->cell('Medias::nextId', [$detail->id]);
            // $colorsDetail      = $this->cell('Medias::colorExtract', [$fullImageDetail]);
            // $explodeColors     = explode(",", $colorsDetail);

            if ($previousIdDetail == '0') {
                $previousUrlDetail = '#';
            }
            else {
                $previousUrlDetail = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]) . '?id=' . $previousIdDetail;
            }

            if ($nextIdDetail == '0') {
                $nextUrlDetail = '#';
            }
            else {
                $nextUrlDetail = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]) . '?id=' . $nextIdDetail;
            }

            $colorsUrl = $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'ajaxGetImageColors']);
?>
<div id="modal-full-content-initial" class="uk-modal-full purple-modal" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
        <div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
            <div class="uk-background-contain uk-animation-slide-left bind-background" style="background-image: url(<?= $fullImageDetail ?>); background-color: #ffffff" uk-height-viewport>
                <a href="<?= $previousUrlDetail ?>" id="media-image-previous-url">
                    <div class="uk-position-center-left uk-overlay uk-overlay-default"><span uk-icon="chevron-left"></span></div>
                </a>
                <a href="<?= $nextUrlDetail ?>" id="media-image-next-url">
                    <div class="uk-position-center-right uk-overlay uk-overlay-default"><span uk-icon="chevron-right"></span></div>
                </a>
            </div>
            <div class="uk-padding-large uk-animation-slide-right">
                <ul class="uk-breadcrumb">
                    <li class="uk-disabled"><a class="bind-by">Uploaded by <?= $detail->admin->get('display_name') ?></a></li>
                    <li class="uk-disabled purple-mobile-media-breadcrumb"><a class="bind-created">Uploaded at <?= date('F d, Y H:i', strtotime($media->created)) ?></a></li>
                </ul>
                <p class="bind-colors">
                    <i class="fa fa-circle-o-notch fa-spin"></i> Getting image colors...
                    <?php
                        // foreach ($explodeColors as $colorExtract):
                    ?>
                    <!-- <a href="#" class="uk-margin-left-small" style="color: <?= $colorExtract ?>" title="<?= $colorExtract ?>"><i class="fa fa-square"></i></a> -->
                    <?php
                        // endforeach;
                    ?>
                </p>
                <?php
                    echo $this->Form->create($mediaImageModal, [
                        'id'                    => 'form-image-detail-initial', 
                        'class'                 => 'pt-3', 
                        'data-parsley-validate' => '',
                        'url'                   => ['action' => 'ajax-update-images']
                    ]);

                    echo $this->Form->hidden('id', ['value' => $detail->id]);
                ?>
                <div class="form-group">
                    <?php
                        echo $this->Form->label('title', 'Title');
                        echo $this->Form->text('title', [
                            'class'                  => 'form-control', 
                            'placeholder'            => 'Title',
                            'data-parsley-minlength' => '1',
                            'data-parsley-maxlength' => '100',
                            'required'               => 'required',
                            'value'                  => $detail->title
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
                            'id'          => 'target-to-copy-detail',
                            'class'       => 'form-control', 
                            'placeholder' => 'URL',
                            'readonly'    => 'readonly',
                            'value'       => $fullImageDetail
                            
                        ]);
                    ?>
                        <div class="input-group-append">
                            <?php
                                echo $this->Form->button('<i class="mdi mdi-content-copy"></i>', [
                                    'id'                    => 'button-clipboard-js-detail',
                                    'class'                 => 'btn btn-sm btn-gradient-primary',
                                    'type'                  => 'button',
                                    'data-clipboard-target' => '#target-to-copy-detail'
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
                            'data-parsley-maxlength' => '200',
                            'value'                  => $detail->description
                        ]);
                    ?>
                </div>
                <div class="form-group purple-media-buttons">
                <?php   
                    echo $this->Form->button('Save', [
                        'id'    => 'button-image-detail-initial',
                        'class' => 'btn btn-gradient-primary'
                    ]);
                
                    echo $this->Form->button('Cancel', [
                        'id'           => 'button-close-modal',
                        'class'        => 'btn btn-outline-primary uk-margin-left uk-modal-close',
                        'type'         => 'button',
                        'data-target'  => '.purple-modal'
                    ]);
                
                    echo $this->Form->button('Delete', [
                        'class'        => 'btn btn-outline-danger uk-margin-left button-delete-media-image',
                        'type'         => 'button',
                        'data-id'      => ''
                    ]);
                
                    echo $this->Form->end();
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        function lightOrDark(color) {
            // Variables for red, green, blue values
            var r, g, b, hsp;
            // Check the format of the color, HEX or RGB?
            if (color.match(/^rgb/)) {
                // If HEX --> store the red, green, blue values in separate variables
                color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
                
                r = color[1];
                g = color[2];
                b = color[3];
            } 
            else {
                // If RGB --> Convert it to HEX: http://gist.github.com/983661
                color = +("0x" + color.slice(1).replace( 
                color.length < 5 && /./g, '$&$&'));
        
                r = color >> 16;
                g = color >> 8 & 255;
                b = color & 255;
            }
            
            // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
            hsp = Math.sqrt(
            0.299 * (r * r) +
            0.587 * (g * g) +
            0.114 * (b * b)
            );
        
            // Using the HSP value, determine whether the color is light or dark
            if (hsp>127.5) {
                return 'light';
            } 
            else {
                return 'dark';
            }
        }

        var modal = $('#modal-full-content-initial');

        UIkit.modal('#modal-full-content-initial').show();

        var image    = '<?= $fullImageDetail ?>',
            token    = $('#csrf-ajax-token').val(),
            colorUrl = '<?= $colorsUrl ?>';
        $.ajax({
            type: "POST",
            url:  colorUrl,
            headers : {
                'X-CSRF-Token': token
            },
            data: { image:image },
            cache: false,
            success: function(data){
                // Image colors
                var colorsArray = data.split(","),
                    i, setColors = '';
                for (i = 0; i < colorsArray.length; i++) {
                    setColors += '<a href="#" class="uk-margin-small-right" style="color: ' + colorsArray[i] + '" title="' + colorsArray[i] + '"><i class="fa fa-square"></i></a>';
                }

                setTimeout(() => {
                    modal.find(".bind-colors").html(setColors);

                    if (colorsArray.length <= 3) {
                        if (lightOrDark(colorsArray[0]) == 'light') {
                            modal.find(".bind-background").css('background-color', '#0e0e0e');
                        }
                        else {
                            modal.find(".bind-background").css('background-color', '#ffffff');
                        }
                    }
                    else {
                        modal.find(".bind-background").css('background-color', colorsArray[0]);
                    }
                }, 1000);
            }
        })

        var clipboard   = new ClipboardJS('#button-clipboard-js-detail'),
            targetLabel = modal.find("form label[for=path]").html();

        clipboard.on('success', function(e) {
            console.info('Action:', e.action);
            console.info('Text:', e.text);
            console.info('Trigger:', e.trigger);
            modal.find("form label[for=path]").html('URL <span class="text-primary">Copied</span>');
            setTimeout(function() {
                modal.find("form label[for=path]").html('URL');
            }, 2500);
            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);
            modal.find("form label[for=path]").html('URL <span class="text-danger">Error. Text is not copied</span>');
            setTimeout(function() {
                modal.find("form label[for=path]").html('URL');
            }, 2500);
        });


        modal.find("form .button-delete-media-image").on("click", function() {
            UIkit.modal('#modal-full-content').hide();
            setTimeout(function() {
                var deleteModal = $("#modal-delete-media"),
                    deleteForm  = deleteModal.find("form"),
                    deleteID    = deleteForm.find("input[name=id]"),
                    deleteTitle = deleteForm.find(".bind-title");

                deleteID.val('<?= $detail->id ?>');
                deleteTitle.html('<?= $detail->title ?>');

                UIkit.modal('#modal-delete-media').show();
            }, 500);
            return false;
        });

        $('#modal-full-content-initial').find('.bind-background').on({
            mouseenter: function () {
                var modal   = $('#modal-full-content-initial');
                var prevUrl = modal.find('#media-image-previous-url').attr('href');
                var nextUrl = modal.find('#media-image-next-url').attr('href');
                if (prevUrl != '#') {
                    modal.find('#media-image-previous-url').show(500);
                }
                if (nextUrl != '#') {
                    modal.find('#media-image-next-url').show(500);
                }
            },
            mouseleave: function () {
                var modal = $('#modal-full-content-initial');
                modal.find('#media-image-previous-url').hide(500);
                modal.find('#media-image-next-url').hide(500);
            }
        });

        var mediaUpdateDetail = {
            form            : 'form-image-detail-initial', 
            button          : 'button-image-detail-initial',
            action          : 'edit', 
            redirectType    : 'redirect', 
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>', 
            btnNormal       : false, 
            btnLoading      : false 
        };
        
        var targetButton = $("#"+mediaUpdateDetail.button);
        targetButton.one('click',function() {
            ajaxSubmit(mediaUpdateDetail.form, mediaUpdateDetail.action, mediaUpdateDetail.redirectType, mediaUpdateDetail.redirect, mediaUpdateDetail.btnNormal, mediaUpdateDetail.btnLoading);
        });
    })
</script>
<?php
        endif;
    endif;
?>

<div id="modal-delete-media" class="uk-flex-top purple-modal" uk-modal="bg-close: false">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($mediaImageDelete, [
                'id'                    => 'form-delete-image', 
                'class'                 => 'pt-3', 
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-delete-images']
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
                    'id'    => 'button-delete-image',
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
            if ($mediaImageTotal > $mediaImageLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>

        $('#drag-and-drop-zone').dmUploader({
            url: '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxUploadImages"]); ?>',
            maxFileSize: 3000000,
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

                $.danidemo.updateFileStatus(id, 'default', '<i class="fa fa-circle-o-notch fa-spin"></i> Uploading...');
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
                
                $('#demo-file' + id).find('.progress-bar').removeClass('progress-bar-animated').addClass('bg-success');

                $.danidemo.addLog('#demo-debug', 'success', console_response);

                $.danidemo.addLog('#demo-debug', 'info', console_response2);

                $.danidemo.updateFileStatus(id, 'success', 'Upload Complete');

                $.danidemo.updateFileProgress(id, '100%');

                console.log(console_response);
                // console.log(console_response2);

                $('#bind-media-load').prepend('<div class="col-6 col-md-2 grid-margin"><div><div class="uk-card"><div class="uk-card-media-top uk-inline"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAQAAADTdEb+AAACHElEQVR42u3SMQ0AAAzDsJU/6aGo+tgQouSgIBJgLIyFscBYGAtjgbEwFsYCY2EsjAXGwlgYC4yFsTAWGAtjYSwwFsbCWGAsjIWxwFgYC2OBsTAWxgJjYSyMBcbCWBgLjIWxMBYYC2NhLDAWxsJYYCyMhbHAWBgLY4GxMBbGAmNhLIwFxsJYGAuMhbEwFhgLY2EsMBbGwlhgLIyFscBYGAtjgbEwFsYCY2EsjAXGwlgYC2OBsTAWxgJjYSyMBcbCWBgLjIWxMBYYC2NhLDAWxsJYYCyMhbHAWBgLY4GxMBbGAmNhLIwFxsJYGAuMhbEwFhgLY2EsMBbGwlhgLIyFscBYGAtjgbEwFsYCY2EsjAXGwlgYC4yFsTAWGAtjYSwwFsbCWGAsjIWxwFgYC2OBsTAWxgJjYSyMBcbCWBgLjIWxMBbGAmNhLIwFxsJYGAuMhbEwFhgLY2EsMBbGwlhgLIyFscBYGAtjgbEwFsYCY2EsjAXGwlgYC4yFsTAWGAtjYSwwFsbCWGAsjIWxwFgYC2OBsTAWxgJjYSyMBcbCWBgLjIWxMBYYC2NhLDAWxsJYYCyMhbHAWBgLY4GxMBbGAmNhLIwFxsJYGAuMhbEwFhgLY2EsjCUBxsJYGAuMhbEwFhgLY2EsMBbGwlhgLIyFscBYGAtjgbEwFsYCY2EsjAXGwlgYC4yFsTAWGAtjYSwwFsbCWGAsjIWxwFjsPeVaAS0/Qs6MAAAAAElFTkSuQmCC"><div class="uk-position-center"><div uk-spinner></div></div></div></div></div></div>');

                var createToast = notifToast('Preparing File', 'Upload Complete. Loading image list...', 'success', true);

                setTimeout(function() {
                    $.ajax({
                        type: 'GET',
                        url: '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxImagesDetail"]); ?>',
                        success: function(data) {
                            $("#uk-filtering").html(data);
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
            
        var mediaUpdate = {
            form            : 'form-image-detail', 
            button          : 'button-image-detail',
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
            form            : 'form-delete-image', 
            button          : 'button-delete-image',
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
