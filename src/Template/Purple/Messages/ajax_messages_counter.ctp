<i class="mdi mdi-email-outline mr-2 text-success"></i> Messages
<?php
	if ($counter > 0):
		$messagesCounter = $this->Purple->notificationCounter($counter);
?>
<span class="uk-badge bg-danger unread-badge uk-margin-small-left"><?= $messagesCounter ?></span>
<?php
	else:
		echo '';
	endif;
?>