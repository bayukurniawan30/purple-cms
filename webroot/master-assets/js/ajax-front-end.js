$(document).ready(function() {
	ajaxSubmit = function(form, action, redirectType, redirect, customButtonNormal, customButtonLoading, developerMode = false, debug = false) {
        var ajaxFormSubmit,
            $ajaxForm     = $("#"+form),
            $ajaxButton   = $ajaxForm.find('button[type=submit]'),
            customErrCont = $ajaxButton.attr('data-purple-error-container'),
            countClick    = 0;

		if(action == 'contact') {
			if(customButtonLoading == false)
				var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Sending Message...';
			else
				ajaxButtonLoadingState     = customButtonLoading;

			if(customButtonNormal == false)
				var ajaxButtonNormalState  = $ajaxButton.html();
			else
				ajaxButtonNormalState      = customButtonNormal;

			var errText = 'message';
		}
        else if(action == 'comment') {
			if(customButtonLoading == false)
				var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Sending Comment...';
			else
				ajaxButtonLoadingState = customButtonLoading;

			if(customButtonNormal == false)
				var ajaxButtonNormalState  = $ajaxButton.html();
			else
				ajaxButtonNormalState  = customButtonNormal;
			
			var errText = 'comment';
		}
        else if(action == 'notify') {
            if(customButtonLoading == false)
                var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Saving Email...';
            else
                ajaxButtonLoadingState = customButtonLoading;

            if(customButtonNormal == false)
                var ajaxButtonNormalState  = $ajaxButton.html();
            else
                ajaxButtonNormalState  = customButtonNormal;
            
            var errText = 'email';
        }
        else {
            var ajaxButtonLoadingState = customButtonLoading;
            var ajaxButtonNormalState  = customButtonNormal;
        }

        $ajaxForm.submit(function(event){
            if ($ajaxForm.parsley().isValid()) {
                countClick++;
                if (ajaxFormSubmit) {
                    ajaxFormSubmit.abort();
                }
                var $inputs        = $ajaxForm.find("input, button"),
                    $error         = $ajaxForm.find("#form-error-alert");
                if (customErrCont) {
                    var errorContainer = $(customErrCont);
                }
                else {
                    var errorContainer = $('#error-result');
                }

                if (action == 'contact') {
                    var serializedData = $ajaxForm.serialize() + "&token=" + $ajaxForm.find('input[name=token]').val();
                }
                else {
                    var serializedData = $ajaxForm.serialize();
                }

                ajaxFormSubmit = $.ajax({
                    url: $ajaxForm.attr('action'),
                    type: "POST",
                    beforeSend: function() {
                        $inputs.prop("disabled", true);
                        $ajaxButton.html(ajaxButtonLoadingState);
                        $ajaxButton.attr('disabled','disabled');
                        errorContainer.html('');
                    },
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    headers: {
                        'X-CSRF-TOKEN': $ajaxForm.find('input[name=token]').val()
                    }
                });
                ajaxFormSubmit.done(function (msg){
			    	if (debug == true || cakeDebug == 'on') {
                        console.log(msg);
                    }

					var json     = $.parseJSON(msg),
				        status   = (json.status),
                        content  = (json.content),
                        templateOpen  = '<div class="alert alert-success" role="alert">',
                        templateClose = '</div>';

					if (status == 'error') {
						var error      = (json.error);
					}

			        if (status == 'ok') {
                        if(redirectType == 'redirect') {
                            setTimeout(function() {
                                window.location=redirect;
                            }, 1500);
                        }
                        else {
                            if (action == 'notify') {
                                $(redirect).html("Thank's for providing your email. We'll send an email notification if our website is online.")
                            }
                            else {
                                $(redirect).html(templateOpen + content + templateClose);
                            }
                            $inputs.prop("disabled", false);
                            $ajaxForm.find('input[type=text]').val('').removeAttr('uk-tooltip');
                            $ajaxForm.find('input[type=email]').val('').removeAttr('uk-tooltip');
                            $ajaxForm.find('textarea').val('').removeAttr('uk-tooltip');
                            $ajaxForm.find('select').val('').removeAttr('uk-tooltip');
                            $ajaxButton.removeAttr('disabled');
                            $ajaxButton.html(ajaxButtonNormalState);
                        }
			        }
			        else if(status == 'error') {
	                    $inputs.prop("disabled", false);
			        	$ajaxButton.removeAttr('disabled');
						$ajaxButton.html(ajaxButtonNormalState);
						if($.isArray(error)) {
							var join = error.join('. ');
                            if (action == 'notify') {
                                errorContainer.html("Failed! " + join);
                            }
                            else {
    							errorContainer.html('<div class="alert alert-danger" role="alert"><strong>Failed!</strong> ' + join + '</div>');
                            }
                        }
						else {
                            if (action == 'notify') {
                                errorContainer.html("Error! Please fill the form with the correct value");
                            }
                            else {
    							errorContainer.html('<div class="alert alert-danger" role="alert"><strong>Error!</strong> ' + error + '</div>');
                            }
						}
			        }
			        else {
			            $inputs.prop("disabled", false);
			        	$ajaxButton.removeAttr('disabled');
						$ajaxButton.html(ajaxButtonNormalState);
                        if (action == 'notify') {
                            errorContainer.html('Error! For some reason, your ' + errText + ' can\'t be sent. Please try again');
                        }
                        else {    
                            errorContainer.html('<div class="alert alert-danger" role="alert"><strong>Error!</strong> For some reason, your ' + errText + ' can\'t be sent. Please try again.</div>');
			            }
                    }
			    });
			    ajaxFormSubmit.fail(function(jqXHR, textStatus) {
                    $inputs.prop("disabled", false);
                    $ajaxButton.removeAttr('disabled');
                    $ajaxButton.html(ajaxButtonNormalState);
                    if (action == 'notify') {
                        errorContainer.html('Error! Please refresh the page and try again.');
                    }
                    else {
                        errorContainer.html('<div class="alert alert-danger" role="alert"><strong>Error!</strong> Please refresh the page and try again.</div>');
                    }
                	
                });
                ajaxFormSubmit.always(function () {
                    $inputs.prop("disabled", false);
                });
                event.preventDefault();
            }
        });
    }
})