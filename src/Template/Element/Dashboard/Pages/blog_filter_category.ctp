<?php
    $manageCategoriesUrl = $this->Url->build([
        '_name' => 'adminPagesBlogCategories',
        'id'    => $this->request->getParam('id'),
        'slug'  => $this->request->getParam('slug'),
    ]);

    $newPostUrl = $this->Url->build([
        '_name' => 'adminPagesBlogsAdd',
        'id'    => $this->request->getParam('id'),
        'slug'  => $this->request->getParam('slug'),
    ]);

    $deleteRedirect = $this->Url->build([
        '_name' => 'adminPagesDetail',
        'type'  => 'blog',
        'id'    => $this->request->getParam('id'),
        'slug'  => $this->request->getParam('slug')
    ]);

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
                        '_name'  => 'adminPagesBlogsEdit',
                        'id'     => $this->request->getParam('id'),
                        'slug'   => $this->request->getParam('slug'),
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
    else:
?>  
<div class="uk-alert-danger" uk-alert>
    <p>Can't find post for this page. You can add a new post <a href="<?= $newPostUrl ?>" class="">here</a>.</p>
</div>
<?php
    endif;
?>

<div id="modal-edit-page-blog" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($pageBlogEdit, [
                'id'                    => 'form-edit-page',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-save-blog-page']
            ]);

            echo $this->Form->hidden('id', ['value' => $pages->id]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Edit Page</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->text('title', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Page Title (Max 100 charecters)',
                        'data-parsley-maxlength' => '100',
                        'autofocus'              => 'autofocus',
                        'required'               => 'required',
                        'value'                  => $pages->title
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-edit-page',
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
    </div>
    <?php
        echo $this->Form->end();
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var btnEditPage = '<div class="card-toolbar"><button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-edit-page-blog" data-purple-modal="#modal-edit-page-blog">' +
                '<i class="mdi mdi-pencil-box-outline btn-icon-prepend"></i>' +
                  'Edit Page' +
                '</button>',
            btnNew    = '<button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left" onclick="location.href=\'<?= $newPostUrl ?>\'">' +
                '<i class="mdi mdi-pencil btn-icon-prepend"></i>' +
                  'New Post' +
                '</button>',
            btnManage = '<button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left" onclick="location.href=\'<?= $manageCategoriesUrl ?>\'">' +
                '<i class="mdi mdi-folder-multiple-outline btn-icon-prepend"></i>' +
                  'Manage Categories' +
                '</button>',
            btnFilterCategory = '<div class="uk-inline uk-align-right" style="margin-bottom: 0">' +
                '<button type="button" class="btn btn-gradient-success btn-toolbar-card btn-sm btn-icon-text button-add-purple uk-margin-small-left">' +
                    '<i class="mdi mdi-filter btn-icon-prepend"></i>' +
                    '<?= $selectedBlogCategories->name ?>' +
                '</button>' +
                '<div uk-dropdown="pos: bottom-right">' +
                    '<ul class="uk-nav uk-dropdown-nav text-right">' +
                        '<li><a href="<?= $this->Url->build(['_name' => 'adminBlogs']) ?>">All Categories</a></li>' +
                        <?php
                                if ($blogCategories->count() > 0):
                            ?>
                                '<li class="uk-nav-divider"></li>' +
                                <?php
                                    foreach ($blogCategories as $blogCategory):
                                        $filterPostUrl = $this->Url->build([
                                            '_name'    => 'adminPagesBlogFilterCategory',
                                            'type'     => 'blog',
                                            'id'       => $this->request->getParam('id'),
                                            'slug'     => $this->request->getParam('slug'),
                                            'category' => $blogCategory->slug,
                                        ]);
                                ?>
                                '<li class="<?php if ($this->request->getParam('category') == $blogCategory->slug) echo 'uk-active' ?>"><a href="<?= $filterPostUrl ?>"><?= $blogCategory->name ?></a></li>' +
                            <?php
                                    endforeach;
                                endif;
                            ?>
                        '</ul>' +
                    '</div>' +
                '</div></div>';

        $('#page-detail-card').find('.card-header').after(btnEditPage + btnNew + btnManage + btnFilterCategory);

        $(".button-edit-page-blog").on("click", function() {
            var btn = $(this),
                modal = btn.data('purple-modal');

            UIkit.modal(modal).show();
            return false;
        })

        var pageSave = {
            form            : 'form-edit-page',
            button          : 'button-edit-page',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'index']); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+pageSave.button);
        targetButton.one('click',function() {
            ajaxSubmit(pageSave.form, pageSave.action, pageSave.redirectType, pageSave.redirect, pageSave.btnNormal, pageSave.btnLoading);
        })
        
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