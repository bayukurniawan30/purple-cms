<style type="text/css">
	#modal-confirm-email .modal-content {
		background-color: #ffffff;
		border-radius: 0;
	}
</style>
<?php
	echo $this->Form->create($adminLogin, [
		'id'                    => 'form-login-admin', 
		'class'                 => '', 
		'data-parsley-validate' => '',
		'url' 					=> ['action' => 'ajax-login']
	]);
?>
<div class="form-group">
	<?php
		echo $this->Form->text('username', [
			'class'                  => 'form-control input-lg', 
			'placeholder'            => 'Username',
			'data-parsley-type'      => 'alphanum',
			'data-parsley-minlength' => '6',
			'data-parsley-maxlength' => '15',
			'required'               => 'required'
		]);
	?>
</div>
<div class="form-group">
	<?php
		echo $this->Form->password('password', [
			'class'                  => 'form-control input-lg', 
			'placeholder'            => 'Password',
			'data-parsley-minlength' => '6',
			'data-parsley-maxlength' => '20',
			'autocomplete' 			 => '',
			'required'               => 'required'
		]);
	?>
</div>
<div id="form-error-alert" class="form-group"></div>
<?php	
	echo $this->Form->button('Sign In', [
		'id'    => 'button-login-admin',
		'class' => 'btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn'
	]);
?>
<?php
	echo $this->Form->end();
?>
<p class="forgot-password text-center"><a href="#modal-confirm-email" data-toggle="modal" data-target="#modal-confirm-email">Ups, I forgot my password</a></p>

<div id="modal-confirm-email" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
    	<div class="modal-content">
	        <?php
	            echo $this->Form->create($forgotPassword, [
	                'id'                    => 'form-confirm-email',
	                'class'                 => '',
	                'data-parsley-validate' => '',
	                'url'                   => ['controller' => 'Authenticate', 'action' => 'ajax-forgot-password']
	            ]);

                echo $this->Form->hidden('ds', ['value' => $this->Url->build(['_name' => 'adminLogin'])]);
	        ?>
	        <div class="modal-header">
	            <h5 class="modal-title">Please Confirm Your Email</h5>
	        </div>
	        <div class="modal-body">
	            <div class="form-group mb-0">
	                <?php
	                    echo $this->Form->text('email', [
							'type'        => 'email',
							'class'       => 'form-control',
							'placeholder' => 'Email',
							'autofocus'   => 'autofocus',
							'required'    => 'required'
	                    ]);
	                ?>
	            </div>
	            <div class="form-group mb-0">
	            	<div id="form-confirm-error-alert"></div>
	            </div>
	        </div>
	        <div class="modal-footer text-right">
	            <?php
	                echo $this->Form->button('Confirm', [
	                    'id'                  => 'button-confirm-email',
	                    'class'               => 'btn btn-gradient-primary'
	                ]);

	                echo $this->Form->button('Cancel', [
	                    'id'           => 'button-close-modal',
	                    'class'        => 'btn btn-outline-primary',
	                    'type'         => 'button',
	                    'data-dismiss' => 'modal'
	                ]);
	            ?>
	        </div>
	        <?php
	            echo $this->Form->end();
	        ?>
	    </div>
    </div>
</div>

<script>
	$(document).ready(function() {
		$("#modal-confirm-email").appendTo("body");

        var adminLogin = {
            form            : 'form-login-admin', 
            button          : 'button-login-admin',
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

        $("#modal-confirm-email").appendTo("body");

        var confirmEmail = {
            form            : 'form-confirm-email', 
            button          : 'button-confirm-email',
            action          : 'confirm-email', 
            redirectType    : 'ajax', 
            redirect        : '#form-confirm-error-alert', 
            btnNormal       : false, 
            btnLoading      : false 
        };
        
        var targetButton = $("#"+confirmEmail.button);
        targetButton.one('click',function() {
            ajaxSubmit(confirmEmail.form, confirmEmail.action, confirmEmail.redirectType, confirmEmail.redirect, confirmEmail.btnNormal, confirmEmail.btnLoading);
        })
	})
</script>