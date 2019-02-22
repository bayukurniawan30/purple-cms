<?php
	$explodeBreadcrumb = explode('::', $breadcrumb);
	$total = count($explodeBreadcrumb);
?>
<div class="page-breadcrumb uk-align-right@m">
	<ul class="uk-breadcrumb">
		<?php
			$i = 1;
			foreach ($explodeBreadcrumb as $list):
				if ($list == 'Home') {
					$url = $this->Url->build(['_name' => 'home']);
				}
				else {
					$url = '';
				}
		?>
	    <li><?php if ($i > 1) echo '<span>'; else echo '<a href="'.$url.'">'; ?><?= $list ?><?php if ($i > 1) echo '</span>'; else echo '</a>'; ?></li>
	    <?php
	    		$i++;
	    	endforeach;
	    ?>
	</ul>
</div>