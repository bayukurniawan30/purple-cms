$(document).ready(function() {
    ajaxSubmit = function(form, action, redirectType, redirect, customButtonNormal, customButtonLoading, developerMode = false, debug = false, toast = true) {
        var ajaxFormSubmit,
            $ajaxForm     = $("#"+form),
            $ajaxButton   = $ajaxForm.find('button[type=submit]'),
            customErrCont = $ajaxButton.attr('data-purple-error-container'),
            countClick    = 0;

		if (action == 'add' || action == 'edit' || action == 'update' || action == 'ajax-load-post-categories') {
			if (customButtonLoading == false)
				var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Saving...';
			else
				ajaxButtonLoadingState     = customButtonLoading;

			if (customButtonNormal == false)
				var ajaxButtonNormalState  = $ajaxButton.html();
			else
				ajaxButtonNormalState      = customButtonNormal;
		}
        else if (action == 'delete') {
			if (customButtonLoading == false)
				var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Deleting...';
			else
				ajaxButtonLoadingState = customButtonLoading;

			if (customButtonNormal == false)
				var ajaxButtonNormalState  = $ajaxButton.html();
			else
				ajaxButtonNormalState  = customButtonNormal;
		}
        else if (action == 'login') {
			if (customButtonLoading == false)
				var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Processing...';
			else
				ajaxButtonLoadingState = customButtonLoading;

			if (customButtonNormal == false)
				var ajaxButtonNormalState  = $ajaxButton.html();
			else
				ajaxButtonNormalState  = customButtonNormal;
		}
        else if (action == 'confirm-email') {
            if (customButtonLoading == false)
                var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Checking...';
            else
                ajaxButtonLoadingState = customButtonLoading;

            if (customButtonNormal == false)
                var ajaxButtonNormalState  = $ajaxButton.html();
            else
                ajaxButtonNormalState  = customButtonNormal;
        }
        else if(action == 'apply-theme') {
            if (customButtonLoading == false)
                var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Applying Theme...';
            else
                ajaxButtonLoadingState = customButtonLoading;

            if (customButtonNormal == false)
                var ajaxButtonNormalState  = $ajaxButton.html();
            else
                ajaxButtonNormalState  = customButtonNormal;
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
                var $inputs        = $ajaxForm.find("input, textarea, button"),
                    serializedData = $ajaxForm.serialize(),
                    errorTemplate  = '<div class="alert alert-danger" role="alert"></div>';
                if (action == 'confirm-email') {
                    var $error = $ajaxForm.find("#form-confirm-error-alert");
                }
                else {
                    var $error = $ajaxForm.find("#form-error-alert");
                }
                ajaxFormSubmit = $.ajax({
                    url: $ajaxForm.attr('action'),
                    type: "POST",
                    beforeSend: function() {
                        $inputs.prop("disabled", true);
                        $ajaxButton.html(ajaxButtonLoadingState);
                        $ajaxButton.attr('disabled','disabled');
                    },
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                });
                ajaxFormSubmit.done(function (msg){
			    	if (debug == true || cakeDebug == 'on') {
                        console.log(msg);
                    }

                    if (action == 'dashboard-statistic') {
                        $("#dashboard-statistic-container").addClass('col-md-9');
                        $("#dashboard-statistic-container").removeClass('col-md-7');
                        $("#recent-activity-container").hide();
                        $(redirect).html(msg);
                        $inputs.prop("disabled", false);
                        $ajaxButton.removeAttr('disabled');
                        $ajaxButton.html(ajaxButtonNormalState);
                        UIkit.modal("#modal-select-date-range").hide();
                    }
                    else {
    					var json    = $.parseJSON(msg),
    				        status  = (json.status),
                            content = (json.content);

    					if(status == 'error') {
    						var error = (json.error);
    					}

    			        if (status == 'ok') {
                            if (action == 'login') {
                                $error.html('');
                                $ajaxButton.html('<i class="fa fa-circle-o-notch fa-spin"></i> Signing you in...');
                            }
                            else if (action == 'confirm-email') {
                                $error.html(content);
                                setTimeout(function() {
                                    $("#modal-confirm-email").modal('hide');
                                    $error.html('');
                                    $ajaxForm.find('input').val('');
                                }, 3000);
                            }
                            else if (action == 'ajax-load-post-categories') {
                                var createToast = notifToast('Form Submiting', 'Success add data', 'success', true, 1500);
                                var afterSubmit = $('#button-add-post-category').attr('data-purple-action'),
                                    loadTarget  = $('#button-add-post-category').attr('data-purple-target'),
                                    loadUrl     = $('#button-add-post-category').attr('data-purple-loadurl'),
                                    pageId      = $('#modal-add-post-category').find('input[name=page_id]').val(),
                                    newId       = json.id,
                                    token       = $("input[name=_csrfToken]").val();

                                $inputs.prop("disabled", false);
                                $ajaxButton.removeAttr('disabled');
                                $ajaxButton.html(ajaxButtonNormalState);

                                if (afterSubmit == 'load') {
                                    $.ajax({
                                        type: "POST",
                                        url:  loadUrl,
                                        headers : {
                                            'X-CSRF-Token': token
                                        },
                                        data: {page: pageId},
                                        cache: false,
                                        beforeSend: function() {
                                        },
                                        success: function(data) {
                                            $('#load-blog-categories').html(data);
                                            setTimeout(function() {
                                                $('#load-blog-categories').find('select[name=blog_category_id] option[value="' + newId + '"]').attr("selected","selected");
                                            }, 500);
                                        }
                                    })
                                    UIkit.modal("#modal-add-post-category").hide();
                                }
                            }
                            else {
                                if (toast == true) {
                                    if (action == 'apply-theme') {
                                        var createToast = notifToast('Form Submiting', 'Success applying theme', 'success', true, 1500);
                                    }
                                    else {
                                        var createToast = notifToast('Form Submiting', 'Success ' + action + ' data', 'success', true, 1500);
                                    }
                                }
                            }

                            if (redirectType == 'redirect') {
                                setTimeout(function() {
                                    window.location=redirect;
                                }, 1500);
                            }
                            else {
                                $(redirect).html(content);
                                $inputs.prop("disabled", false);
                                $ajaxButton.removeAttr('disabled');
                                $ajaxButton.html(ajaxButtonNormalState);
                            }

    			        }
    			        else if (status == 'error') {
    	                    $inputs.prop("disabled", false);
    			        	$ajaxButton.removeAttr('disabled');
    						$ajaxButton.html(ajaxButtonNormalState);
    						if($.isArray(error)) {
    							var join = error.join('. ');
                                if (action == 'login' || action == 'confirm-email') {
                                    $error.html(errorTemplate);
                                    $error.find('.alert').append(join);
                                }
                                else {
                                    var createToast = notifToast('Form Submiting', join, 'error', true);
                                }
                            }
                            else if (typeof error === 'object') {
                                $.each (error, function (index) {
                                    var validation = error[index];
                                    var validationText = '';
                                    $.each (validation, function (indexValidation) {
                                        validationText += validation[indexValidation]+". ";
                                    })

                                    var createToast = notifToast('Form Submiting', validationText, 'error', true);
                                });
                            }
    						else {
                                if (action == 'login' || action == 'confirm-email') {
                                    $error.html(errorTemplate);
                                    $error.find('.alert').append(error);
                                }
                                else {
                                    var createToast = notifToast('Form Submiting', error, 'error', true);
                                }
    						}
    			        }
    			        else {
    			            $inputs.prop("disabled", false);
    			        	$ajaxButton.removeAttr('disabled');
    						$ajaxButton.html(ajaxButtonNormalState);
                            if (action == 'login' || action == 'confirm-email') {
                                $error.html(errorTemplate);
                                $error.find('.alert').append('Error. Can\'t let you in. Please try again.');
                            }
                            else {
                                var createToast = notifToast('Form Submiting', 'There is an error with Purple. Please try again', 'error', true);
                            }
    			        }
                    }
			    });
			    ajaxFormSubmit.fail(function(jqXHR, textStatus) {
                    $inputs.prop("disabled", false);
                    $ajaxButton.removeAttr('disabled');
                    $ajaxButton.html(ajaxButtonNormalState);
                    if (action == 'login' || action == 'confirm-email') {
                        $error.html(errorTemplate);
                        $error.find('.alert').append('Error. Please refresh the page and try again.');
                    }
                    else {
                        var createToast = notifToast('Form Submiting', 'Error. Please refresh the page and try again.', 'error', true);
                    }
                });
                ajaxFormSubmit.always(function () {
                    $inputs.prop("disabled", false);
                });
                event.preventDefault();
            }
        });
    }

    ajaxButton = function(button, ajaxType, sendData, action, url, redirectType, redirect, customButtonNormal, customButtonLoading, developerMode = false, debug = false, toast = true) {
        $(button).click(function() {
            var btn      = $(this),
                btnTxt   = btn.html(),
                table    = btn.data('purple-table'),
                id       = btn.data('purple-id'),
                token    = $('#csrf-ajax-token').val();

            if (action == 'add' || action == 'edit' || action == 'update') {
                if (customButtonLoading == false)
                    var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Saving...';
                else
                    ajaxButtonLoadingState     = customButtonLoading;

                if (customButtonNormal == false)
                    var ajaxButtonNormalState  = btnTxt;
                else
                    ajaxButtonNormalState      = customButtonNormal;
            }
            else if (action == 'delete') {
                if (customButtonLoading == false)
                    var ajaxButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Deleting...';
                else
                    ajaxButtonLoadingState = customButtonLoading;

                if (customButtonNormal == false)
                    var ajaxButtonNormalState  = btnTxt;
                else
                    ajaxButtonNormalState  = customButtonNormal;
            }
            else {
                var ajaxButtonNormalState  = customButtonNormal;
                var ajaxButtonNormalState  = customButtonNormal;

            }

            $.ajax({
                type: ajaxType,
                url:  url,
                headers : {
                    'X-CSRF-Token': token
                },
                data: sendData,
                cache: false,
                beforeSend: function(){
                    if (action != 'link') {
                        btn.html(ajaxButtonLoadingState);
                        btn.attr('disabled','disabled');
                    }
                },
                success: function(msg){
                    if (debug == true || cakeDebug == 'on') {
                        console.log(msg);
                    }

					var json    = $.parseJSON(msg),
				        status  = (json.status),
                        content = (json.content);

					if (status == 'error') {
						var error = (json.error);
					}

			        if (status == 'ok') {
                        if (toast == true) {
                            var createToast = notifToast('Awesome Thing', 'Success ' + action + ' data', 'success', true, 1500);
                        }

                        if (redirectType == 'redirect') {
                            setTimeout(function() {
                                window.location=redirect;
                            }, 1500);
                        }
                        else {
                            $(redirect).html(content);
                            if (action != 'link') {
                                btn.removeAttr('disabled');
                                btn.html(ajaxButtonNormalState);
                            }
                        }
                    }
                    else if (status == 'error') {
                        if(action != 'link') {
                            btn.removeAttr('disabled');
                            btn.html(ajaxButtonNormalState);
                        }

                        if ($.isArray(error)) {
							var join = error.join('. ');
                            var createToast = notifToast('Awesome Thing', join, 'error', true);
                        }
                        else {
                            var createToast = notifToast('Awesome Thing', error, 'error', true);
                        }
                    }
                    else {
                        if (action != 'link') {
                            btn.removeAttr('disabled');
                            btn.html(ajaxButtonNormalState);
                        }

                        var createToast = notifToast('Awesome Thing', 'There is an error with Purple. Please try again', 'error', true);
                    }
                }
            })

            return false;
        })
    }
})