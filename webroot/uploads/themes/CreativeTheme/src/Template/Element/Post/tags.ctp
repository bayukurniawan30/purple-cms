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

            $text = '<a class="non-uikit" href="'.$url.'">' . $tag->title . '</a>';

            echo $this->Purple->loopingList($text, ', ', ['initial' => $i, 'total' => $total]);

            $i++;
   		endforeach;
   		unset($i);
   	?>
</p>