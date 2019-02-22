<h4>Post Comments (<?= $totalComments ?>)</h4>

<?php
	if ($totalComments > 0):
		foreach ($fetchComments as $comment):
			$totalReplies = $this->cell('Comments::fetchReplies', [$blog_id, $comment->id, 'count']);
?>
	<div class="media uk-margin-medium-bottom">
  		<img class="initial-photo uk-border-circle mr-3" src="" alt="<?= $comment->name ?>" data-name="<?= $comment->name ?>" data-height="50" data-width="50" data-char-count="2" data-font-size="20">
  		<div class="media-body">
    		<h5 class="mt-0 mb-2"><?= $comment->name ?><br><a class="non-uikit" href=""><small data-livestamp="<?= $comment->created ?>"></small></a></h5>
    		<?= $comment->content ?>

    		<?php
	        	if ($totalReplies > '0'):
					echo $this->cell('Comments::fetchReplies', [$blog_id, $comment->id, 'fetch']);
	        	endif;
	        ?>
  		</div>
	</div>
<?php
		endforeach;
	endif; 
?>