<?php
    $manageCategoriesUrl = $this->Url->build([
        '_name' => 'adminBlogCategories'
    ]);

    $newPostUrl = $this->Url->build([
		'_name'  => 'adminBlogsAction',
		'action' => 'add'
    ]);

    $deleteRedirect = $this->Url->build([
        '_name' => 'adminBlogs'
    ]);
?>

<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div id="page-detail-card" class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Posts</h4>
            </div>
            <div class="card-toolbar">
            	<button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text" onclick="location.href='<?= $newPostUrl ?>'">
                <i class="mdi mdi-pencil btn-icon-prepend"></i>
                	New Post
                </button>
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple uk-margin-small-left" onclick="location.href='<?= $manageCategoriesUrl ?>'">
                	<i class="mdi mdi-folder-multiple-outline btn-icon-prepend"></i>
                  		Manage Categories
                </button>
                <div class="uk-inline uk-align-right" style="margin-bottom: 0">
                    <button type="button" class="btn btn-gradient-success btn-toolbar-card btn-sm btn-icon-text button-add-purple uk-margin-small-left">
                        <i class="mdi mdi-filter btn-icon-prepend"></i>
                            All Categories
                    </button>
                    <div uk-dropdown="pos: bottom-right; mode: click">
                        <ul class="uk-nav uk-dropdown-nav text-right">
                            <li class="uk-active"><a href="<?= $this->Url->build(['_name' => 'adminBlogs']) ?>">All Categories</a></li>
                            <?php
                                if ($blogCategories->count() > 0):
                            ?>
                                <li class="uk-nav-divider"></li>
                                <?php
                                    foreach ($blogCategories as $blogCategory):
                                        $filterPostUrl = $this->Url->build([
                                            '_name'    => 'adminBlogsFilterCategory',
                                            'category' => $blogCategory->slug,
                                        ]);
                                ?>
                                <li><a href="<?= $filterPostUrl ?>"><?= $blogCategory->name ?></a></li>
                            <?php
                                    endforeach;
                                endif;
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body <?php if ($blogs->count() == 0) echo 'uk-padding-remove'; ?>">
            	<?php
	            	if ($blogs->count() > 0):
	            ?>
                <div class="uk-overflow-auto">
    	            <table class="uk-table uk-table-justify uk-table-divider uk-table-middle purple-datatable">
    				    <thead>
    				        <?php
    				            echo $this->Html->tableHeaders([
    				                ['No' => ['width' => '30']],
    				                'Title',
    				                'Category',
    				                'Post Date',
                                    'Modified Date',
    				                'Status',
    				                'Posted By',
    				                ['Action' => ['class' => 'uk-width-small text-center']]
    				            ]);
    				        ?>
    				    </thead>
    				    <tbody> 
    				        <?php 
    				            $i = 1;
    				            foreach ($blogs as $blog): 
    				                $editPostUrl = $this->Url->build([
                                        '_name'  => 'adminBlogsEdit',
                                        'blogid' => $blog->id,
    				                ]);

                                    $postCommentsUrl = $this->Url->build([
                                        '_name'  => 'adminComments',
                                        'blogid' => $blog->id,
                                    ]);

                                    $totalUnreadComments = $this->cell('Comments::totalComment', [$blog->id, 'unread']);
                                    $totalAllComments    = $this->cell('Comments::totalComment', [$blog->id, 'all']);
    				        ?>
    				        <tr>
    				            <td><?= $i ?></td>
    				            <td>
    				                <?= $this->Text->truncate(
    				                        $blog->title,
    				                        30,
    				                        [
    				                            'ellipsis' => '...',
    				                            'exact' => false
    				                        ]
    				                ); ?>
    				            </td>
    				            <td><?= $blog->blog_category->name ?></td>
    				            <td><?= date($settingsDateFormat.' '.$settingsTimeFormat, strtotime($blog->created)) ?></td>
    				            <td>
    				                <?php
    				                    if ($blog->modified == NULL) {
    				                        echo '-';
    				                    }
    				                    else { 
    				                        echo date($settingsDateFormat.' '.$settingsTimeFormat, strtotime($blog->modified));
    				                    }
    				                ?>
    				            </td>
                                <td><?= $blog->text_status ?></td>
    				            <td><?= $blog->admin->display_name ?></td>
    				            <td class="text-center">
    				                <button type="button" class="btn btn-gradient-primary btn-rounded btn-icon" uk-tooltip="Edit Post" onclick="location.href='<?= $editPostUrl; ?>'">
    				                    <i class="mdi mdi-pencil"></i>
    				                </button>
    				                <button type="button" class="btn btn-gradient-success btn-rounded btn-icon btn-with-badge" uk-tooltip="<?php if ($totalAllComments != '0') echo 'View Comments'; else echo 'No comment for this post'; ?>" onclick="location.href='<?= $postCommentsUrl; ?>'" <?php if ($totalAllComments == '0') echo 'disabled' ?>>
                                        <i class="mdi mdi-comment-multiple-outline"></i>

                                        <?php if ($totalUnreadComments != '0'): ?><span class="uk-badge bg-danger notification-badge"><?= $totalUnreadComments ?></span><?php endif; ?>
                                    </button>
                                    <?php
                                        if ($adminLevel == 1):
                                    ?>
    				                <button type="button" class="btn btn-gradient-danger btn-rounded btn-icon button-delete-purple" uk-tooltip="Delete Post" data-purple-id="<?= $blog->id ?>" data-purple-name="<?= $blog->title ?>" data-purple-modal="#modal-delete-post">
    				                    <i class="mdi mdi-delete"></i>
    				                </button>
                                    <?php endif; ?>
    				            </td>
    				        </tr>
    				        <?php 
    				                $i++;
    				            endforeach; 
    				        ?>
    				    </tbody>
    				</table>
                </div>
	            <?php
				    else:
				?>  
				<div class="uk-alert-danger <?php if ($blogs->count() == 0) echo 'uk-margin-remove-bottom'; ?>" uk-alert>
				    <p>Can't find post for this page. You can add a new post <a href="<?= $newPostUrl ?>" class="">here</a>.</p>
				</div>
				<?php
				    endif;
				?>
        	</div>
    	</div>
	</div>
</div>

<?php
	if ($blogs->count() > 0):
?>
<div id="modal-delete-post" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($blogDelete, [
                'id'                    => 'form-delete-post',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['controller' => 'Blogs', 'action' => 'ajax-delete']
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
                    'id'    => 'button-delete-post',
                    'class' => 'btn btn-gradient-danger uk-margin-left'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>
<?php
    endif;
?>

<script type="text/javascript">
    $(document).ready(function() {
    	<?php
            if ($blogs->count() > 0):
        ?>
        var dataTable = $('.purple-datatable').DataTable({
            responsive: true,
            "columnDefs": [{
                "targets": -1,
                "orderable": false
            }]
        });

        dataTable.on( 'responsive-display', function ( e, datatable, row, showHide, update ) {
            $(".button-delete-purple").on("click", function() {
                var btn         = $(this),
                    id          = btn.data('purple-id'),
                    title       = btn.data('purple-name'),
                    modal       = btn.data('purple-modal'),

                    deleteModal = $(modal),
                    deleteForm  = deleteModal.find("form"),
                    deleteID    = deleteForm.find("input[name=id]"),
                    deleteTitle = deleteForm.find(".bind-title");

                deleteID.val(id);
                deleteTitle.html(title);

                UIkit.modal(modal).show();
                return false;
            })
        });

        var postDelete = {
            form            : 'form-delete-post',
            button          : 'button-delete-post',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $deleteRedirect ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+postDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(postDelete.form, postDelete.action, postDelete.redirectType, postDelete.redirect, postDelete.btnNormal, postDelete.btnLoading);
        })
        <?php endif; ?>
    })
</script>