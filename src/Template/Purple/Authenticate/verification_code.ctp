<style>
    #resend-code {
        display: none;
    }
</style>
<?php
	echo $this->Form->create($adminVerificationCode, [
		'id'                    => 'form-code-admin', 
		'class'                 => '', 
		'data-parsley-validate' => '',
		'url' 					=> ['_name' => 'adminAjaxVerifyCode']
	]);
?>
<div class="form-group">
	<?php
		echo $this->Form->text('code', [
			'class'                  => 'form-control input-lg text-center', 
			'placeholder'            => '6 Digits Verification Code',
			'data-parsley-type'      => 'integer',
			'data-parsley-minlength' => '6',
			'data-parsley-maxlength' => '6',
			'required'               => 'required',
			'autofocus'              => true
		]);
	?>
</div>
<div id="form-error-alert" class="form-group"></div>
<?php	
	echo $this->Form->button('Submit Code', [
		'id'    => 'button-code-admin',
		'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn'
	]);
?>
<?php
	echo $this->Form->end();
?>
<p class="forgot-password text-center"><a id="resend-code" data-purple-url="<?= $this->Url->build(['_name' => 'adminAjaxResendSignInVerification']) ?>" href="#">Resend code</a></p>

<script>
	$(document).ready(function() {
        setTimeout(function() {
            $('#resend-code').show();

            $('#resend-code').one('click', function() {
                var btn   = $(this),
                    data  = {action:'resend'},
                    url   = btn.data('purple-url');
                    token = $('#csrf-ajax-token').val();

                var ajaxProcessing = $.ajax({
                    type: "POST",
                    url:  url,
                    headers : {
                        'X-CSRF-Token': token
                    },
                    data: data,
                    cache: false,
                    beforeSend: function() {
                        $('input, button, textarea, select').prop("disabled", true);
                        btn.html('<i class="fa fa-circle-o-notch fa-spin"></i> Sending code...');
                    }
                });
                ajaxProcessing.done(function(msg) {
                    console.log(msg);
                    
                    var json    = $.parseJSON(msg),
                        status  = (json.status);

                    if (status == 'ok') {
                        btn.html('Code has been sent');
                    }
                    else {
                        alert("Can't send verification code.");
                    }
                });
                ajaxProcessing.fail(function(jqXHR, textStatus) {
                    alert("Can't send verification code.");
                });
                ajaxProcessing.always(function () {
                    $('input, button, textarea, select').prop("disabled", false);
                });

                return false
            })
        }, 10000);

        var adminLogin = {
            form            : 'form-code-admin', 
            button          : 'button-code-admin',
            action          : 'login', 
            redirectType    : 'redirect', 
            redirect        : '<?= $this->Url->build(['_name' => 'adminDashboard']) ?>', 
            btnNormal       : false, 
            btnLoading      : false 
        };
        
        var targetButton = $("#"+adminLogin.button);
        targetButton.one('click',function() {
            ajaxSubmit(adminLogin.form, adminLogin.action, adminLogin.redirectType, adminLogin.redirect, adminLogin.btnNormal, adminLogin.btnLoading);
        })
	})
</script>