<div id="modal-check-code" class="uk-flex-top purple-modal" uk-modal="bg-close: false">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($form, [
                'id'                    => 'form-check-code',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url' 					=> ['action' => $formAction]
            ]);

            echo $this->Form->hidden('phone');
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Verification Code</h3>
        </div>
        <div class="uk-modal-body">
            <p>A verification code has been sent to your phone. Please enter the code in the form below.</p>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="form-group uk-margin-remove-bottom">
                        <?php
                            echo $this->Form->text('code', [
                                'id'                     => 'ver-code',
                                'class'                  => 'form-control form-control-lg text-center', 
                                'placeholder'            => '6 Digits Verification Code',
                                'data-parsley-type'      => 'number',
                                'data-parsley-minlength' => '6',
                                'data-parsley-maxlength' => '6',
                                'maxlength'              => '6',
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
                'id'       => 'button-check-code',
                'class'    => 'btn btn-gradient-primary',
                'disabled' => 'disabled'
            ]);

            echo $this->Form->button('Resend Code', [
                'id'              => 'button-resend-code',
                'class'           => 'btn btn-outline-primary uk-margin-left',
                'type'            => 'button',
                'disabled'        => 'disabled',
                'data-purple-url' => $verifyUrl
            ]);
        ?>
        </div>
    </div>
    <?php
        echo $this->Form->end();
    ?>
</div>

<script>
    $(document).ready(function() {
        $('#ver-code').on('keyup', function() {
            var length = this.value.length;
            if (length == 6) {
                $('#button-check-code').removeAttr('disabled');
            }
            else {
                $('#button-check-code').attr('disabled', 'disabled');
            }
        });

        $('#form-check-code').submit(function(event){
            var formSubmit,
                form       = $('#form-check-code'),
                btn        = $('#button-check-code'),
                btnNormal  = btn.html(),
                btnLoading = '<i class="fa fa-circle-o-notch fa-spin"></i> Verifying code...';

            if (form.parsley().isValid()) {
                var inputs         = form.find("input, textarea, button"),
                    serializedData = form.serialize(),
                    formData       = new FormData(this);

                formSubmit = $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    beforeSend: function() {
                        inputs.prop("disabled", true);
                        btn.html(btnLoading);
                        btn.attr('disabled','disabled');

                        // Progress bar
                        progress.start();
                    },
                    data: serializedData,
                    // contentType: false,
                    cache: false,
                    // processData:false,
                });
                formSubmit.done(function (msg){
                    // Stop progress bar
                    progress.end();

			    	if (cakeDebug == 'on') {
                        console.log(msg);
                    }

                    var json   = $.parseJSON(msg),
                        status = json.status
                    if (status == 'error') {
                        var error = json.error;
                    }

                    if (status == 'ok') {
                        btn.html('<i class="fa fa-check"></i> Verified');
                        setTimeout(() => {
                            UIkit.modal('#modal-check-code').hide();
                            $('#modal-check-code').find('input').val('');
                            $('#modal-check-code').find('button').attr('disabled', 'disabled');
                            $('#button-update-user').removeAttr('disabled');
                            $('#button-verify-phone').html('Phone number has been verified');
                            $('#button-verify-phone').off('click');
                        }, 2000);
                    }
                    else {
                        btn.removeAttr('disabled');
                        btn.html(btnNormal);
                        var createToast = notifToast('Error', error, 'error', true);
                    }
                });
                formSubmit.fail(function(jqXHR, textStatus) {
                    inputs.prop("disabled", false);
                    btn.removeAttr('disabled');
                    btn.html(btnNormal);
                    var createToast = notifToast(jqXHR.statusText, 'Error. Please refresh the page and try again.', 'error', true);
                });
                formSubmit.always(function () {
                    inputs.prop("disabled", false);
                });
                event.preventDefault();
            }
        })

        $('#button-resend-code').on('click', function() {
            var btn    = $(this),
                phone  = '+' + $('#calling-code').val() + $('#phone-number').val(),
                data   = { phone:phone },
				url    = btn.data('purple-url');
				token  = $('#csrf-ajax-token').val();

			var ajaxProcessing = $.ajax({
				type: "POST",
				url:  url,
				headers : {
					'X-CSRF-Token': token
				},
				data: data,
                cache: false,
                beforeSend: function() {
                    $('button').prop("disabled", true);
                    btn.html('<i class="fa fa-circle-o-notch fa-spin"></i> Sending code...');

                    // Progress bar
                    progress.start();
                }
			});
			ajaxProcessing.done(function(msg) {
                // Stop progress bar
                progress.end();

				if (cakeDebug == 'on') {
					console.log(msg);
                }
                
                var json   = $.parseJSON(msg) 
                    status = json.status;

                if (status == 'ok') {
                    btn.hide();

                    var phone    = json.phone;
                    $('#modal-check-code').find('input[name=phone]').val(phone);
                }
                else {
                    var createToast = notifToast('Error', json.error, 'error', true);
                }
			});
			ajaxProcessing.fail(function(jqXHR, textStatus) {
				var createToast = notifToast(jqXHR.statusText, 'There is an error when sending verification code. Please refresh the page', 'error', true);
			});
			ajaxProcessing.always(function () {
                $('button').prop("disabled", false);
            });
            
            return false;
        })
    })
</script>