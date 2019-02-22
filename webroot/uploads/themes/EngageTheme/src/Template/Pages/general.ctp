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
    echo html_entity_decode($viewPage->general->content);
?>