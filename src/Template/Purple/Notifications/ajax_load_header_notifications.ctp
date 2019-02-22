<h6 class="p-3 mb-0">Notifications</h6>
<div class="dropdown-divider"></div>
<?php
    $notificationUrl = $this->Url->build([
        '_name'  => 'adminNotifications',
    ]);

    if ($notifications->count() > 0):
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
<a class="dropdown-item preview-item notification-<?= $i ?>" uk-tooltip="<?= $notification->content ?>">
    <!-- <div class="preview-thumbnail">
        <img class="initial-photo" src="" alt="<?= strtok($notification->content, " ") ?>" data-name="<?= strtok($notification->content, " ") ?>" data-height="30" data-width="30" data-char-count="1" data-font-size="15" style="max-width: none">
    </div> -->
    <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
        <h6 class="preview-subject ellipsis mb-1 font-weight-normal"><?= $notification->content ?></h6>
        <p class="text-gray mb-0 uk-margin-remove-top" data-livestamp="<?= $notification->created ?>"></p>
    </div>
</a>
<div class="dropdown-divider"></div>

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
        unset($i);
    else:
?>
<!-- <p>You have no notification now.</p> -->
<?php endif; ?>
<h6 class="p-3 mb-0 uk-margin-remove-top text-center"><a href="<?= $notificationUrl ?>"><?= $this->Purple->plural($notificationsTotal, ' new notification', 's', true) ?></a></h6>

<?php
    if ($notifications->count() > 0):
?>
<?= $this->Html->script('/master-assets/plugins/initial/initial.min.js'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Convert NULL photo to initial SVG
        $('.initial-photo').initial(); 

        // Add notification circle 
        $('#messageDropdown').append('<span class="count-symbol bg-danger"></span>');
    })
</script>
<?php else: ?>
<script type="text/javascript">
    $(document).ready(function() {
        // Remove notification circle 
        $('#messageDropdown').find('.count-symbol').remove();
    })
</script>
<?php endif; ?>