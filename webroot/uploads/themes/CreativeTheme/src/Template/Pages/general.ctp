<div class="row">
    <div class="col-xl-12">
        <div class="content-column-content">
            <h1 class="non-uikit"><?= $pageTitle ?></h1>

            <?php
            	$replaceFunction = $this->Purple->getAllFuncInHtml(html_entity_decode($viewPage->general->content));
            	if ($replaceFunction == false) {
            		echo html_entity_decode($viewPage->general->content);
            	}
            	else {
	            	$i = 1;
	            	foreach ($replaceFunction as $data):
	            		$functionName = trim(str_replace('function|', '', $data));
	            		if ($i == 1) {
		            		$html = str_replace('{{function|'.$functionName.'}}', $themeFunction->$functionName(), html_entity_decode($viewPage->general->content));
		            	}
		            	else {
		            		$html = str_replace('{{function|'.$functionName.'}}', $themeFunction->$functionName(), $html);
		            	}
	            		$i++;
	            	endforeach;

	            	echo $html;
            	}
            ?>
        </div>
    </div>
</div>