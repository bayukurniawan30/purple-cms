<!-- DOCTYPE -->
<?= $this->Html->docType(); ?>
<html lang="en">
	<?= $this->element('head') ?>
  	<body>
  		
		<?= $this->element('navigation') ?>
  		
  		<!-- Fetch Content -->
		<?= $this->fetch('content') ?>

		<?= $this->element('footer') ?>
		<?= $this->element('script') ?>
  	</body>
</html>