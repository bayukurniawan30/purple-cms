<div class="p-3">
    <h4 class="font-italic">Category</h4>
    <ul class="uk-list uk-list-divider">
    	<?php
    		foreach ($categories as $category):
    			$url = $this->Url->build([
					'_name'    => 'postsInCategory',
					'category' => $category->slug
    			]);
    	?>
	    <li><a href="<?= $url ?>"><?= $category->name ?> <?= $this->cell('Categories::totalPost', [$category->id]); ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>