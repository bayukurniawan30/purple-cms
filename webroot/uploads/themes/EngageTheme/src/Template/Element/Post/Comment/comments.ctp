<h4 class="font-italic">Post Comments (<?= $totalComments ?>)</h4>

<?php
	if ($totalComments > 0):
?>
	<ul class="uk-comment-list">
		<?php
			foreach ($fetchComments as $comment):
				$totalReplies = $this->cell('Comments::fetchReplies', [$blog_id, $comment->id, 'count']);
		?>
	    <li>
	        <article class="uk-comment uk-visible-toggle">
	            <header class="uk-comment-header uk-position-relative">
	                <div class="uk-grid-medium uk-flex-middle" uk-grid>
	                    <div class="uk-width-auto">
	                    	<img class="initial-photo uk-comment-avatar" src="" alt="<?= $comment->name ?>" data-name="<?= $comment->name ?>" data-height="50" data-width="50" data-char-count="2" data-font-size="20">
	                    </div>
	                    <div class="uk-width-expand">
	                        <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#"><?= $comment->name ?></a></h4>
	                        <p class="uk-comment-meta uk-margin-remove-top"><a class="uk-link-reset" href="#" data-livestamp="<?= $comment->created ?>"></a></p>
	                    </div>
	                </div>
	            </header>
	            <div class="uk-comment-body">
	                <p><?= $comment->content ?></p>
	            </div>
	        </article>
	        <?php
	        	if ($totalReplies > '0'):
					echo $this->cell('Comments::fetchReplies', [$blog_id, $comment->id, 'fetch']);
	        	endif;
	        ?>
	    </li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>