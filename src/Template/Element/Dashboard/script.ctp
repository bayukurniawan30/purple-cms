<!-- plugins:js -->
<?= $this->Html->script('/master-assets/plugins/parsley/dist/parsley.js'); ?>
<?= $this->Html->script('/master-assets/js/vendor.bundle.addons.js'); ?>
<?= $this->Html->script('/master-assets/plugins/toast/jquery.toast.min.js'); ?>
<?= $this->Html->script('/master-assets/plugins/initial/initial.min.js'); ?>
<?= $this->Html->script('/master-assets/plugins/clipboardjs/clipboard.min.js'); ?>
<?= $this->Html->script('/master-assets/plugins/js-cookie/js.cookie.js'); ?>
<?= $this->Html->script('/master-assets/plugins/livestamp/livestamp.min.js'); ?>

<?php if ($this->request->getParam('controller') == 'Medias' || $this->request->getParam('controller') == 'Appearance'): ?>
	<?= $this->Html->script('/master-assets/plugins/dmuploader/js/jquery.dm-uploader.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/dmuploader/js/dmuploader-helper.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/datatables/datatables.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/datatables/Responsive-2.2.3/js/dataTables.responsive.min.js'); ?>
	<?= $this->Html->script('/master-assets/js/file-download.js'); ?>
<?php endif; ?>

<?php if ($this->request->getParam('controller') == 'Appearance' || ($this->request->getParam('controller') == 'Admins' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit'))): ?>
	<?= $this->Html->script('/master-assets/plugins/croppie/croppie.min.js'); ?>
<?php endif; ?>

<?php if ($this->request->getParam('controller') == 'Dashboard'): ?>
	<?= $this->Html->script('/master-assets/plugins/simple-weather/jquery.simpleWeather.min.js'); ?>
<?php endif; ?>

<?php if (($this->request->getParam('controller') == 'Admins' && $this->request->getParam('action') == 'index') || $this->request->getParam('controller') == 'Pages' || $this->request->getParam('controller') == 'Blogs' || $this->request->getParam('controller') == 'Subscribers'): ?>
	<?= $this->Html->script('/master-assets/plugins/datatables/datatables.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/datatables/Responsive-2.2.3/js/dataTables.responsive.min.js'); ?>
<?php endif; ?>

<?php if (($this->request->getParam('controller') == 'Admins' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit')) || ($this->request->getParam('controller') == 'Blogs' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit')) || $this->request->getParam('controller') == 'Themes'): ?>
	<?= $this->Html->script('/master-assets/plugins/dmuploader/js/jquery.dm-uploader.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/dmuploader/js/dmuploader-helper.js'); ?>
<?php endif; ?>

<?php if ($this->request->getParam('controller') == 'Pages'): ?>
	<?= $this->Html->script('/master-assets/plugins/jscolor/jscolor.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/leaflet/leaflet.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/stylesheet/jquery-cssrule.js'); ?>
<?php endif; ?>

<?php if ($this->request->getParam('controller') == 'Pages' || ($this->request->getParam('controller') == 'Blogs' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit')) || ($this->request->getParam('controller') == 'Settings' && $this->request->getParam('action') == 'seo')): ?>
	<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js'); ?>
	<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/froala-editor/js/froala_editor.pkgd.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/froala-editor/js/third_party/font_awesome.min.js'); ?>
	<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/fabric.js/1.6.7/fabric.min.js'); ?>
	<?= $this->Html->script('https://cdn.jsdelivr.net/npm/tui-code-snippet@1.4.0/dist/tui-code-snippet.min.js'); ?>
	<?= $this->Html->script('https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/froala-editor/js/third_party/image_tui.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/ace/ace/ace.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/ace/ace/theme-github.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/ace/ace/mode-html.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/ace/ace/mode-css.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/ace/jquery-ace.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/tag-editor/jquery.caret.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/jquery-ui/jquery-ui.min.js'); ?>
	<?= $this->Html->script('/master-assets/plugins/tag-editor/jquery.tag-editor.min.js'); ?>
<?php endif; ?>
<?php if ($this->request->getParam('controller') == 'Socials'): ?>
	<?= $this->Html->script('/master-assets/plugins/jssocials/js/jssocials.min.js'); ?>
<?php endif; ?>

<?php if ($this->request->getParam('controller') == 'Admins' && $this->request->getParam('action') == 'changePassword'): ?>
	<?= $this->Html->script('/master-assets/plugins/password/password.min.js'); ?>
<?php endif; ?>
<?php if ($this->request->getParam('controller') == 'Settings' && $this->request->getParam('action') == 'seo'): ?>
	<?= $this->Html->script('/master-assets/plugins/prism/prism.js'); ?>
<?php endif; ?>
<!-- endinject -->

<!-- Purple CMS Plugins Script -->
<?php
	foreach ($plugins as $plugin):
		if ($plugin['yes'] == true) {
			$pluginNamespace = $plugin['namespace'];
			$pluginAssets    = $plugin['dashboard_assets'];
			
			foreach ($pluginAssets as $asset):
				$assetType  = $asset['type'];
				$assetValue = $asset['value'];

				if ($assetType == 'script' && $assetValue == 'yes') {
					$assetElement = $asset['name'];
					echo $this->element($pluginNamespace . '.' . $assetElement);
				}
			endforeach;
		}
	endforeach;
?>

<!-- Plugin js for this page-->
<!-- End plugin js for this page-->
<!-- inject:js -->
<?= $this->Html->script('/master-assets/js/off-canvas.js'); ?>
<?= $this->Html->script('/master-assets/js/misc.js'); ?>
<!-- endinject -->
<!-- Custom js for this page-->
<?= $this->Html->script('/master-assets/js/dashboard.js'); ?>
<?= $this->Html->script('/master-assets/js/hoverable-collapse.js'); ?>
<!-- End custom js for this page-->
<!-- UI Kit -->
<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>
<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>
<!-- End UI Kit -->
<!-- Purple js -->
<?= $this->Html->script('/master-assets/js/purple.js'); ?>
<!-- End Purple js -->

<?php 
	if ($this->request->getParam('controller') == 'Dashboard') {
		echo $this->element('Dashboard/dashboard_script');
	}
?>
<script type="text/javascript">
    $(document).ready(function() {
        // Convert NULL photo to initial SVG
        $('.initial-photo').initial(); 

        setTimeout(function() {
			$("#dashboard-notifications").load('<?= $this->Url->build(['_name' => 'adminNotificationsAction', 'action' => 'ajaxLoadHeaderNotifications']) ?>');
			$("#messages-with-counter").load('<?= $this->Url->build(['_name' => 'adminMessagesAction', 'action' => 'ajaxMessagesCounter']) ?>');
        }, 3000);

		function loadNotificationsAndMessagesCounter(){
			$("#dashboard-notifications").load('<?= $this->Url->build(['_name' => 'adminNotificationsAction', 'action' => 'ajaxLoadHeaderNotifications']) ?>');
			$("#messages-with-counter").load('<?= $this->Url->build(['_name' => 'adminMessagesAction', 'action' => 'ajaxMessagesCounter']) ?>');
		}
		setInterval(function(){loadNotificationsAndMessagesCounter()}, 180000);
    })
</script>