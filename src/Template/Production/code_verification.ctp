<?php
	echo $this->Form->create($codeVerification, [
		'id'                    => 'form-code-verification',
		'class'                 => 'uk-grid pt-3',
		'data-parsley-validate' => '',
		'url' 					=> ['_name' => 'productionSiteAction', 'action' => 'ajax-code-verification']
	]);
?>
<div class="uk-width-1-1 uk-margin-small">
	<?php
		echo $this->Form->text('code', [
			'class'                  => 'uk-input',
			'placeholder'            => 'Verification Code',
			'uk-tooltip'             => 'title: Required; pos: bottom-left',
			'data-parsley-minlength' => '6',
			'data-parsley-maxlength' => '6',
			'required'               => 'required'
		]);
	?>
</div>
<div class="uk-width-1-1 uk-margin-small">
<?php
	echo $this->Form->button('Verify Code', [
		'id'    => 'button-code-verification',
		'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn'
	]);
?>
</div>
<?php
	echo $this->Form->end();
?>

<script>
	$(document).ready(function() {
		var codeVerification;
    	var countClick = 0;
		$("#form-code-verification").submit(function(event){
            if ($(this).parsley().isValid()) {
                countClick++;
                if (codeVerification) {
                    codeVerification.abort();
                }
                var $form   = $(this);
                var $inputs = $(this).find("button, input, select, textarea");
                var $button = $(this).find("button[type=submit]");
                var serializedData = $form.serialize();
                codeVerification = $.ajax({
                    url: $form.attr('action'),
                    type: "post",
                    beforeSend: function(){
                        $inputs.prop("disabled", true);
                        $button.html('<i class="fa fa-spinner fa-pulse"></i> Processing...');
                        $button.attr('disabled','disabled');},
                    data: serializedData
                });
                codeVerification.done(function (msg){
                    console.log(msg);
                    var json   = $.parseJSON(msg);
                    var status = (json.status);
                    if(status == 'error') {
                        var error = (json.error);
                        var error_type = (json.error_type);
                    }

                    if(status == 'ok') {
                        $button.html('<i class="fa fa-spinner fa-pulse"></i> Checking Code...');
                        setTimeout(function() {
                            window.location = "<?= $this->Url->build(['_name' => 'productionSiteAction', 'action' => 'databaseMigration']) ?>";
                        }, 5000);
                    }
                    else if(status == 'error') {
                        $button.removeAttr('disabled');
                        $button.html('Next');
                        if (error_type == 'form') {
                            $.each (error, function (index) {
                                var validation = error[index];
                                var validationText = '';
                                $.each (validation, function (indexValidation) {
                                    validationText += validation[indexValidation]+", ";
                                })
                                if(countClick == 1) {
                                    console.log('adderror');
                                    $form.find($("input[name="+index+"]")).parsley().addError(index+'Validation', {message: validationText, updateClass: true});
                                }
                                else {
                                    console.log('updateerror');
                                    $form.find($("input[name="+index+"]")).parsley().updateError(index+'Validation', {message: validationText, updateClass: true});
                                }
                            });
                        }
                        else {
                            alert('Error. ' + error);
                        }
                    }
                    else {
                        $button.removeAttr('disabled');
                        $button.html('Verify Code');
                        $("#modal-message").modal('show');
                        // $('#error-message').html('Error. Can\'t get you to the next step. Please try again.');
                        alert('Error. Can\'t verify the code. Please try again.');
                    }
                });
                codeVerification.fail(function(jqXHR, textStatus) {
                    $button.removeAttr('disabled');
                    $button.html('Verify Code');
                    $("#modal-message").modal('show');
                    // $('#error-message').html('Error. '+textStatus);
                    alert('Error. '+textStatus);
                });
                codeVerification.always(function () {
                    $inputs.prop("disabled", false);
                });
                event.preventDefault();
			}
		});
	});
</script>