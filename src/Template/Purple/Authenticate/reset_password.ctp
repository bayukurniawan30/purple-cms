<?php
	echo $this->Form->create($newPassword, [
		'id'                    => 'form-reset-password',
		'class'                 => 'pt-3',
		'data-parsley-validate' => '',
		'url' 					=> ['action' => 'ajax-reset-password']
	]);

	echo $this->Form->hidden('ds', ['value' => $this->Url->build(['_name' => 'adminLogin'])]);
	echo $this->Form->hidden('id', ['value' => $id]);
	echo $this->Form->hidden('token', ['value' => $this->request->getParam('token')]);
	echo $this->Form->hidden('passwordscore', ['value' => '0', 'id' => 'password-score']);
	?>

<style>
	.pass-wrapper {
		width: 100%;
		position: absolute;
		bottom: 0;
		display: none;
	}
</style>

<div class="form-group">
	<div class="input-group">
		<?php
			echo $this->Form->password('password', [
				'id'                     => 'same-password',
				'class'                  => 'form-control input-lg',
				'placeholder'            => 'Password',
				'data-parsley-minlength' => '6',
				'data-parsley-maxlength' => '20',
				'uk-tooltip'			 => 'title: Required. 6-20 chars.; pos: bottom',
				'autocomplete' 			 => '',
				'required'               => 'required'
			]);
		?>
		<div class="input-group-append">
			<button id="button-generate-password" class="btn btn-gradient-success btn-sm" type="button" uk-tooltip="title: Generate password; pos: bottom"><i class="fa fa-key"></i></button>
		</div>
	</div>
	<div class="pwstrength-viewport-progress"></div>
</div>
<div class="form-group">
	<div class="input-group">
		<?php
			echo $this->Form->password('repeatpassword', [
				'class'                  => 'form-control input-lg',
				'placeholder'            => 'Repeat Password',
				'data-parsley-minlength' => '6',
				'data-parsley-maxlength' => '20',
				'uk-tooltip'			 => 'title: Required. Repeat Password. 6-20 chars.; pos: bottom',
				'data-parsley-equalto'   => '#same-password',
				'required'               => 'required'
			]);
		?>
		<div class="input-group-append">
			<button id="button-visible-password" class="btn btn-gradient-success btn-sm" type="button" uk-tooltip="title: Toggle visible password; pos: bottom"><i class="fa fa-eye"></i></button>
		</div>
	</div>
</div>
<?php
	echo $this->Form->button('Change Password', [
		'id'    => 'button-reset-password',
		'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn'
	]);
?>
<div class="form-group">
	<div id="error-message" class="uk-margin-small"></div>	
</div>
<?php
	echo $this->Form->end();
?>

<script>
	$(document).ready(function() {
		$('.pass-wrapper').css('width', '100%');
		$('.pass-wrapper').css('position', 'absolute');

		$('#same-password').password({
		  	shortPass: 'Are you sure? Don\'t tell me you choose that password ',
		  	badPass: 'Weak, try combining letters & numbers',
		  	goodPass: 'Good, try using special charecters',
		  	strongPass: 'Yeeaah, strong password',
		  	containsUsername: 'The password contains the username',
		  	showPercent: false,
		  	showText: false, // shows the text tips
		  	animate: true, // whether or not to animate the progress bar on input blur/focus
		  	animateSpeed: 'fast', // the above animation speed
		  	username: false, // select the username field (selector or jQuery instance) for better password checks
		  	usernamePartialMatch: true, // whether to check for username partials
		  	minimumLength: 6 // minimum password length (below this threshold, the score is 0)
		});

		$('#button-generate-password').click(function() {
			$('input[name=password]').val(password.generate());
			$('input[name=repeatpassword]').val($('input[name=password]').val());
			$('#password-score').val(85);
			$('.pass-wrapper').css('width', '100%');
			$('.pass-wrapper').css('position', 'absolute');
			$('.pass-wrapper').hide();
			return false;
		})

		function visiblePassword() {
            function visiblePassword1() {
				$(this).one("click", visiblePassword2);
				$(this).find('i').attr('class', 'fa fa-eye-slash');
				$('input[name=password]').attr('type', 'text');
				$('input[name=repeatpassword]').attr('type', 'text');
			}

            function visiblePassword2() {
				$(this).one("click", visiblePassword1);
				$(this).find('i').attr('class', 'fa fa-eye');
				$('input[name=password]').attr('type', 'password');
				$('input[name=repeatpassword]').attr('type', 'password');
            }
            $("#button-visible-password").one("click", visiblePassword1);
        };

        visiblePassword();

		$('#same-password').on('password.score', (e, score) => {
			$('#password-score').val(score);
		})

		var resetPassword;
    	var countClick = 0;
		$("#form-reset-password").submit(function(event){
            if ($(this).parsley().isValid()) {
				var passwordScore = $('#password-score').val();
				if (passwordScore < 50) {
					alert('Please use stronger password. Do not use weak password like admin, administrator, or admin123456.');
					event.preventDefault();
				}
				else {
					countClick++;
					if (resetPassword) {
						resetPassword.abort();
					}
					var $form   = $(this);
					var $inputs = $(this).find("button, input, select, textarea");
					var $button = $(this).find("button[type=submit]");
					var serializedData = $form.serialize();
					resetPassword = $.ajax({
						url: $form.attr('action'),
						type: "post",
						beforeSend: function(){
							$inputs.prop("disabled", true);
							$button.html('<i class="fa fa-spinner fa-pulse"></i> Updating Data...');
							$button.attr('disabled','disabled');},
						data: serializedData
					});
					resetPassword.done(function (msg){
						console.log(msg);
						var json   = $.parseJSON(msg);
						var status = (json.status);
						if(status == 'error') {
							var error = (json.error);
						}

						if(status == 'ok') {
							$button.html('<i class="fa fa-spinner fa-pulse"></i> Sending Email...');
							setTimeout(function() {
								$button.html('Change Password');
								$('#error-message').html('<div class="alert alert-success" role="alert" style="margin-top: 15px">Your password has been change. The new sign in information has been sent to your email. Please check your inbox or spam folder.</div>');
								window.location = "<?= $this->Url->build(['_name' => 'adminLogin']) ?>";
							}, 10000);
						}
						else if(status == 'error') {
							$button.removeAttr('disabled');
							$button.html('Change Password');
							alert('Error. ' + error);
							/*
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
							*/
						}
						else {
							$button.removeAttr('disabled');
							$button.html('Change Password');
							// $("#modal-message").modal('show');
							// $('#error-message').html('Error. Can\'t get you to the next step. Please try again.');
							alert('Error. Can\'t change your password. Please try again.');
						}
					});
					resetPassword.fail(function(jqXHR, textStatus) {
						$button.removeAttr('disabled');
						$button.html('Change Password');
						// $("#modal-message").modal('show');
						// $('#error-message').html('Error. '+textStatus);
						alert('Error. '+textStatus);
					});
					resetPassword.always(function () {
						$inputs.prop("disabled", false);
					});
					event.preventDefault();
				}
			}
		});
	})
</script>