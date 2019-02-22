<?php
	echo $this->Form->create($setupDatabase, [
		'id'                    => 'form-database-setup',
		'class'                 => 'pt-3',
		'data-parsley-validate' => '',
		'url' 					=> ['action' => 'ajax-database']
	]);
?>
<div class="form-group">
	<?php
		echo $this->Form->text('name', [
			'class'                  => 'form-control input-lg',
			'placeholder'            => 'Database Name',
			'data-parsley-maxlength' => '30',
			'uk-tooltip'			 => 'title: Required. Alpha Numeric. Max 30 chars.; pos: bottom',
            'autofocus'              => 'autofocus',
			'required'               => 'required'
		]);
	?>
</div>
<div class="form-group">
	<?php
		echo $this->Form->text('username', [
			'class'        => 'form-control input-lg',
			'placeholder'  => 'Username',
			'uk-tooltip'   => 'title: Required.; pos: bottom',
			'autocomplete' => 'username',
			'required'     => 'required'
		]);
	?>
</div>
<div class="form-group">
	<?php
		echo $this->Form->password('password', [
			'class'        => 'form-control input-lg',
			'placeholder'  => 'Password',
			'uk-tooltip'   => 'title: Optional. Leave empty if your database not requiring password.; pos: bottom',
			'autocomplete' => 'current-password'
		]);
	?>
</div>

<?php
	echo $this->Form->button('Next', [
		'id'    => 'button-database-setup',
		'class' => 'btn btn-gradient-primary btn-block btn-lg font-weight-medium auth-form-btn'
	]);
?>
<?php
	echo $this->Form->end();
?>

<script>
	$(document).ready(function() {
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
                        $button.html('<i class="fa fa-check"></i> Database is OK');
			         	window.location = "<?= $this->Url->build(['controller' => 'Setup', 'action' => 'administrative']) ?>";
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
			    databaseSetup.fail(function(jqXHR, textStatus) {
                    $button.removeAttr('disabled');
					$button.html('Next');
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