<?php
    if ($messages->count() > 0) {
        $folderInboxUrl = $this->Url->build([
            '_name'  => 'adminMessages'
        ]);
        $folderTrashUrl = $this->Url->build([
            '_name'  => 'adminMessagesFolder',
            'folder' => 'trash',
        ]);
    }
?>

<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Messages</h4>
            </div>
            <?php
                if ($messages->count() > 0):
            ?>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text active" onclick="location.href='<?= $folderInboxUrl ?>'">
                <i class="mdi mdi-bell btn-icon-prepend"></i>
                    Inbox
                </button>
                <button type="button" class="btn btn-gradient-danger btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left" onclick="location.href='<?= $folderTrashUrl ?>'">
                <i class="mdi mdi-delete btn-icon-prepend"></i>
                    Trash
                </button>
            </div>
            <?php endif; ?>
            <div class="card-body">
            	<?php
            		if ($messages->count() > 0):
            	?>
            	<ul class="uk-comment-list purple-notification-list">
                    <?php
                        $i = 1;
                        foreach ($messages as $message):
                            $url = $this->Url->build([
                                '_name' => 'adminMessagesView',
                                'id'    => $message->id
                            ]);
                    ?>
                    <li>
                        <article class="uk-comment uk-visible-toggle post-comment-list notification-<?= $i ?>" onclick="location.href='<?= $url ?>'">
                            <header class="uk-comment-header uk-position-relative">
                                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                    <div class="uk-width-expand">
                                        <h5 class="uk-comment-title uk-margin-remove">
                                            <a class="uk-link-reset" href="#">
                                                <?= ucfirst($message->subject) ?> <?php if ($message->read == 'Unread'): ?><span class="uk-badge bg-danger unread-badge">Unread</span><?php endif; ?> <br><small class="text-muted">From : <?= $message->name ?></small>
                                            </a> </h5>
                                        <p class="uk-comment-meta uk-margin-remove-top">
                                            <i class="fa fa-clock-o"></i> <a class="uk-link-reset" href="#" data-livestamp="<?= $message->created ?>"></a>
                                        </p>
                                    </div>
                                </div>
                            </header>
                            <div class="uk-comment-body">
                                <p>
                                    <?= $this->Text->truncate(
                                        $message->content,
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
                    <script type="text/javascript">
                        $(document).ready(function() {
                            <?php
                                if ($message->is_read == '0'):
                            ?>
                            var sendData = { id:'<?= $message->id ?>' };

                            var readNotification = {
                                button          : '.notification-<?= $i ?>',
                                ajaxType        : 'POST',
                                sendData        : sendData,
                                action          : 'link',
                                url             : '<?= $this->Url->build(['_name' => 'adminMessagesAction', 'action' => 'ajaxReadMessage']); ?>',
                                redirectType    : 'redirect',
                                redirect        : '<?= $url ?>',
                                btnNormal       : false,
                                btnLoading      : $('.notification-<?= $i ?>').html()
                            };

                            ajaxButton(readNotification.button, readNotification.ajaxType, readNotification.sendData, readNotification.action, readNotification.url, readNotification.redirectType, readNotification.redirect, readNotification.btnNormal, readNotification.btnLoading, false, false, true);
                            <?php
                                elseif ($message->is_read == '1'):
                            ?>
                            $('.notification-<?= $i ?>').click(function() {
                                var redirect = '<?= $url ?>';
                                window.location=redirect;
                                return false;
                            })
                            <?php
                                endif;
                            ?>
                        })
                    </script>
                    <?php 
                        $i++;
                        endforeach; 
                    ?>
            	</ul>
            	<?php
					else:
				?>
				<div class="uk-alert-danger" uk-alert>
				    <p>Can't find message for now.</p>
				</div>
				<?php
					endif;
				?>
            </div>
            <?php
                if ($messagesTotal > $messagesLimit):
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
                                            '_name'  => 'adminMessagesPagination',
                                            'id'     => $this->Paginator->current() - 0
                                        ];
                                    }
                                    else {
                                        $previousUrl = [
                                            '_name'  => 'adminMessagesPagination',
                                            'id'     => $this->Paginator->current() - 1
                                        ];
                                    }

                                    if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                        $nextUrl = [
                                            '_name'  => 'adminMessagesPagination',
                                            'id'     => $this->Paginator->current() + 0
                                        ];
                                    }
                                    else {
                                        $nextUrl = [
                                            '_name'  => 'adminMessagesPagination',
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

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($messagesTotal > $messagesLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>
    })
</script>