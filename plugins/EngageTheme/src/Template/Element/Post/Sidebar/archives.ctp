<div class="p-3">
    <h4 class="font-italic">Archives</h4>
    <ul class="uk-list uk-list-divider">
    	<?php
    		foreach ($archives as $archive):
    			$date = date('F Y', strtotime($archive->created));
    			$url = $this->Url->build([
					'_name' => 'archivesPost',
					'year'  => date('Y', strtotime($archive->created)),
					'month' => date('m', strtotime($archive->created))
    			]);
    	?>
        <li><a href="<?= $url ?>"><?= $date ?></a></li>
	    <?php endforeach; ?>
    </ul>
</div>