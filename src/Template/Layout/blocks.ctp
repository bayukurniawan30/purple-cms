<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Blocks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <?= $this->Html->css('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') ?>
    <?= $this->Html->css('https://fonts.googleapis.com/css?family=Quicksand:300,400,700') ?>
    <?= $this->Html->css('/master-assets/plugins/froala-blocks/css/froala_blocks.css') ?>
    <?= $this->Html->css('/master-assets/css/bttn.css') ?>
    <?= $this->Html->css('/master-assets/css/blocks.css') ?>
    <?= $this->Html->css('/master-assets/plugins/uikit/css/uikit.css') ?>
    <?= $this->Html->script('/master-assets/js/vendor.bundle.base.js'); ?>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
	<!-- Fetch Content -->
	<?= $this->fetch('content') ?>

    <?= $this->Html->script('/master-assets/js/button.js'); ?>
	<?= $this->Html->script('/master-assets/js/purple.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/clipboardjs/clipboard.min.js'); ?>
	<!-- UI Kit -->
	<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>
</body>    