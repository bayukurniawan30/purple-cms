<?php
	echo $this->Form->create($setupDatabase, [
		'id'                    => 'form-database-setup',
		'class'                 => 'uk-grid pt-3',
		'data-parsley-validate' => '',
		'url' 					=> ['action' => 'ajax-database-migration']
	]);
?>
<div class="uk-width-1-1 uk-margin-small">
	<?php
		echo $this->Form->text('name', [
			'class'                  => 'uk-input',
			'placeholder'            => 'Database Name',
			'data-parsley-maxlength' => '30',
			'uk-tooltip'			 => 'title: Required. Alpha Numeric. Max 30 chars.; pos: bottom',
            'autofocus'              => 'autofocus',
			'required'               => 'required'
		]);
	?>
</div>
<div class="uk-width-1-1 uk-margin-small">
	<?php
		echo $this->Form->text('username', [
			'class'        => 'uk-input',
			'placeholder'  => 'Username',
			'uk-tooltip'   => 'title: Required.; pos: bottom',
			'autocomplete' => 'username',
			'required'     => 'required'
		]);
	?>
</div>
<div class="uk-width-1-1 uk-margin-small">
	<div class="uk-inline" style="width: 100%">
		<a id="button-visible-password" class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: lock" uk-tooltip="title: Unlock Password; pos: bottom-right"></a>
		<?php
			echo $this->Form->password('password', [
				'class'        => 'uk-input',
				'placeholder'  => 'Password',
				'uk-tooltip'   => 'title: Optional. Leave empty if your database not requiring password.; pos: bottom-left',
				'autocomplete' => 'current-password'
			]);
		?>
	</div>
</div>
<div class="uk-width-1-1 uk-margin-small">
<?php
	echo $this->Form->button('Change Database', [
		'id'    => 'button-database-setup',
		'class' => 'btn btn-gradient-primary btn-block btn-lg font-weight-medium auth-form-btn'
	]);
?>
</div>
<?php
	echo $this->Form->end();
?>

<script>
	$(document).ready(function() {
		function visiblePassword() {
            function visiblePassword1() {
				$(this).one("click", visiblePassword2);
				$(this).attr('uk-icon', 'icon: unlock');
				$(this).attr('uk-tooltip', 'title: Lock Password; pos: bottom-right');
				$('input[name=password]').attr('type', 'text');
			}

            function visiblePassword2() {
				$(this).one("click", visiblePassword1);
				$(this).attr('uk-icon', 'icon: lock');
				$(this).attr('uk-tooltip', 'title: Unlock Password; pos: bottom-right');
				$('input[name=password]').attr('type', 'password');
            }
            $("#button-visible-password").one("click", visiblePassword1);
        };

		visiblePassword();
		
		var databaseSetup;
    	var countClick = 0;
		$("#form-database-setup").submit(function(event){
            if ($(this).parsley().isValid()) {
            	countClick++;
			    if (databaseSetup) {
			        databaseSetup.abort();
			    }
				var $form   = $(this);
				var $inputs = $(this).find("button, input, select, textarea");
				var $button = $(this).find("button[type=submit]");
			    var serializedData = $form.serialize();
			    databaseSetup = $.ajax({
			        url: $form.attr('action'),
			        type: "post",
			        beforeSend: function(){
			        	$inputs.prop("disabled", true);
			        	$button.html('<i class="fa fa-spinner fa-pulse"></i> Checking Database...');
			        	$button.attr('disabled','disabled');},
			        data: serializedData
			    });
			    databaseSetup.done(function (msg){
			    	console.log(msg);
					var json   = $.parseJSON(msg);
					var status = (json.status);
					if(status == 'error') {
						var error = (json.error);
						var error_type = (json.error_type);
					}

			        if(status == 'ok') {
                        $button.html('<i class="fa fa-check"></i> Database Changed');
			         	window.location = "<?= $this->Url->build(['_name' => 'adminLogin']) ?>";
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
						$button.html('Change Database');
						$("#modal-message").modal('show');
						// $('#error-message').html('Error. Can\'t get you to the next step. Please try again.');
						alert('Error. Can\'t get you to the next step. Please try again.');
			        }
			    });
			    databaseSetup.fail(function(jqXHR, textStatus) {
                    $button.removeAttr('disabled');
					$button.html('Change Database');
					$("#modal-message").modal('show');
					// $('#error-message').html('Error. '+textStatus);
					alert('Error. '+textStatus);
                });
                databaseSetup.always(function () {
                    $inputs.prop("disabled", false);
                });
			    event.preventDefault();
			}
		});
	});
</script>