<?php
	if ($tags->count() > 0):
?>
<div class="p-3">
    <h4 class="font-italic">Tags</h4>
    <?php
    	foreach($tags as $tag):
            $totalPosts = $this->cell('Tags::totalPostsInTag', [$tag->id]);

            if ($totalPosts != '0'):
        		$url = $this->Url->build([
    				'_name' => 'taggedPosts',
    				'tag'   => $tag->slug
    			]);
    ?>
	    <div class="tag-list"><a class="non-uikit" href="<?= $url ?>"><?= $tag->title ?></a></div>
    <?php
            endif;
    	endforeach;
    ?>
</div>
<?php
	endif;
?>