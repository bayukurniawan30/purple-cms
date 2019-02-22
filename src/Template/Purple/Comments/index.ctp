<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div id="page-detail-card" class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Comments</h4>
            </div>
            <div class="card-body">
            	<?php
            		if ($comments->count() > 0):
            	?>
            	<ul class="uk-comment-list">
            		<?php
            			foreach ($comments as $comment):
            				$detailUrl = $this->Url->build([
								'_name'  => 'adminCommentsView',
								'blogid' => $this->request->getParam('blogid'),
								'id'     => $comment->id,
			                ]);
            		?>
				    <li>
				        <article class="uk-comment uk-visible-toggle post-comment-list" onclick="location.href='<?= $detailUrl ?>'">
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
				                        <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset"><?= $comment->name ?></a> <?php if ($comment->read == 'Unread'): ?><span class="uk-badge bg-danger unread-badge">Unread</span><?php endif; ?></h4>
				                        <p class="uk-comment-meta uk-margin-remove-top">
                                            <i class="fa fa-clock-o"></i> <a class="uk-link-reset" href="#" data-livestamp="<?= $comment->created ?>"></a><span class="fdb-button-option-divider uk-margin-small-left uk-margin-small-right">|</span><i class="fa fa-globe"></i> <?= $comment->text_status ?><span class="fdb-button-option-divider uk-margin-small-left uk-margin-small-right">|</span><i class="fa fa-comments-o"></i> <?= $this->cell('Comments::totalReplies', [$this->request->getParam('blogid'), $comment->id, 'text']); ?>
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
				                <p>
                                    <?= $this->Text->truncate(
                                        $comment->content,
                                        200,
                                        [
                                            'ellipsis' => '...',
                                            'exact'    => false,
                                            'html'     => true
                                        ]
                                    ); ?>    
                                </p>
				            </div>
				        </article>
				    </li>
				    <?php
				    	endforeach;
				    ?>
				</ul>
				<?php
					else:
				?>
				<div class="uk-alert-danger" uk-alert>
				    <p>Can't find comment for this post.</p>
				</div>
				<?php
					endif;
				?>
            </div>
            <?php
                if ($commentsTotal > $commentsLimit):
            ?>
             <div class="card-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="uk-position-center-left">Page <?= $this->Paginator->counter() ?></div>
                        </div>
                        <div class="col-md-6">
                            <ul class="uk-pagination purple-pagination uk-margin-remove-bottom uk-flex-right">
                                <?php
                                    if ($this->Paginator->current() - 1 <= 0) {
                                        $previousUrl = [
                                            '_name'  => 'adminCommentsPagination',
                                            'blogid' => $this->request->getParam('blogid'),
                                            'id'     => $this->Paginator->current() - 0
                                        ];
                                    }
                                    else {
                                        $previousUrl = [
                                            '_name'  => 'adminCommentsPagination',
                                            'blogid' => $this->request->getParam('blogid'),
                                            'id'     => $this->Paginator->current() - 1
                                        ];
                                    }

                                    if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                        $nextUrl = [
                                            '_name'  => 'adminCommentsPagination',
                                            'blogid' => $this->request->getParam('blogid'),
                                            'id'     => $this->Paginator->current() + 0
                                        ];
                                    }
                                    else {
                                        $nextUrl = [
                                            '_name'  => 'adminCommentsPagination',
                                            'blogid' => $this->request->getParam('blogid'),
                                            'id'     => $this->Paginator->current() + 1
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
            </div>  
            <?php endif ?>
        </div>
    </div>
</div>

<?php
	if ($comments->count() > 0):
?>
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

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($commentsTotal > $commentsLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>

        var changeStatus = {
            form            : 'form-status-comment',
            button          : 'button-status-comment',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(['_name' => 'adminComments', 'blogid' => $this->request->getParam('blogid')]); ?>',
            btnNormal       : false,
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Changing Status...'
        };

        var targetButton = $("#"+changeStatus.button);
        targetButton.one('click',function() {
            ajaxSubmit(changeStatus.form, changeStatus.action, changeStatus.redirectType, changeStatus.redirect, changeStatus.btnNormal, changeStatus.btnLoading);
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
	})
</script>   
<?php
	endif;
?>