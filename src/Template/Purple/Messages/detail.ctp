<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Messages</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text">
                <i class="mdi mdi-reply btn-icon-prepend"></i>
                    Reply
                </button>
                <button type="button" class="btn btn-gradient-danger btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left button-delete-purple" data-purple-id="<?= $message->id ?>" data-purple-name="<?= $message->name ?>" data-purple-modal="#modal-delete-message">
                <i class="mdi mdi-delete btn-icon-prepend"></i>
                    Move to Trash
                </button>
            </div>
            <div class="card-body">
                <ul class="uk-comment-list purple-notification-list">
                    <li>
                        <article class="uk-comment uk-visible-toggle post-comment-list">
                            <header class="uk-comment-header uk-position-relative">
                                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                    <div class="uk-width-expand">
                                        <h5 class="uk-comment-title uk-margin-remove">
                                            <a class="uk-link-reset" href="#">
                                                <?= ucfirst($message->subject) ?> <br><small class="text-muted">From : <?= $message->name ?></small>
                                            </a></h5>
                                        <p class="uk-comment-meta uk-margin-remove-top">
                                            <i class="fa fa-clock-o"></i> <a class="uk-link-reset" href="#" data-livestamp="<?= $message->created ?>"></a><span class="fdb-button-option-divider uk-margin-small-left uk-margin-small-right">|</span> <i class="fa fa-envelope"></i> <?= $message->email ?>
                                        </p>
                                    </div>
                                </div>
                            </header>
                            <div class="uk-comment-body">
                                <p>
                                    <?= $message->content ?>   
                                </p>
                            </div>
                        </article>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="modal-delete-message" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($messageDelete, [
                'id'                    => 'form-delete-message',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['_name' => 'adminMessagesAction', 'action' => 'ajax-move-to-trash']
            ]);

            echo $this->Form->hidden('id');
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to delete message from <span class="bind-title"></span>? Make sure you already reply the message.</p>
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
                    'id'    => 'button-delete-message',
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
        var deleteMessage = {
            form            : 'form-delete-message',
            button          : 'button-delete-message',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(['_name' => 'adminMessages']); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+deleteMessage.button);
        targetButton.one('click',function() {
            ajaxSubmit(deleteMessage.form, deleteMessage.action, deleteMessage.redirectType, deleteMessage.redirect, deleteMessage.btnNormal, deleteMessage.btnLoading);
        })

    })
</script>