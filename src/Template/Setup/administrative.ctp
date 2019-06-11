<?php
	// Check HTTP type of the URL
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
		$checkHttp = 'https://';
	}
	else {
		$checkHttp = 'http://';
	}
?>

<style>
	.pass-wrapper {
		width: 100%;
		position: absolute;
		bottom: 0;
		display: none;
	}
</style>

<?php
	echo $this->Form->create($setupAdministrative, [
		'id'                    => 'form-sitename-admin-setup',
		'class'                 => 'uk-grid pt-3',
		'data-parsley-validate' => '',
		'url' 					=> ['action' => 'ajax-administrative']
	]);

	echo $this->Form->hidden('ds', ['value' => $this->Url->build(['_name' => 'adminLogin'])]);
	echo $this->Form->hidden('siteurl', ['value' => $checkHttp]);
	echo $this->Form->hidden('foldername', ['value' => $this->Setup->checkSubfolder()]);
	echo $this->Form->hidden('passwordscore', ['value' => '0', 'id' => 'password-score']);
?>
<div class="uk-width-1-1 uk-margin-small">
	<?php
		echo $this->Form->text('sitename', [
			'class'                  => 'uk-input',
			'placeholder'            => 'Site Name',
			'data-parsley-minlength' => '1',
			'data-parsley-maxlength' => '30',
            'autofocus'              => 'autofocus',
			'uk-tooltip'			 => 'title: Required. Max 30 chars.; pos: bottom',
			'required'               => 'required'
		]);
	?>
</div>
<div class="uk-width-1-1 uk-margin-small">
	<?php
		echo $this->Form->text('username', [
			'class'                  => 'uk-input',
			'placeholder'            => 'Username',
			'data-parsley-type'      => 'alphanum',
			'data-parsley-minlength' => '6',
			'data-parsley-maxlength' => '15',
			'uk-tooltip'			 => 'title: Required. Alpha numeric. 6-15 chars.; pos: bottom',
			'required'               => 'required'
		]);
	?>
</div>
<div class="uk-width-1-2 uk-margin-small">
	<div class="uk-inline" style="width: 100%">
		<a id="button-generate-password" class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: file-edit" uk-tooltip="title: Generate password; pos: bottom-right"></a>
		<?php
			echo $this->Form->password('password', [
				'id'                     => 'same-password',
				'class'                  => 'uk-input',
				'placeholder'            => 'Password',
				'data-parsley-minlength' => '6',
				'data-parsley-maxlength' => '20',
				'uk-tooltip'			 => 'title: Required. 6-20 chars.; pos: bottom',
				'autocomplete' 			 => 'off',
				'required'               => 'required'
			]);
		?>
	</div>
	<div class="pwstrength-viewport-progress"></div>
</div>
<div class="uk-width-1-2 uk-margin-small">
	<div class="uk-inline" style="width: 100%">
		<a id="button-visible-password" class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: lock" uk-tooltip="title: Unlock Password; pos: bottom-right"></a>
		<?php
			echo $this->Form->password('repeatpassword', [
				'class'                  => 'uk-input',
				'placeholder'            => 'Repeat Password',
				'data-parsley-minlength' => '6',
				'data-parsley-maxlength' => '20',
				'uk-tooltip'			 => 'title: Required. Repeat Password. 6-20 chars.; pos: bottom',
				'data-parsley-equalto'   => '#same-password',
				'autocomplete' 			 => 'off',
				'required'               => 'required'
			]);
		?>
	</div>
</div>
<div class="uk-width-1-1 uk-margin-small">
	<?php
		echo $this->Form->text('email', [
			'type'        => 'email',
			'class'       => 'uk-input',
			'placeholder' => 'Email',
			'uk-tooltip'  => 'title: Required; pos: bottom',
			'required'    => 'required'
		]);
	?>
</div>
<div class="uk-width-1-1 uk-margin-small">
    <?php
        $timezoneListing = array();
        foreach ($timezoneList as $list) {
            $timezoneListing[$list] = $list;
        }

        echo $this->Form->select(
            'timezone',
            $timezoneListing,
            [
                'empty' => 'Select Your Timezone',
                'class' => 'uk-select'
            ]
        );
    ?>
</div>
<div class="uk-width-1-1 uk-margin-small">
<?php
	echo $this->Form->button('Next', [
		'id'    => 'button-sitename-admin-setup',
		'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn'
	]);
?>
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
				$(this).attr('uk-icon', 'icon: unlock');
				$(this).attr('uk-tooltip', 'title: Lock Password; pos: bottom-right');
				$('input[name=password]').attr('type', 'text');
				$('input[name=repeatpassword]').attr('type', 'text');
			}

            function visiblePassword2() {
				$(this).one("click", visiblePassword1);
				$(this).attr('uk-icon', 'icon: lock');
				$(this).attr('uk-tooltip', 'title: Unlock Password; pos: bottom-right');
				$('input[name=password]').attr('type', 'password');
				$('input[name=repeatpassword]').attr('type', 'password');
            }
            $("#button-visible-password").one("click", visiblePassword1);
        };

        visiblePassword();

		$('#same-password').on('password.score', (e, score) => {
			$('#password-score').val(score);
		})

		var administrativeSetup;
    	var countClick = 0;
		$("#form-sitename-admin-setup").submit(function(event){
            if ($(this).parsley().isValid()) {
				var passwordScore = $('#password-score').val();
				var usernameValue = $('input[name=username]').val();
				var passwordValue = $('input[name=password]').val();
				if (passwordScore < 50 || usernameValue == passwordValue) {
					alert('Please use stronger password. Do not use your username as password or weak password like admin, administrator, or admin123456.');
					event.preventDefault();
				}
				else {
					countClick++;
					if (administrativeSetup) {
						administrativeSetup.abort();
					}
					var $form   = $(this);
					var $inputs = $(this).find("button, input, select, textarea");
					var $button = $(this).find("button[type=submit]");
					var serializedData = $form.serialize();
					administrativeSetup = $.ajax({
						url: $form.attr('action'),
						type: "post",
						beforeSend: function(){
							$inputs.prop("disabled", true);
							$button.html('<i class="fa fa-spinner fa-pulse"></i> Processing...');
							$button.attr('disabled','disabled');},
						data: serializedData
					});
					administrativeSetup.done(function (msg){
						console.log(msg);
						var json   = $.parseJSON(msg);
						var status = (json.status);
						if(status == 'error') {
							var error = (json.error);
							var error_type = (json.error_type);
						}

						if(status == 'ok') {
							$button.html('<i class="fa fa-spinner fa-pulse"></i> Finishing Setup...');
							setTimeout(function() {
								window.location = "<?= $this->Url->build(['controller' => 'Setup', 'action' => 'finish']) ?>";
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
							$button.html('Next');
							$("#modal-message").modal('show');
							// $('#error-message').html('Error. Can\'t get you to the next step. Please try again.');
							alert('Error. Can\'t get you to the next step. Please try again.');
						}
					});
					administrativeSetup.fail(function(jqXHR, textStatus) {
						$button.removeAttr('disabled');
						$button.html('Next');
						$("#modal-message").modal('show');
						// $('#error-message').html('Error. '+textStatus);
						alert('Error. '+textStatus);
					});
					administrativeSetup.always(function () {
						$inputs.prop("disabled", false);
					});
					event.preventDefault();
				}
			}
		});
	});
</script>