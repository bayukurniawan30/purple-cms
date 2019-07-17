<!DOCTYPE html>
<html lang="en">
	<!-- Head Element -->
	<?= $this->element('Dashboard/head') ?>

	<body class="<?php if (($this->request->getParam('controller') == 'Pages' && $this->request->getParam('action') == 'detail') || ($this->request->getParam('controller') == 'Blogs' && ($this->request->getParam('action') == 'index' || $this->request->getParam('action') == 'filterCategory'))) echo 'sidebar-icon-only' ?>">

		<!--CSRF Token-->
		<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
        <!-- Client Timezone -->
		<input id="client-timezone-url" type="hidden" name="clientTimezoneUrl" value="<?= $this->Url->build(['_name' => 'setClientTimezone']); ?>">
		<!--Froala Manager URL-->
        <input id="froala-load-url" type="hidden" name="froalaLoadURL" value="<?= $this->Url->build(["_name" => "adminFroalaAction", "action" => "froalaManagerLoadUrl"]); ?>">
        <!--Froala Image Upload URL-->
        <input id="froala-image-upload-url" type="hidden" name="froalaImageURL" value="<?= $this->Url->build(["_name" => "adminFroalaAction", "action" => "froalaImageUploadUrl"]); ?>">
        <!--Froala File Upload URL-->
        <input id="froala-file-upload-url" type="hidden" name="froalaImageURL" value="<?= $this->Url->build(["_name" => "adminFroalaAction", "action" => "froalaFileUploadUrl"]); ?>">
        <!--Froala Video Upload URL-->
        <input id="froala-video-upload-url" type="hidden" name="froalaImageURL" value="<?= $this->Url->build(["_name" => "adminFroalaAction", "action" => "froalaVideoUploadUrl"]); ?>">

	  	<div class="container-scroller">
			<!-- Header Element -->
			<?= $this->element('Dashboard/header') ?>

			<div class="container-fluid page-body-wrapper">
				<!-- Sidebar Element -->
				<?= $this->element('Dashboard/sidebar') ?>

				<div class="main-panel">
			        <div class="content-wrapper">
						<!-- Breadcrumb Element -->
						<?= $this->element('Dashboard/breadcrumb') ?>

						<!-- Fetch Content -->
						<?= $this->fetch('content') ?>
			        </div>
			    </div>
			</div>
		</div>

		<!-- Disallowed Modal -->
		<?= $this->element('Dashboard/Modal/disallowed_delete_modal') ?>

		<!-- Script Element -->
		<?= $this->element('Dashboard/script') ?>
	</body>
</html>