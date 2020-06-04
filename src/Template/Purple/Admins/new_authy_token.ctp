<div class="row">
    <div class="col-md-12">
        <div class="uk-alert-primary" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p>Enter Authy token to the form and click Verify.</p>
        </div>
    </div>
</div>
<div id="modal-authy-token" class="uk-flex-top purple-modal" uk-height-viewport uk-modal="bg-close: false">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($adminAuthyToken, [
                'id'                    => 'form-authy-token',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url' 					=> ['action' => 'ajax-verify-authy-token']
            ]);

            echo $this->Form->hidden('id', ['value' => $adminData->id]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Verification Code</h3>
        </div>
        <div class="uk-modal-body uk-flex-middle">
            <p class="uk-text-center">A verification code has been sent to your phone. If you have <a href="https://authy.com/" class="uk-text-bold" target="_blank">Authy</a> installed in your phone, you have to check the Authy app instead of SMS. Please enter the code in the form below.</p>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="form-group uk-margin-remove-bottom">
                        <?php
                            echo $this->Form->text('token', [
                                'id'                     => 'ver-code',
                                'class'                  => 'form-control form-control-lg text-center', 
                                'placeholder'            => 'Enter Verification Code',
                                'data-parsley-type'      => 'number',
                                'required'               => 'required',
                                'autofocus'              => true,
                                'style'                  => 'font-size: 16px'
                            ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Verify', [
                'id'       => 'button-authy-token',
                'class'    => 'btn btn-gradient-primary',
                'disabled' => 'disabled'
            ]);

            // echo $this->Form->button('Resend Code', [
            //     'id'              => 'button-resend-code',
            //     'class'           => 'btn btn-outline-primary uk-margin-left',
            //     'type'            => 'button',
            //     'disabled'        => 'disabled',
            //     'data-purple-url' => ''
            // ]);
        ?>
        </div>
    </div>
    <?php
        echo $this->Form->end();
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        UIkit.modal('#modal-authy-token').show();

        $('#ver-code').on('keyup', function() {
            var length = this.value.length;
            if (length == 7) {
                $('#button-authy-token').removeAttr('disabled');
            }
            else {
                $('#button-authy-token').attr('disabled', 'disabled');
            }
        });

        var userVerify = {
            form            : 'form-authy-token',
            button          : 'button-authy-token',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'index']); ?>',
            btnNormal       : false,
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Verifying...'
        };

        var targetButton = $("#"+userVerify.button);
        targetButton.one('click',function() {
            ajaxSubmit(userVerify.form, userVerify.action, userVerify.redirectType, userVerify.redirect, userVerify.btnNormal, userVerify.btnLoading);
        })
    })
</script>