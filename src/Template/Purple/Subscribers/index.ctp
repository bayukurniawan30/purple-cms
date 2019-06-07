<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Subscribers</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple" data-purple-modal="#modal-add-subscriber">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Add Subscriber
                </button>
                <?php
                    if ($subscribers->count() > 0):
                ?>
                <button type="button" class="btn btn-gradient-success btn-toolbar-card btn-sm btn-icon-text button-export-subscribers uk-margin-small-left" data-purple-url="<?= $this->Url->build(['_name' => 'adminSubscribersAction', 'action' => 'ajaxExport']) ?>" data-purple-redirect="<?= $this->Url->build(['_name' => 'adminSubscribersAction', 'action' => 'download']) ?>">
                    <i class="mdi mdi-file-document btn-icon-prepend"></i>
                    Export Subscriber
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php
                    if ($subscribers->count() > 0):
                ?>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-divider uk-table-middle purple-datatable">
                        <thead>
                            <?php
                                echo $this->Html->tableHeaders([
                                    ['No' => ['width' => '30']],
                                    'Email',
                                    'Created',
                                    'Status',
                                    ['Action' => ['class' => 'uk-width-small text-center']]
                                ]);
                            ?>
                        </thead>
                        <tbody> 
                            <?php 
                                $i = 1;
                                foreach ($subscribers as $subscriber): 
                            ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><?= $subscriber->email ?></td>
                                <td><?= date($settingsDateFormat.' '.$settingsTimeFormat, strtotime($subscriber->created)) ?></td>
                                <td><?= ucwords($subscriber->status) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-gradient-primary btn-rounded btn-icon button-edit-subscriber" uk-tooltip="Edit Subscriber" data-purple-id="<?= $subscriber->id ?>" data-purple-email="<?= $subscriber->email ?>" data-purple-modal="#modal-edit-subscriber">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-gradient-danger btn-rounded btn-icon button-delete-purple" uk-tooltip="Delete Subscriber" data-purple-id="<?= $subscriber->id ?>" data-purple-name="<?= $subscriber->email ?>" data-purple-modal="#modal-delete-subscriber">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
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
                <div class="uk-alert-danger" uk-alert>
                    <p>Can't find subscriber for your website. You can add a new subscriber <a href="#" class="button-add-purple" data-purple-modal="#modal-add-subscriber">here</a>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="modal-add-subscriber" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($subscriberAdd, [
                'id'                    => 'form-add-subscriber',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-add']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Add Subscriber</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->text('email', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Subscriber Email',
                        'data-parsley-type'      => 'email',
                        'autofocus'              => 'autofocus',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-add-subscriber',
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

<?php
    if ($subscribers->count() > 0):
?>
<div id="modal-edit-subscriber" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($subscriberEdit, [
                'id'                    => 'form-edit-subscriber',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-update']
            ]);

            echo $this->Form->hidden('id');
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Edit Subscriber</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->text('email', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Subscriber Email',
                        'data-parsley-type'      => 'email',
                        'autofocus'              => 'autofocus',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-edit-subscriber',
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
<?php
    endif;
?>

<?= $this->element('Dashboard/Modal/delete_modal', [
        'action'     => 'subscriber',
        'form'       => $subscriberDelete,
        'formAction' => 'ajax-delete'
]) ?>

<script>
    $(document).ready(function() {
        var subscriberAdd = {
            form            : 'form-add-subscriber',
            button          : 'button-add-subscriber',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+subscriberAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(subscriberAdd.form, subscriberAdd.action, subscriberAdd.redirectType, subscriberAdd.redirect, subscriberAdd.btnNormal, subscriberAdd.btnLoading);
        })

        <?php
            if ($subscribers->count() > 0):
        ?>
            var dataTable = $('.purple-datatable').DataTable({
                responsive: true,
                "columnDefs": [{
                    "targets": -1,
                    "orderable": false
                }]
            });

            dataTable.on( 'responsive-display', function ( e, datatable, row, showHide, update ) {
                $(".button-edit-subscriber").click(function() {
                    var btn         = $(this),
                        id          = btn.data('purple-id'),
                        email       = btn.data('purple-email'),
                        modal       = btn.data('purple-modal'),
                        editModal   = $(modal);

                    editModal.find('input[name=id]').val(id);
                    editModal.find('input[name=email]').val(email);

                    UIkit.modal(modal).show();
                    return false;
                })
                
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

            var subscriberEdit = {
                form            : 'form-edit-subscriber',
                button          : 'button-edit-subscriber',
                action          : 'edit',
                redirectType    : 'redirect',
                redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
                btnNormal       : false,
                btnLoading      : false
            };

            var targetButton = $("#"+subscriberEdit.button);
            targetButton.one('click',function() {
                ajaxSubmit(subscriberEdit.form, subscriberEdit.action, subscriberEdit.redirectType, subscriberEdit.redirect, subscriberEdit.btnNormal, subscriberEdit.btnLoading);
            })

            var subscriberDelete = {
                form            : 'form-delete-subscriber',
                button          : 'button-delete-subscriber',
                action          : 'delete',
                redirectType    : 'redirect',
                redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
                btnNormal       : false,
                btnLoading      : false
            };

            var targetButton = $("#"+subscriberDelete.button);
            targetButton.one('click',function() {
                ajaxSubmit(subscriberDelete.form, subscriberDelete.action, subscriberDelete.redirectType, subscriberDelete.redirect, subscriberDelete.btnNormal, subscriberDelete.btnLoading);
            })
        <?php
            endif;
        ?>
    })
</script>    