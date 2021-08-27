<?php
    if ($this->request->getParam('id') == NULL) {
    	$submitRedirect = $this->Url->build([
            '_name' => 'adminBlogCategories'
        ]);
    }
    else {
        $submitRedirect = $this->Url->build([
            '_name' => 'adminPagesBlogCategories',
            'id'    => $this->request->getParam('id'),
            'slug'  => $this->request->getParam('slug'),
        ]);
    }
?>

<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Post Categories</h4> 
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple" data-purple-modal="#modal-add-post-category">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Add Category
                </button>
            </div>
            <div class="card-body uk-padding-remove">
                <?php
                    if ($blogCategories->count() > 0):
                ?>
                <ul id="sortable-items" class="" uk-sortable="handle: .uk-sortable-handle" data-purple-url="<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxReorder"]); ?>" uk-grid>
                    <?php
						$order      = 1;
                        foreach ($blogCategories as $blogCategory):
                            $permalink = $this->Url->build([
								'_name'    => 'postsInCategory',
								'category' => $blogCategory->slug,
						    ], true);
							$totalBlogs = 0;
						    foreach ($blogs as $blog):
						    	if ($blog->blog_category_id === $blogCategory->id) {
						    		$totalBlogs++;
						    	}
						    endforeach;
                    ?>
                    <li id="sortable-<?= $blogCategory->id; ?>" class="uk-width-1-1 uk-margin-remove-top" data-order="<?= $order ?>" style="position: relative">
                        <div class="sortable-remover" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 5; display: none; background: rgba(255,255,255,.4)"></div>
                        <div class="uk-card uk-card-default uk-card-small uk-card-body">
                            <span class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: menu"></span><?= $blogCategory->name ?>

                            <div class="uk-inline uk-align-right">
                                <a href="#" class="uk-margin-small-right" uk-icon="icon: file-edit" uk-tooltip="Total <?php if ($totalBlogs == 0 || $totalBlogs == 1) echo $totalBlogs.' post'; else echo $totalBlogs.' posts' ?> inside <?= $blogCategory->name ?>"></a>
                                <a href="#" class="uk-margin-small-right" uk-icon="icon: user" uk-tooltip="<?= $blogCategory->admin->display_name ?>"></a>
                                <button class="uk-button uk-button-link"><span uk-icon="more-vertical"></span></button>
                                <div uk-dropdown="mode: click; pos: bottom-right">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <li><a href="<?= $permalink ?>" target="_blank">Open</a></li>
                                        <li><a class="button-edit-category" href="#" data-purple-id="<?= $blogCategory->id ?>" data-purple-name="<?= $blogCategory->name ?>" data-purple-page="<?= $this->request->getParam('id') ?>" data-purple-modal="#modal-edit-post-category">Edit</a></li>
                                        <li><a class="button-get-permalink" href="#" data-purple-link="<?= $permalink ?>" data-purple-modal="#modal-show-permalink">Get Permalink</a></li>
                                        <li class="uk-nav-divider"></li>
                                        <?php
                                            if ($totalBlogs > 0):
                                        ?>
                                        <li><a class="button-disallowed-delete" href="#" data-purple-modal="#modal-disallowed-delete">Delete</a></li>
                                        <?php
                                            else:
                                        ?>
                                        <li><a class="button-delete-purple text-danger" href="#" data-purple-id="<?= $blogCategory->id ?>" data-purple-name="<?= $blogCategory->name ?>" data-purple-modal="#modal-delete-post-category">Delete</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php
                        $order++;
                    endforeach;
                ?>
                </ul>
                <?php
                    else:
                ?>
                <div class="uk-alert-danger <?php if ($blogCategories->count() == 0) echo 'uk-margin-remove-bottom'; ?>" uk-alert>
                    <p>Can't find post category. You can add a new category <a href="#" class="button-add-purple" data-purple-modal="#modal-add-post-category">here</a>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->element('Dashboard/Modal/BlogCategories/add_modal', [
        'blogCategoryAdd' => $blogCategoryAdd,
        'pageId'          => $this->request->getParam('id'),
        'afterSubmit'     => 'redirect',
        'loadTarget'      => NULL,
        'loadUrl'         => NULL
]) ?>

<?php
    if ($blogCategories->count() > 0) {
        echo $this->element('Dashboard/Modal/BlogCategories/edit_modal', [
            'blogCategoryEdit' => $blogCategoryEdit,
            'pageId'           => NULL
        ]);
        echo $this->element('Dashboard/Modal/delete_modal', [
            'action'     => 'post-category',
            'form'       => $blogCategoryDelete,
            'formAction' => 'ajax-delete'
        ]);
        echo $this->element('Dashboard/Modal/permalink_modal');
    }
?>

<script type="text/javascript">
    $(document).ready(function() {
		var categoryAdd = {
            form            : 'form-add-post-category',
            button          : 'button-add-post-category',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+categoryAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(categoryAdd.form, categoryAdd.action, categoryAdd.redirectType, categoryAdd.redirect, categoryAdd.btnNormal, categoryAdd.btnLoading);
        })

        <?php
		    if ($blogCategories->count() > 0):
		?>
		var categoryEdit = {
            form            : 'form-edit-post-category',
            button          : 'button-edit-post-category',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+categoryEdit.button);
        targetButton.one('click',function() {
            ajaxSubmit(categoryEdit.form, categoryEdit.action, categoryEdit.redirectType, categoryEdit.redirect, categoryEdit.btnNormal, categoryEdit.btnLoading);
        })

        var categoryDelete = {
            form            : 'form-delete-post-category',
            button          : 'button-delete-post-category',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+categoryDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(categoryDelete.form, categoryDelete.action, categoryDelete.redirectType, categoryDelete.redirect, categoryDelete.btnNormal, categoryDelete.btnLoading);
        })

    	UIkit.util.on('#sortable-items', 'stop', function () {
            var h = [];
            $("#sortable-items>li").each(function() {
                h.push($(this).attr('id').substr(9));
            });
            var data  = {order: h + ""},
                url   = $("#sortable-items").data('purple-url');
                token = $('#csrf-ajax-token').val();

            var ajaxProcessing = $.ajax({
                type: "POST",
                url:  url,
                headers : {
                    'X-CSRF-Token': token
                },
                data: data,
                cache: false,
                beforeSend: function() {
                    $('input, button, textarea, select').prop("disabled", true);
                    $("#sortable-items>li .uk-sortable-handle").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
                    $("#sortable-items>li .sortable-remover").show();
                }
            });
            ajaxProcessing.done(function(msg) {
                if (cakeDebug == 'on') {
                    console.log(msg);
                }

                var json    = $.parseJSON(msg),
                    status  = (json.status);

                if (status == 'ok') {
                    $("#sortable-items>li .sortable-remover").hide();
                    $("#sortable-items>li .uk-sortable-handle").html('<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"> <rect x="2" y="4" width="16" height="1"></rect> <rect x="2" y="9" width="16" height="1"></rect> <rect x="2" y="14" width="16" height="1"></rect></svg>');
                    var createToast = notifToast('Reordering Categories', 'Success reordering categories', 'success', true);
                }
                else {
                    var createToast = notifToast('Reordering Categories', 'There is an error with Purple. Please try again', 'error', true);
                }
            });
            ajaxProcessing.fail(function(jqXHR, textStatus) {
                var createToast = notifToast(jqXHR.statusText, 'There is an error with Purple. Please try again', 'error', true);
            });
            ajaxProcessing.always(function () {
                $('input, button, textarea, select').prop("disabled", false);
            });
        });
		<?php endif; ?>
    })
</script>