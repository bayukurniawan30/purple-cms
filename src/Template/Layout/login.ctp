<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Sign In | Purple CMS</title>
	<?= $this->Html->css('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') ?>
	<!-- plugins:css -->
	<?= $this->Html->css('/master-assets/plugins/iconfonts/mdi/css/materialdesignicons.min.css') ?>
	<?= $this->Html->css('/master-assets/css/vendor.bundle.base.css') ?>
	<!-- endinject -->
	<!-- plugin css for this page -->
	<?= $this->Html->css('/master-assets/plugins/parsley/src/parsley.css') ?>
	<!-- End plugin css for this page -->
	<!-- inject:css -->
	<?= $this->Html->css('/master-assets/css/style.css') ?>
	<!-- endinject -->
	<?= $this->Html->script('/master-assets/js/vendor.bundle.base.js'); ?>
	<?= $this->Html->script('/master-assets/js/notification.js'); ?>
	<?= $this->Html->script('/master-assets/js/ajax.js'); ?>
	<!-- Favicon -->
	<link rel="icon" href="<?= $this->request->getAttribute("webroot").'master-assets/img/favicon.png' ?>">
	
	<style type="text/css">
		.auth .brand-logo img {
			width: 100%;
		}
		.forgot-password {
			margin-top: 15px;
		}
		.forgot-password a {
			color: #b66dff;
		}
		.forgot-password a:hover, .forgot-password a:focus {
			color: #a347ff;
			text-decoration: none;
		}
		#particles-js {
			position: fixed;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			z-index: 1;
		}
		.bg-gradient-primary {
			background: #b66dff;
			background: -moz-radial-gradient(center, ellipse cover, #9c5eff 0%, #d98bff 100%);
			background: -webkit-radial-gradient(center, ellipse cover, #9c5eff 0%,#d98bff 100%);
			background: radial-gradient(ellipse at center, #9c5eff 0%,#d98bff 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#9c5eff', endColorstr='#d98bff',GradientType=1 );
		}
		/* Login Form */
		@media(max-width: 767px) {
				.login-background .row {
						margin-right: 0;
						margin-left: 0;
				}
				.content-wrapper {
						padding: 2rem .5rem!important;
				}
		}
		<?php if ($settingDefaultBgLogin->value != 'no'): ?>
		.content-wrapper {
			background: none; 
		}
		<?php endif; ?>
	</style>
</head>

<body class="">
	<div class="container-scroller">
		<?php if ($settingDefaultBgLogin->value != 'no'): ?>
			<div id="particles-js" class="bg-gradient-primary"></div>
		<?php endif; ?>
		<div class="container-fluid page-body-wrapper full-page-wrapper" style="position: relative; z-index: 5">
			<div class="content-wrapper d-flex align-items-center auth login-background" <?php if ($settingDefaultBgLogin->value == 'no'): ?><?= $settingBgLogin->value != '' ? 'style="background: url('.$this->request->getAttribute("webroot") . 'uploads/images/original/'. $settingBgLogin->value .'); background-size: cover;"' : '' ?><?php endif; ?>>
				
				<div class="row w-100">
					<div class="col-lg-4 mx-auto">
						<div class="auth-form-light text-left p-5">
							<div class="brand-logo">
								<?= $this->Html->image('/master-assets/img/logo.svg', ['alt' => 'Purple CMS', 'data-id' => 'login-cover-image', 'width' => '100%']) ?>
							</div>
							<h4>Sign In to feel the Purple</h4>
							<h6 class="font-weight-light">Purple is Awesome</h6>
							<?= $this->fetch('content') ?>
						</div>
					</div>
				</div>
			</div>
			<!-- content-wrapper ends -->
		</div>
		<!-- page-body-wrapper ends -->
	</div>
	<!-- container-scroller -->
	<!-- plugins:js -->
	<?= $this->Html->script('/master-assets/plugins/parsley/dist/parsley.js'); ?>
	<?php if ($settingDefaultBgLogin->value != 'no'): ?>
	<?= $this->Html->script('/master-assets/plugins/particlejs/particles.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/particlejs/particles.app.js'); ?>
	<?php endif; ?>
	<!-- endinject -->
	<!-- inject:js -->
	<?= $this->Html->script('/master-assets/js/off-canvas.js'); ?>
	<?= $this->Html->script('/master-assets/js/misc.js'); ?>
	<!-- endinject -->
	<script type="text/javascript">
		var cakeDebug = "<?= $cakeDebug ?>";
		window.cakeDebug = cakeDebug;
	</script>
</body>

</html>
