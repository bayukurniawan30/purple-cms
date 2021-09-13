<p>
    Tags : 
   	<?php
   		$i = 1;
   		$total = $tags->count();
   		foreach ($tags as $tag):
   			$url = $this->Url->build([
  				'_name' => 'taggedPosts',
  				'tag'   => $tag->slug
  			]);

			if ($i == $total) {
				$text = '<a class="non-uikit" href="'.$url.'">' . $tag->title . '</a>';
			}
			else {
				$text = '<a class="non-uikit" href="'.$url.'">' . $tag->title . '</a>, ';
			}

			echo $text;

            $i++;
   		endforeach;
   		unset($i);
   	?>
</p>