<?php
    $filterAllUrl = $this->Url->build([
        '_name'  => 'adminNotifications'
    ]);
    $filterReadUrl = $this->Url->build([
        '_name'  => 'adminNotificationsFilter',
        'filter' => 'read',
    ]);
    $filterUnreadUrl = $this->Url->build([
        '_name'  => 'adminNotificationsFilter',
        'filter' => 'unread',
    ]);
?>

<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Notifications</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text" onclick="location.href='<?= $filterAllUrl ?>'">
                <i class="mdi mdi-bell btn-icon-prepend"></i>
                    All
                </button>
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left <?php if ($this->request->getParam('filter') == 'read') echo 'active' ?>" onclick="location.href='<?= $filterReadUrl ?>'">
                <i class="mdi mdi-email-open btn-icon-prepend"></i>
                    Read
                </button>
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left <?php if ($this->request->getParam('filter') == 'unread') echo 'active' ?>" onclick="location.href='<?= $filterUnreadUrl ?>'">
                <i class="mdi mdi-email btn-icon-prepend"></i>
                    Unread
                </button>
            </div>
            <div class="card-body">
            	<?php
            		if ($notifications->count() > 0):
            	?>
            	<ul class="uk-comment-list purple-notification-list">
                    <?php
                        $i = 1;
                        foreach ($notifications as $notification):
                            if ($notification->type == 'comment') {
                                $url = $this->Url->build([
                                    '_name'  => 'adminCommentsView',
                                    'blogid' => $notification->blog_id,
                                    'id'     => $notification->comment_id
                                ]);
                            }
                            elseif ($notification->type == 'message') {
                                $url = $this->Url->build([
                                    '_name'  => 'adminMessagesView',
                                    'id'     => $notification->message_id
                                ]);
                            }
                    ?>
                    <li>
                        <article class="uk-comment uk-visible-toggle post-comment-list" onclick="location.href='<?= $url ?>'">
                            <header class="uk-comment-header uk-position-relative">
                                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                                    <!-- <div class="uk-width-auto">
                                        <img class="uk-comment-avatar uk-border-circle initial-photo" width="50" height="50" data-width="50" data-height="50" data-char-count="2" data-font-size="20" data-name="<?= strtok($notification->content, " ") ?>" alt="<?= strtok($notification->content, " ") ?>">
                                    </div> -->
                                    <div class="uk-width-expand">
                                        <h5 class="uk-comment-title uk-margin-remove">
                                            <a class="uk-link-reset" href="#">
                                                <?php
                                                    if ($notification->type == 'comment') {
                                                        echo 'Post Comment';
                                                    }
                                                    elseif ($notification->type == 'message') {
                                                        echo 'Contact Message';
                                                    }
                                                ?>
                                            </a> <?php if ($notification->read == 'Unread'): ?><span class="uk-badge bg-danger unread-badge">Unread</span><?php endif; ?></h5>
                                        <p class="uk-comment-meta uk-margin-remove-top">
                                            <i class="fa fa-clock-o"></i> <a class="uk-link-reset" href="#" data-livestamp="<?= $notification->created ?>"></a>
                                        </p>
                                    </div>
                                </div>
                            </header>
                            <div class="uk-comment-body">
                                <p>
                                    <?php 
                                        if ($notification->type == 'comment') {
                                            $content = $notification->content;
                                            $explodeContent = explode('.', $content);
                                            echo $explodeContent[0] . ' (<strong>' . $this->cell('Notifications::commentNotification', [$notification->blog_id]) . '</strong>).' . $explodeContent[1];
                                        }
                                        else {
                                            echo $notification->content;
                                        }
                                    ?>    
                                </p>
                            </div>
                        </article>
                    </li>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            <?php
                                if ($notification->is_read == '0'):
                            ?>
                            var sendData = { id:'<?= $notification->id ?>' };

                            var readNotification = {
                                button          : '.notification-<?= $i ?>',
                                ajaxType        : 'POST',
                                sendData        : sendData,
                                action          : 'link',
                                url             : '<?= $this->Url->build(['_name' => 'adminNotificationsAction', 'action' => 'ajaxReadNotification']); ?>',
                                redirectType    : 'redirect',
                                redirect        : '<?= $url ?>',
                                btnNormal       : false,
                                btnLoading      : $('.notification-<?= $i ?>').html()
                            };

                            ajaxButton(readNotification.button, readNotification.ajaxType, readNotification.sendData, readNotification.action, readNotification.url, readNotification.redirectType, readNotification.redirect, readNotification.btnNormal, readNotification.btnLoading, false, false, true);
                            <?php
                                elseif ($notification->is_read == '1'):
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
				    <p>Can't find notification for now.</p>
				</div>
				<?php
					endif;
				?>
            </div>
            <?php
                if ($notificationsTotal > $notificationsLimit):
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
                                            '_name'  => 'adminNotificationsPagination',
                                            'id'     => $this->Paginator->current() - 0
                                        ];
                                    }
                                    else {
                                        $previousUrl = [
                                            '_name'  => 'adminNotificationsPagination',
                                            'id'     => $this->Paginator->current() - 1
                                        ];
                                    }

                                    if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                        $nextUrl = [
                                            '_name'  => 'adminNotificationsPagination',
                                            'id'     => $this->Paginator->current() + 0
                                        ];
                                    }
                                    else {
                                        $nextUrl = [
                                            '_name'  => 'adminNotificationsPagination',
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
            if ($notificationsTotal > $notificationsLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>
    })
</script>