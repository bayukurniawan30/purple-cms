<?php
	echo $this->Form->create($adminAuthyToken, [
		'id'                    => 'form-code-admin', 
		'class'                 => '', 
		'data-parsley-validate' => '',
		'url' 					=> ['_name' => 'adminAjaxVerifyAuthyToken']
    ]);
    
    echo $this->Form->hidden('id', ['value' => $id]);
?>
<p class="text-center" style="margin-top: 20px">Open Authy app to get the code.</p>
<div class="form-group">
	<?php
		echo $this->Form->text('token', [
			'class'                  => 'form-control input-lg text-center', 
			'placeholder'            => 'Verification Code',
			'data-parsley-type'      => 'integer',
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

<script>
	$(document).ready(function() {
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