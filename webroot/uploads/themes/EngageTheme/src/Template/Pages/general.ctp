<div class="container">
  	<div class="row">
        <div class="col-md-6 uk-margin-top page-title-container">
        	<h3 class="page-title"><?= $pageTitle ?></h3>
        </div>
        <div class="col-md-6 breadcrumb-container">
        	<?= $this->element('breadcrumb', [
		        'breadcrumb' => $breadcrumb
			]) ?>
        </div>
    </div>
</div>

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