<?php 
	if ($type == 'count'):
		echo $replyComments;
	elseif ($type == 'fetch'):
?>
<ul>
	<?php
		foreach ($replyComments as $reply):
	?>
    <li>
        <article class="uk-comment uk-visible-toggle">
            <header class="uk-comment-header uk-position-relative">
                <div class="uk-grid-medium uk-flex-middle" uk-grid>
                    <div class="uk-width-auto">
                    	<?php
                            if ($reply->admin->photo === NULL):
                        ?>
                        <img class="uk-comment-avatar initial-photo" width="50" height="50" data-width="50" data-height="50" data-char-count="2" data-font-size="20" data-name="<?= $reply->admin->display_name ?>" alt="<?= $reply->admin->display_name ?>">
                        <?php else: ?>
                        <img class="uk-comment-avatar" width="50" height="50" src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $reply->admin->photo ?>" alt="<?= $reply->admin->display_name ?>">
                        <?php
                            endif;
                        ?>
                    </div>
                    <div class="uk-width-expand">
                        <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#"><?= $reply->admin->display_name ?></a></h4>
                        <p class="uk-comment-meta uk-margin-remove-top"><a class="uk-link-reset" href="#" data-livestamp="<?= $reply->created ?>"></a></p>
                    </div>
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