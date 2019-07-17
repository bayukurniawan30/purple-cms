<!-- JavaScript & jQuery Plugins -->
<!-- Bootstrap -->
<?= $this->Html->script('bootstrap.min.js'); ?>
<!-- UI Kit -->
<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>
<?= $this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>
<!-- Parsley -->
<?= $this->Html->script('/master-assets/plugins/parsley/dist/parsley.js'); ?>
<!-- Initial -->
<?= $this->Html->script('/master-assets/plugins/initial/initial.min.js'); ?>
<!-- Livestamp -->
<?= $this->Html->script('/master-assets/plugins/livestamp/livestamp.min.js'); ?>
<!-- Purple -->
<?= $this->Html->script('/master-assets/js/ajax-front-end.js'); ?>
<?= $this->Html->script('/master-assets/js/purple-front-end.js'); ?>
<script type="text/javascript">
	$(document).ready(function(){
		var urlActionContactForm = '<?= $this->Url->build(['_name' => 'ajaxSendContact']) ?>';
		var urlActionCommentForm = '<?= $this->Url->build(['_name' => 'ajaxSendComment']) ?>';
		 
		$('body').find('.form-send-contact').attr('action', urlActionContactForm);
		$('body').find('.form-send-contact').prepend('<input type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>');
		$('body').find('.form-send-contact').prepend('<input type="hidden" name="ds" value=<?= $this->Url->build(['_name' => 'adminMessages']); ?>>');
		$('body').find('.form-send-comment').attr('action', urlActionCommentForm);

		<?php if ($formSecurity == 'on'): ?>
    	$('body').find('.form-send-contact').prepend('<input type="hidden" name="status" value="">');
    	$('body').find('.form-send-contact').prepend('<input type="hidden" name="score" value="">');
	    if ($('.form-send-contact').length > 0) {
		    grecaptcha.ready(function() {
		        grecaptcha.execute('<?= $recaptchaSitekey ?>', {action: 'ajaxVerifyForm'}).then(function(token) {
		            fetch('<?= $this->Url->build(['_name' => 'ajaxVerifyForm', 'action' => 'ajaxVerifyForm', 'token' => '']); ?>' + token).then(function(response) {
		                response.json().then(function(data) {
		                    var json    = $.parseJSON(data),
		                        success = (json.success),
		                        score   = (json.score);

		                    if (success == true) {
		                       $('.form-send-contact').find('input[name=status]').val('success'); 
		                       $('.form-send-contact').find('input[name=score]').val(score); 
		                    }
		                    else {
		                       $('.form-send-contact').find('input[name=status]').val('failed'); 
		                       $('.form-send-contact').find('input[name=score]').val('0'); 
		                    }
		                });
		            });
		        });
		    });
	    }
	    <?php endif; ?>

		$('.initial-photo').initial(); 
	})
</script>

<!-- Google Analytics Code -->
<?php echo $googleAnalytics != '' ? $googleAnalytics : ''; ?>