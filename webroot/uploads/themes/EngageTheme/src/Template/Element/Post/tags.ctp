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

   			if ($i < $total) {
                echo '<a href="'.$url.'">' . $tag->title . '</a>, ';
            }
            else {
                echo '<a href="'.$url.'">' . $tag->title . '</a>';
            }

            $i++;
   		endforeach;
   		unset($i);
   	?>
</p>