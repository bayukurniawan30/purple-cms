<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Users</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple" onclick="location.href='<?= $this->Url->build(['_name' => 'adminUsersAction', 'action' => 'add']); ?>'">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Add User 
                </button>
            </div>
            <div class="card-body">
                <div uk-grid>
                    <div class="uk-width-1-1">
                        
                    </div>
                </div>

                <?php
                    if ($users->count() > 0):
                ?>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-divider uk-table-middle purple-datatable">
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Email
                                </th>
                                <th>
                                    Registered
                                </th>
                                <th>
                                    Level
                                </th>
                                <th class="text-center">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <?= $user->display_name ?>
                                </td>
                                <td>
                                    <a href="mailto:<?= $user->email ?>" uk-tooltip="Send email to <?= $user->email ?>"><?= $user->email ?></a>
                                </td>    
                                <td>
                                    <?= date($settingsDateFormat.' '.$settingsTimeFormat, strtotime($user->created)) ?>
                                </td>
                                <td>
                                    <?php
                                        if ($user->level == 1) {
                                            echo 'Administrator';
                                        }
                                        elseif ($user->level == 2) {
                                            echo 'Editor';
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-gradient-warning btn-rounded btn-icon" uk-tooltip="Change <?php if ($sessionID == $user->id) echo 'your password'; else echo $user->display_name.' password' ?>" onclick="location.href='<?= $this->Url->build(['_name' => 'adminUsersChangePassword', 'id' => $user->id]); ?>'">
                                        <i class="mdi mdi-key-variant"></i>
                                    </button>
                                    <button type="button" class="btn btn-gradient-primary btn-rounded btn-icon" uk-tooltip="Edit <?php if ($sessionID == $user->id) echo 'your profile'; else echo $user->display_name ?>" onclick="location.href='<?= $this->Url->build(['_name' => 'adminUsersEdit', 'id' => $user->id]); ?>'">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <?php
                                        if ($sessionID != $user->id):
                                    ?>
                                    <button type="button" class="btn btn-gradient-danger btn-rounded btn-icon button-delete-purple" uk-tooltip="Delete <?= $user->display_name ?>" data-purple-id="<?= $user->id ?>" data-purple-name="<?= $user->display_name ?>" data-purple-modal="#modal-delete-user">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    else:
                ?>
                <div class="uk-alert-danger" uk-alert>
                    <p>Can't find user for your website. You can add a new user <a href="<?= $this->Url->build(['_name' => 'adminUsersAction', 'action' => 'add']); ?>" class="">here</a>.</p>
                </div>
                <?php
                    endif;
                ?>
            </div>
        </div>
    </div>
</div>

<?php
    if ($users->count() > 0):
?>
<div id="modal-delete-user" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($adminDelete, [
                'id'                    => 'form-delete-user',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-delete']
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
                    'id'    => 'button-delete-user',
                    'class' => 'btn btn-gradient-danger uk-margin-left'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        var dataTable = $('.purple-datatable').DataTable({
            responsive: true,
            columnDefs: [
                { 
                    orderable: false, 
                    targets: -1 
                }
            ],
            "bLengthChange": false
        });

        dataTable.on( 'responsive-display', function ( e, datatable, row, showHide, update ) {
            $(".button-delete-purple").click(function() {
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

        var userDelete = {
            form            : 'form-delete-user',
            button          : 'button-delete-user',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+userDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(userDelete.form, userDelete.action, userDelete.redirectType, userDelete.redirect, userDelete.btnNormal, userDelete.btnLoading);
        })
    })
</script>
<?php
    endif;
?>