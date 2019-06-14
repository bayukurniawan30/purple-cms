<?php 
    $currentUrl = $this->request->getAttribute('params');
    
    if ($currentUrl['_name'] == 'adminPagesBlogsAdd' || $currentUrl['_name'] == 'adminPagesBlogsEdit') {
        $submitRedirect = $this->Url->build([
            '_name' => 'adminPagesDetail',
            'type'  => 'blog',
            'id'    => $this->request->getParam('id'),
            'slug'  => $this->request->getParam('slug')
        ]);
    }
    else {
        $submitRedirect = $this->Url->build([
            '_name' => 'adminBlogs'
        ]);;
    }
?>

<?php
    echo $this->Form->create($blogAdd, [
        'id'                    => 'form-add-post',
        'class'                 => '',
        'data-parsley-validate' => '',
        'url'                   => ['action' => 'ajax-add']
    ]);

    echo $this->Form->hidden('page_id', ['value' => $this->request->getParam('id')]);
    echo $this->Form->hidden('featured');
?>
<div class="row">
    <div class="col-md-8 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Post Detail</h4>
            </div>
            
            <div class="card-body">
                <div class="form-group">
                    <?php
                        echo $this->Form->text('title', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Post Title',
                            'data-parsley-minlength' => '2',
                            'data-parsley-maxlength' => '255',
                            'uk-tooltip'             => 'title: Required. 2-255 chars.; pos: bottom',
                            'autofocus'              => 'autofocus',
                            'required'               => 'required'
                        ]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->select(
                            'status',
                            [
                                '0' => 'Draft',
                                '1' => 'Publish',
                            ],
                            [
                                'empty'    => 'Select Status',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->select(
                            'comment',
                            [
                                'yes' => 'Allow',
                                'no'  => 'Close Comment',
                            ],
                            [
                                'empty'    => 'Allow Comment?',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->select(
                            'social_share',
                            [
                                'enable'  => 'Enable',
                                'disable' => 'Disable',
                            ],
                            [
                                'empty'    => 'Social Sharing Buttons',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->textarea('content',[
                            'class'       => 'form-control',
                            'placeholder' => 'Post Content',
                            'required'    => 'required'
                        ]);
                    ?>
                </div>   
                <div class="form-group">
                    <?php
                        echo $this->Form->text('tags', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Post Tags',
                            'uk-tooltip'             => 'title: Optional. Max 5 tags.; pos: bottom'
                        ]);
                    ?>
                </div>    
            </div>
            <div class="card-footer">
                <?php
                    echo $this->Form->button('Save', [
                        'id'    => 'button-add-post',
                        'class' => 'btn btn-gradient-primary'
                    ]);

                    echo $this->Form->button('Cancel', [
                        'class'   => 'btn btn-outline-primary uk-margin-left',
                        'type'    => 'button',
                        'onclick' => 'location.href = \''.$submitRedirect.'\''
                    ]);
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-4 grid-margin">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <?= $this->element('Dashboard/upload_or_browse_image', [
                        'selected'     => NULL,
                        'widgetTitle'  => 'Post Cover',
                        'inputTarget'  => 'featured',
                        'browseMedias' => $browseMedias,
                        'multiple'     => true,
                        'modalParams'  => [
                            'browseContent' => 'blogs::0',
                            'browseAction'  => 'send-to-input',
                            'browseTarget'  => 'featured'
                        ]
                ]) ?>
            </div>

            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title uk-margin-remove-bottom">Category</h4>
                    </div>
                    <div class="card-body">
                        <div id="load-blog-categories">
                            
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="button-new-post-category" type="button" class="btn btn-gradient-primary btn-sm" data-purple-modal="#modal-add-post-category">New Category</button>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title uk-margin-remove-bottom">SEO (Search Engine Optimization)</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <!-- <label>Meta Keywords</label> -->
                            <?php
                                echo $this->Form->text('meta_keywords', [
                                    'id'                     => 'form-input-metakeywords',
                                    'class'                  => 'form-control',
                                    'placeholder'            => 'Meta Keywords (Best practice is 10 keyword phrases)',
                                    'data-parsley-maxlength' => '100',
                                    'uk-tooltip'             => 'title: Optional. max 100 chars.; pos: bottom',
                                ]);
                            ?>
                        </div>
                        <div class="form-group">
                            <!-- <label>Meta Description</label> -->
                            <?php
                                echo $this->Form->textarea('meta_description',[
                                    'id'                     => 'form-input-metadescription',
                                    'class'                  => 'form-control',
                                    'placeholder'            => 'Meta Description (Max 150 chars)',
                                    'data-parsley-maxlength' => '150',
                                    'rows'                   => '4',
                                    'uk-tooltip'             => 'title: Optional. max 150 chars.; pos: bottom',
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
<?php
    echo $this->Form->end();
?>

<?= $this->element('Dashboard/Modal/add_blog_category_modal', [
        'blogCategoryAdd' => $blogCategoryAdd,
        'pageId'          => $this->request->getParam('id'),
        'afterSubmit'     => 'load',
        'loadTarget'      => '#load-blog-categories',
        'loadUrl'         => $this->Url->build(["controller" => 'BlogCategories', "action" => "ajaxLoadSelectbox"])
]) ?>

<!-- Include PDF export JS lib. -->
<?= $this->Html->script('/master-assets/plugins/html2pdf/html2pdf.bundle.js'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('input[name="tags"]').tagEditor({ 
            // initialTags: [],
            autocomplete: {
                delay: 0, 
                position: { collision: 'flip' },
                source: [
                    <?php  
                        foreach ($tagsArray as $tags):
                            if (end($tagsArray) == $tags) {
                                echo '"' . $tags . '"';
                            }
                            else {
                                echo '"' . $tags . '", ';
                            }
                        endforeach;
                    ?>
                ]
            },
            maxTags: 5,
            placeholder: "Tags (Max 5 tags)",
            
        });
        
        var token                  = $("input[name=_csrfToken]").val(),
            froalaManagerLoadUrl   = $("#froala-load-url").val(),
            froalaImageUploadUrl   = $("#froala-image-upload-url").val(),
            froalaFileUploadUrl    = $("#froala-file-upload-url").val(),
            froalaVideoUploadUrl   = $("#froala-video-upload-url").val();

        $('textarea[name=content]').froalaEditor({
            theme: 'royal',
            height: 400,
            toolbarStickyOffset: 70,
            charCounterCount: false,
            placeholderText: 'Post content here...',
            enter: $.FroalaEditor.ENTER_DIV,
            imageManagerLoadURL: froalaManagerLoadUrl,
            imageUploadURL: froalaImageUploadUrl,
            fileUploadURL: froalaFileUploadUrl,
            videoUploadURL: froalaVideoUploadUrl,
            imageMaxSize: 3 * 1024 * 1024,
            imageAllowedTypes: ['jpeg', 'jpg', 'png'],
            fileMaxSize: 5 * 1024 * 1024,
            fileAllowedTypes: ['*'],
            videoMaxSize: 20 * 1024 * 1024,
            videoAllowedTypes: ['mp4', 'm4v', 'ogg', 'webm'],
            requestHeaders: {
                'X-CSRF-Token': token
            }
        })

        var url = '<?= $this->Url->build(["controller" => 'BlogCategories', "action" => "ajaxLoadSelectbox"]); ?>';

        $.ajax({
            type: "POST",
            url:  url,
            headers : {
                'X-CSRF-Token': token
            },
            data: {page: '<?php if ($this->request->getParam('id') == NULL) echo 'NULL'; else echo $this->request->getParam('id') ?>'},
            cache: false,
            beforeSend: function() {
            },
            success: function(data) {
                $('#load-blog-categories').html(data);
            }
        })

        var categoryAdd = {
            form            : 'form-add-post-category',
            button          : 'button-add-post-category',
            action          : 'ajax-load-post-categories',
            redirectType    : 'ajax',
            redirect        : '#load-blog-categories',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+categoryAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(categoryAdd.form, categoryAdd.action, categoryAdd.redirectType, categoryAdd.redirect, categoryAdd.btnNormal, categoryAdd.btnLoading);
        })

        var blogAdd = {
            form            : 'form-add-post',
            button          : 'button-add-post',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect; ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+blogAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(blogAdd.form, blogAdd.action, blogAdd.redirectType, blogAdd.redirect, blogAdd.btnNormal, blogAdd.btnLoading);
        })
    })
</script>