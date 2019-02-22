<?php
	// Check HTTP type of the URL
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
		$checkHttp = 'https://';
	}
	else {
		$checkHttp = 'http://';
	}
?>

<?php
	echo $this->Form->create($setupAdministrative, [
		'id'                    => 'form-sitename-admin-setup',
		'class'                 => 'pt-3',
		'data-parsley-validate' => '',
		'url' 					=> ['action' => 'ajax-administrative']
	]);

	echo $this->Form->hidden('ds', ['value' => $this->Url->build(['_name' => 'adminLogin'])]);
	echo $this->Form->hidden('siteurl', ['value' => $checkHttp]);
	echo $this->Form->hidden('foldername', ['value' => $this->Setup->checkSubfolder()]);
	echo $this->Form->hidden('passwordscore', ['value' => '0', 'id' => 'password-score']);
?>
<div class="form-group">
	<?php
		echo $this->Form->text('sitename', [
			'class'                  => 'form-control input-lg',
			'placeholder'            => 'Site Name',
			'data-parsley-minlength' => '1',
			'data-parsley-maxlength' => '30',
            'autofocus'              => 'autofocus',
			'uk-tooltip'			 => 'title: Required. Max 30 chars.; pos: bottom',
			'required'               => 'required'
		]);
	?>
</div>
<div class="form-group">
	<?php
		echo $this->Form->text('username', [
			'class'                  => 'form-control input-lg',
			'placeholder'            => 'Username',
			'data-parsley-type'      => 'alphanum',
			'data-parsley-minlength' => '6',
			'data-parsley-maxlength' => '15',
			'uk-tooltip'			 => 'title: Required. Alpha numeric. 6-15 chars.; pos: bottom',
			'required'               => 'required'
		]);
	?>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<?php
				echo $this->Form->password('password', [
					'id'                     => 'same-password',
					'class'                  => 'form-control input-lg',
					'placeholder'            => 'Password',
					'data-parsley-minlength' => '6',
					'data-parsley-maxlength' => '20',
					'uk-tooltip'			 => 'title: Required. 6-20 chars.; pos: bottom',
					'autocomplete' 			 => 'off',
					'required'               => 'required'
				]);
			?>
			<div class="pwstrength-viewport-progress"></div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<?php
				echo $this->Form->password('repeatpassword', [
					'class'                  => 'form-control input-lg',
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
</div>
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
<div class="form-group">
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
                'class' => 'form-control input-lg'
            ]
        );
    ?>
</div>
<?php
	echo $this->Form->button('Next', [
		'id'    => 'button-sitename-admin-setup',
		'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn'
	]);
?>
<?php
	echo $this->Form->end();
?>

<script>
	$(document).ready(function() {
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

		$('#same-password').on('password.score', (e, score) => {
			$('#password-score').val(score);
		})

		var administrativeSetup;
    	var countClick = 0;
		$("#form-sitename-admin-setup").submit(function(event){
            if ($(this).parsley().isValid()) {
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
		});
	});
</script>