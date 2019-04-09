<style>
	.pass-wrapper {
		width: 100%;
		position: absolute;
		bottom: 0;
		display: none;
	}
</style>

<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">User Password</h4>
            </div>
            <?php
                echo $this->Form->create($adminEditPassword, [
                    'id'                    => 'form-update-password-user',
                    'class'                 => 'pt-3',
                    'data-parsley-validate' => '',
                    'url'                   => ['action' => 'ajax-update-password']
                ]);

                echo $this->Form->hidden('id', ['value' => $adminData->id]);
                echo $this->Form->hidden('username', ['value' => $adminData->username]);
                echo $this->Form->hidden('email', ['value' => $adminData->email]);
                echo $this->Form->hidden('passwordscore', ['value' => '0', 'id' => 'password-score']);
            ?>
            <div class="card-body">
                <div class="form-group">
                    <div class="input-group">
                        <?php
                            echo $this->Form->password('password', [
                                'id'                     => 'same-password',
                                'class'                  => 'form-control',
                                'placeholder'            => 'Password',
                                'data-parsley-minlength' => '6',
                                'data-parsley-maxlength' => '20',
                                'uk-tooltip'             => 'title: Rquired. 6-20 chars.; pos: bottom',
                                'autocomplete'           => '',
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
                                'class'                  => 'form-control',
                                'placeholder'            => 'Repeat Password',
                                'data-parsley-minlength' => '6',
                                'data-parsley-maxlength' => '20',
                                'uk-tooltip'             => 'title: Required. Repeat Password. 6-20 chars.; pos: bottom',
                                'data-parsley-equalto'   => '#same-password',
                                'required'               => 'required'
                            ]);
                        ?>
                        <div class="input-group-append">
                            <button id="button-visible-password" class="btn btn-gradient-success btn-sm" type="button" uk-tooltip="title: Toggle visible password; pos: bottom"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                </div>
             </div>
             <div class="card-footer">
                <?php
                    echo $this->Form->button('Save', [
                        'id'    => 'button-update-password-user',
                        'class' => 'btn btn-gradient-primary'
                    ]);

                    echo $this->Form->button('Cancel', [
                        'id'           => 'button-close-modal',
                        'class'        => 'btn btn-outline-primary uk-margin-left',
                        'type'         => 'button'
                    ]);
                ?>
             </div>
             <?php
                echo $this->Form->end();
            ?>
        </div>
    </div>
</div>

<?= $this->Html->script('/master-assets/plugins/password/password-generator.js'); ?>
<script type="text/javascript">
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

        var userChangePassword = {
            form            : 'form-update-password-user',
            button          : 'button-update-password-user',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'index']); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+userChangePassword.button);
        targetButton.one('click',function() {
            var passwordScore = $('#password-score').val();
            if (passwordScore < 50) {
                alert('Please use stronger password. Do not use your username as password or weak password like admin, administrator, or admin123456.');
                event.preventDefault();
            }
            else {
                ajaxSubmit(userChangePassword.form, userChangePassword.action, userChangePassword.redirectType, userChangePassword.redirect, userChangePassword.btnNormal, userChangePassword.btnLoading);
            }
        })
    })
</script>