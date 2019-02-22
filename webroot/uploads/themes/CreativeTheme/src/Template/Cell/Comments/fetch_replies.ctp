<?php 
	if ($type == 'count'):
		echo $replyComments;
	elseif ($type == 'fetch'):
        foreach ($replyComments as $reply):
?>
<div class="media uk-margin-top">
    <?php
        if ($reply->admin->photo === NULL):
    ?>
    <img class="initial-photo uk-border-circle mr-3" width="50" height="50" data-width="50" data-height="50" data-char-count="2" data-font-size="20" data-name="<?= $reply->admin->display_name ?>" alt="<?= $reply->admin->display_name ?>">
    <?php else: ?>
    <img class="uk-border-circle mr-3" width="50" height="50" src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $reply->admin->photo ?>" alt="<?= $reply->admin->display_name ?>">
    <?php
        endif;
    ?>
    <div class="media-body">
        <h5 class="mt-0 mb-2"><?= $reply->admin->display_name ?><br><a class="non-uikit" href=""><small data-livestamp="<?= $reply->created ?>"></small></a></h5>
        <?= $reply->content ?>
    </div>
</div>
<?php
        endforeach;
    endif;
?>