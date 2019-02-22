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
                    <div class="pwstrength-viewport-progress"></div>
                </div>
                <div class="form-group">
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

<script type="text/javascript">
    $(document).ready(function() {
        $('#same-password').password({
            shortPass: 'Are you sure? Don\'t tell me you choose that password ',
            badPass: 'Weak, try combining letters & numbers',
            goodPass: 'Good, try using special charecters',
            strongPass: 'Yeeaah, strong password',
            containsUsername: 'The password contains the username',
            showPercent: false,
            showText: true, // shows the text tips
            animate: true, // whether or not to animate the progress bar on input blur/focus
            animateSpeed: 'fast', // the above animation speed
            username: false, // select the username field (selector or jQuery instance) for better password checks
            usernamePartialMatch: true, // whether to check for username partials
            minimumLength: 6 // minimum password length (below this threshold, the score is 0)
        });

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
            ajaxSubmit(userChangePassword.form, userChangePassword.action, userChangePassword.redirectType, userChangePassword.redirect, userChangePassword.btnNormal, userChangePassword.btnLoading);
        })
    })
</script>