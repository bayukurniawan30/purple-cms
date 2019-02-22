<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div id="page-detail-card" class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Comments</h4>
            </div>
            <div class="card-toolbar">
            	<button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-modal-reply-comment" data-purple-modal="#modal-reply-comment" data-purple-name="<?= $comment->name ?>">
                <i class="mdi mdi-reply btn-icon-prepend"></i>
                	Reply Comment
                </button>
            </div>
            <div class="card-body">
            	<ul class="uk-comment-list">
            		<li>
				        <article class="uk-comment uk-visible-toggle">
				            <header class="uk-comment-header uk-position-relative">
				                <div class="uk-grid-medium uk-flex-middle" uk-grid>
				                    <div class="uk-width-auto">
				                    	<?php
				                    		if ($comment->admin_id == NULL):
				                    	?>
				                        <img class="uk-comment-avatar uk-border-circle initial-photo" width="50" height="50" data-width="50" data-height="50" data-char-count="2" data-font-size="20" data-name="<?= $comment->name ?>" alt="<?= $comment->name ?>">
				                    	<?php
				                    		endif;
				                    	?>
				                    </div>
				                    <div class="uk-width-expand">
				                        <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#"><?= $comment->name ?></a></h4>
				                        <p class="uk-comment-meta uk-margin-remove-top">
                                            <i class="fa fa-clock-o"></i> <a class="uk-link-reset" href="#" data-livestamp="<?= $comment->created ?>"></a><span class="fdb-button-option-divider uk-margin-small-left uk-margin-small-right">|</span> <i class="fa fa-globe"></i> <?= $comment->text_status ?>
                                        </p>
				                    </div>
				                </div>
				                <div class="uk-position-top-right uk-position-small uk-hidden-hover">
                                    <ul class="uk-iconnav">
                                        <li><a class="button-modal-status-comment" href="#" uk-icon="icon: settings" data-purple-modal="#modal-status-comment" data-purple-id="<?= $comment->id ?>" data-purple-status="<?= $comment->status ?>" uk-tooltip="title: Change Status"></a></li>
                                        <li><a class="button-delete-purple" href="#" uk-icon="icon: trash" data-purple-id="<?= $comment->id ?>" data-purple-name="<?= $comment->name ?>" data-purple-modal="#modal-delete-comment" uk-tooltip="title: Delete Comment"></a></li>
                                    </ul>
                                </div>
				            </header>
				            <div class="uk-comment-body">
				                <p><?= $comment->content ?></p>
				            </div>
				        </article>

                        <?php
                            if ($replies->count() > 0):
                        ?>
                        <ul class="uk-comment-list">
                        <?php
                                foreach ($replies as $reply):
                        ?>
                            <li>
                                <article class="uk-comment uk-visible-toggle">
                                    <header class="uk-comment-header uk-position-relative">
                                        <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                            <div class="uk-width-auto">
                                                <?php
                                                    if ($reply->admin->photo === NULL):
                                                ?>
                                                <img class="uk-comment-avatar uk-border-circle initial-photo" width="50" height="50" data-width="50" data-height="50" data-char-count="2" data-font-size="20" data-name="<?= $reply->admin->display_name ?>" alt="<?= $reply->admin->display_name ?>">
                                                <?php else: ?>
                                                <img class="uk-comment-avatar uk-border-circle" width="50" height="50" src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $reply->admin->photo ?>" alt="<?= $reply->admin->display_name ?>">
                                                <?php
                                                    endif;
                                                ?>
                                            </div>
                                            <div class="uk-width-expand">
                                                <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#"><?php if ($reply->admin->id == $sessionID) echo 'You'; else echo $reply->admin->display_name ?></a></h4>
                                                <p class="uk-comment-meta uk-margin-remove-top">
                                                    <i class="fa fa-clock-o"></i> <a class="uk-link-reset" href="#" data-livestamp="<?= $reply->created ?>"></a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="uk-position-top-right uk-position-small uk-hidden-hover">
                                            <ul class="uk-iconnav">
                                                <li><a class="button-delete-purple" href="#" uk-icon="icon: trash" data-purple-id="<?= $reply->id ?>" data-purple-name="<?php if ($reply->admin->id == $sessionID) echo 'You'; else echo $reply->admin->display_name ?>" data-purple-modal="#modal-delete-reply-comment" uk-tooltip="title: Delete Comment Reply"></a></li>
                                            </ul>
                                        </div>
                                    </header>
                                    <div class="uk-comment-body">
                                        <p><?= $reply->content ?></p>
                                    </div>
                                </article>
                            </li>
                        <?php
                                endforeach;
                        ?>
                        </ul>
                        <?php
                            endif;
                        ?>
				    </li>
            	</ul>
            </div>
        </div>
    </div>
</div>

<div id="modal-status-comment" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($commentStatus, [
                'id'                    => 'form-status-comment',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['_name' => 'adminCommentsAction', 'action' => 'ajax-change-status', 'blogid' => $this->request->getParam('blogid')]
            ]);

            echo $this->Form->hidden('blog_id', ['value' => $this->request->getParam('blogid')]);
            echo $this->Form->hidden('id');
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Change Comment Status</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'status',
                        [
                            '1' => 'Publish', 
                            '0' => 'Unpublish',
                        ],
                        [
                            'empty'    => 'Select Comment Status', 
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Change Status', [
                'id'    => 'button-status-comment',
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

<div id="modal-reply-comment" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($commentReply, [
                'id'                    => 'form-reply-comment',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['_name' => 'adminCommentsAction', 'action' => 'ajax-reply', 'blogid' => $this->request->getParam('blogid')]
            ]);

            echo $this->Form->hidden('blog_id', ['value' => $this->request->getParam('blogid')]);
            echo $this->Form->hidden('reply', ['value' => $this->request->getParam('id')]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Reply Comment from <span class="bind-from"></span></h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->textarea('content', [
                        'class'                  => 'form-control', 
                        'placeholder'            => 'Reply comment...',
                        'data-parsley-maxlength' => '1000',
                        'autofocus'              => 'autofocus',
                        'required'               => 'required',
                        'rows'                   => '4'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Reply', [
                'id'    => 'button-reply-comment',
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

<div id="modal-delete-comment" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($commentDelete, [
                'id'                    => 'form-delete-comment',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['_name' => 'adminCommentsAction', 'action' => 'ajax-delete', 'blogid' => $this->request->getParam('blogid')]
            ]);

            echo $this->Form->hidden('type', ['value' => 'master']);
            echo $this->Form->hidden('id');
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to delete comment from <span class="bind-title"></span>? It will also delete all your replies for this comment.</p>
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
                    'id'    => 'button-delete-comment',
                    'class' => 'btn btn-gradient-danger uk-margin-left'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<div id="modal-delete-reply-comment" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($commentDeleteReply, [
                'id'                    => 'form-delete-reply-comment',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['_name' => 'adminCommentsAction', 'action' => 'ajax-delete', 'blogid' => $this->request->getParam('blogid')]
            ]);

            echo $this->Form->hidden('type', ['value' => 'reply']);
            echo $this->Form->hidden('id');
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to delete reply comment from <span class="bind-title"></span>?</p>
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
                    'id'    => 'button-delete-reply-comment',
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
        var changeStatus = {
            form            : 'form-status-comment',
            button          : 'button-status-comment',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(['_name' => 'adminCommentsView', 'blogid' => $this->request->getParam('blogid'), 'id' => $this->request->getParam('id')]); ?>',
            btnNormal       : false,
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Changing Status...'
        };

        var targetButton = $("#"+changeStatus.button);
        targetButton.one('click',function() {
            ajaxSubmit(changeStatus.form, changeStatus.action, changeStatus.redirectType, changeStatus.redirect, changeStatus.btnNormal, changeStatus.btnLoading);
        })
        
    	var replyComment = {
            form            : 'form-reply-comment',
            button          : 'button-reply-comment',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(['_name' => 'adminCommentsView', 'blogid' => $this->request->getParam('blogid'), 'id' => $this->request->getParam('id')]); ?>',
            btnNormal       : false,
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Replying...'
        };

        var targetButton = $("#"+replyComment.button);
        targetButton.one('click',function() {
            ajaxSubmit(replyComment.form, replyComment.action, replyComment.redirectType, replyComment.redirect, replyComment.btnNormal, replyComment.btnLoading);
        })

        var deleteComment = {
            form            : 'form-delete-comment',
            button          : 'button-delete-comment',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(['_name' => 'adminComments', 'blogid' => $this->request->getParam('blogid')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+deleteComment.button);
        targetButton.one('click',function() {
            ajaxSubmit(deleteComment.form, deleteComment.action, deleteComment.redirectType, deleteComment.redirect, deleteComment.btnNormal, deleteComment.btnLoading);
        })

        var deleteReplyComment = {
            form            : 'form-delete-reply-comment',
            button          : 'button-delete-reply-comment',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(['_name' => 'adminCommentsView', 'blogid' => $this->request->getParam('blogid'), 'id' => $this->request->getParam('id')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+deleteReplyComment.button);
        targetButton.one('click',function() {
            ajaxSubmit(deleteReplyComment.form, deleteReplyComment.action, deleteReplyComment.redirectType, deleteReplyComment.redirect, deleteReplyComment.btnNormal, deleteReplyComment.btnLoading);
        })
	})
</script>    