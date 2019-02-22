<?php
	if ($tags->count() > 0):
?>
<div class="p-3">
    <h4 class="font-italic">Tags</h4>
    <?php
    	foreach($tags as $tag):
    		$url = $this->Url->build([
				'_name' => 'taggedPosts',
				'tag'   => $tag->slug
			]);
    ?>
	    <div class="tag-list"><a class="non-uikit" href="<?= $url ?>"><?= $tag->title ?></a></div>
    <?php
    	endforeach;
    ?>
</div>
<?php
	endif;
?>