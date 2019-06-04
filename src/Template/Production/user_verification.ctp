<?php
	echo $this->Form->create($userVerification, [
		'id'                    => 'form-user-verification',
		'class'                 => 'pt-3',
		'data-parsley-validate' => '',
		'url' 					=> ['_name' => 'productionSiteAction', 'action' => 'ajax-user-verification']
	]);
?>
<div class="form-group">
	<?php
		echo $this->Form->text('email', [
			'type'        => 'email',
			'class'       => 'form-control input-lg',
			'placeholder' => 'Email',
			'uk-tooltip'  => 'title: Required; pos: bottom',
			'required'    => 'required'
		]);
	?>
</div>
<?php
	echo $this->Form->button('Verify Email', [
		'id'    => 'button-user-verification',
		'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn'
	]);
?>
<?php
	echo $this->Form->end();
?>

<script>
	$(document).ready(function() {
		var userVerification;
    	var countClick = 0;
		$("#form-user-verification").submit(function(event){
            if ($(this).parsley().isValid()) {
                countClick++;
                if (userVerification) {
                    userVerification.abort();
                }
                var $form   = $(this);
                var $inputs = $(this).find("button, input, select, textarea");
                var $button = $(this).find("button[type=submit]");
                var serializedData = $form.serialize();
                userVerification = $.ajax({
                    url: $form.attr('action'),
                    type: "post",
                    beforeSend: function(){
                        $inputs.prop("disabled", true);
                        $button.html('<i class="fa fa-spinner fa-pulse"></i> Processing...');
                        $button.attr('disabled','disabled');},
                    data: serializedData
                });
                userVerification.done(function (msg){
                    console.log(msg);
                    var json   = $.parseJSON(msg);
                    var status = (json.status);
                    if(status == 'error') {
                        var error = (json.error);
                        var error_type = (json.error_type);
                    }

                    if(status == 'ok') {
                        $button.html('<i class="fa fa-spinner fa-pulse"></i> Sending Code...');
                        setTimeout(function() {
                            window.location = "<?= $this->Url->build(['_name' => 'productionSiteAction', 'action' => 'codeVerification']) ?>";
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
                        $button.html('Verify Email');
                        $("#modal-message").modal('show');
                        // $('#error-message').html('Error. Can\'t get you to the next step. Please try again.');
                        alert('Error. Can\'t verify your email. Please try again.');
                    }
                });
                userVerification.fail(function(jqXHR, textStatus) {
                    $button.removeAttr('disabled');
                    $button.html('Verify Email');
                    $("#modal-message").modal('show');
                    // $('#error-message').html('Error. '+textStatus);
                    alert('Error. '+textStatus);
                });
                userVerification.always(function () {
                    $inputs.prop("disabled", false);
                });
                event.preventDefault();
			}
		});
	});
</script>