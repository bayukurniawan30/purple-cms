$(document).ready(function() {
    /**
     *
     * Modal
     *
     */

    $(".button-about-purple-cms").on("click", function() {
        var btn       = $(this),
            modal     = btn.data('purple-modal');

        UIkit.modal(modal).show();
        return false;
    })
    
    $(".button-dashboard-date-range").on("click", function() {
        var modal = $(this).data('purple-modal');
        UIkit.modal(modal).show();
        return false;
    })

    $("#button-save-page").on("click", function() {
        var btn             = $(this),
            modal           = $(this).data('purple-modal'),
            pageTitle       = $("#form-input-title").val(),
            metaKeywords    = $("#form-input-metakeywords").val(),
            metaDescription = $("#form-input-metadescription").val();

        if (btn.data('purple-page') == 'custom') {
            var html  = $(btn.data('purple-content')).val();
        }
        else if (btn.data('purple-page') == 'general') {
            var html  = $(btn.data('purple-content')).html();
            var css   = $(btn.data('purple-content')).find('style').html();
        }

        if (pageTitle.length == 0) {
            alert('Page title is required');
        }
        else {
            $(modal).find('input[name=title]').val(pageTitle);
            $(modal).find('input[name=meta_keywords]').val(metaKeywords);
            $(modal).find('input[name=meta_description]').val(metaDescription);
            $(modal).find('input[name=content]').val(html);
            $(modal).find('input[name=css-content]').val(css);

            UIkit.modal(modal).show();
        }
        return false;
    })

    $(".button-change-page-status").on("click", function() {
        var btn         = $(this),
            id          = btn.data('purple-id'),
            name        = btn.data('purple-name'),
            status      = btn.data('purple-status'),
            modal       = btn.data('purple-modal'),
            editModal   = $(modal);

        editModal.find('input[name=id]').val(id);
        editModal.find('select[name=status]').val(status);
        editModal.find('.bind-title').html(name);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-edit-navigation").on("click", function() {
        var btn         = $(this),
            id          = btn.data('purple-id'),
            navtype     = btn.data('purple-navtype'),
            hassub      = btn.data('purple-hassub'),
            status      = btn.data('purple-status'),
            title       = btn.data('purple-title'),
            point       = btn.data('purple-point'),
            target      = btn.data('purple-target'),
            modal       = btn.data('purple-modal'),
            editModal   = $(modal),
            editForm    = editModal.find("form"),
            editID      = editForm.find("input[name=id]"),
            editType    = editForm.find("input[name=navtype]"),
            editTitle   = editForm.find("input[name=title]"),
            editStatus  = editForm.find("select[name=status]"),
            editPoint   = editForm.find("select[name=point]");

        if(point == 'pages') {
            editTarget  = editForm.find("#select-target-form");
            editTarget.val(target);
            editForm.find("input[name=target]").removeAttr('name').removeAttr('required').hide();
            editForm.find("select[name=target]").attr('required','required');
        }
        else if(point == 'customlink') {
            editTarget  = editForm.find("#input-target-form");
            editTarget.val(target);
            editForm.find("select[name=target]").removeAttr('name').removeAttr('required').hide();
            editForm.find("input[name=target]").attr('required','required');
        }

        if(hassub == '0' || navtype == 'submenu') {
            editModal.find('.uk-alert-primary').hide();
        }
        else if(hassub == '1') {
            editModal.find('.uk-alert-primary').show();
        }

        editID.val(id);
        editType.val(navtype);
        editTitle.val(title);
        editStatus.val(status);
        editPoint.val(point);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-edit-page-blog").on("click", function() {
        var btn = $(this),
            modal = btn.data('purple-modal');

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-edit-category").on("click", function() {
        var btn         = $(this),
            id          = btn.data('purple-id'),
            name        = btn.data('purple-name'),
            page        = btn.data('purple-page'),
            modal       = btn.data('purple-modal'),
            editModal   = $(modal);

        editModal.find('input[name=id]').val(id);
        editModal.find('input[name=name]').val(name);
        editModal.find('input[name=page_id]').val(page);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-edit-subscriber").on("click", function() {
        var btn         = $(this),
            id          = btn.data('purple-id'),
            email       = btn.data('purple-email'),
            modal       = btn.data('purple-modal'),
            editModal   = $(modal);

        editModal.find('input[name=id]').val(id);
        editModal.find('input[name=email]').val(email);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-export-subscribers").on("click", function() {
        var btn      = $(this),
            btnHtml  = btn.html(),
            url      = btn.data('purple-url'),
            redirect = btn.data('purple-redirect'),
            token    = $('#csrf-ajax-token').val(),
            data     = { action:'export' };

            $.ajax({
                type: "POST",
                url:  url,
                headers : {
                    'X-CSRF-Token': token
                },
                data: data,
                cache: false,
                beforeSend: function() {
                    btn.html('<i class="mdi mdi-spin mdi-loading btn-icon-prepend"></i> Exporting data...')
                    btn.attr('disabled', 'disabled');
                },
                success: function(data) {
                    // console.log(data);
                    var json    = $.parseJSON(data),
                        status  = (json.status);
                        error   = (json.error);

                    if (status == 'ok') {
                        url = (json.url);
                        btn.removeAttr('disabled', 'disabled');
                        setTimeout(function() {
                            btn.html(btnHtml);
                            window.open(redirect);
                            // window.open(url);
                        }, 2000);
                    }
                    else {
                        btn.html(btnHtml);
                        btn.removeAttr('disabled', 'disabled');
                        alert(error) 
                    }
                }
            })

        return false;
    })

    $(".button-edit-sfooter").on("click", function() {
        var btn         = $(this),
            modal       = btn.data('purple-modal');

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-edit-social").on("click", function() {
        var btn         = $(this),
            id          = btn.data('purple-id'),
            name        = btn.data('purple-name'),
            link        = btn.data('purple-link'),
            modal       = btn.data('purple-modal'),
            editModal   = $(modal),
            editForm    = editModal.find("form"),
            editID      = editForm.find("input[name=id]"),
            editLink    = editForm.find("input[name=link]"),
            editName    = editForm.find("select[name=name]");

        editID.val(id);
        editLink.val(link);
        editName.val(name);

        UIkit.modal(modal).show();
        return false;
    })

    $("#button-new-post-category").on("click", function() {
        var btn         = $(this),
            modal = btn.data('purple-modal');

        $(modal).css('z-index', '12000');

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-modal-status-comment").on("click", function() {
        var modal  = $(this).data('purple-modal'),
            status = $(this).data('purple-status'),
            id     = $(this).data('purple-id');

        $(modal).find('input[name=id]').val(id);
        $(modal).find('select[name=status]').val(status);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-modal-reply-comment").on("click", function() {
        var modal = $(this).data('purple-modal'),
            from  = $(this).data('purple-name');

        $(modal).find('.bind-from').html(from);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-modal-status-comment").on("click", function() {
        var modal = $(this).data('purple-modal');
        UIkit.modal(modal).show();
        return false;
    })

    $(".button-apply-theme").on("click", function() {
        var modal  = $(this).data('purple-modal'),
            folder = $(this).data('purple-folder'),
            name   = $(this).data('purple-name')

        $(modal).find('input[name=name]').val(name);
        $(modal).find('input[name=folder]').val(folder);
        $(modal).find('.bind-title').html(name);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-add-purple").on("click", function() {
        var modal = $(this).data('purple-modal');
        UIkit.modal(modal).show();
        return false;
    })

    $(".button-delete-purple").on("click", function() {
        var btn         = $(this),
            id          = btn.data('purple-id'),
            title       = btn.data('purple-name'),
            modal       = btn.data('purple-modal'),

            // For Navigation Only
            entity      = btn.data('purple-entity'),
            
            // For Pages Only
            pageType    = btn.data('purple-type'),

            // For Themes Only
            themeFolder = btn.data('purple-folder'),
            themeName   = btn.data('purple-name'),

            deleteModal = $(modal),
            deleteForm  = deleteModal.find("form"),
            deleteID    = deleteForm.find("input[name=id]"),
            deleteTitle = deleteForm.find(".bind-title");

        deleteID.val(id);
        deleteTitle.html(title);

        // For Navigation Only
        if (entity == 'menu' || entity == 'submenu') {
            deleteForm.find("input[name=navtype]").val(entity);
        }

        // For Pages Only
        deleteForm.find("input[name=page_type]").val(pageType);

        // For Themes Only
        deleteForm.find("input[name=folder]").val(themeFolder);
        deleteForm.find("input[name=name]").val(themeName);

        UIkit.modal(modal).show();
        return false;
    })

    $(".button-disallowed-delete").on("click", function() {
        var modal = $(this).data('purple-modal');
        UIkit.modal(modal).show();
        return false;
    })

    $(".button-get-permalink").on("click", function() {
        var btn    = $(this),
            link   = btn.data('purple-link'),
            modal  = btn.data('purple-modal'),
            showModal = $(modal),
            input  = showModal.find('input');

        input.val(link);
        UIkit.modal(modal).show();
        return false;
    })

    // Button Text
    var clipboard     = new ClipboardJS('.button-copy-permalink'),
        defaultButton = $('.button-copy-permalink').html();

    clipboard.on('success', function(e) {
        $('.button-copy-permalink').html('Copied');
        setTimeout(function() {
            $('.button-copy-permalink').html(defaultButton);
        }, 1000);
        var createToast = notifToast('Permalink', 'Link has been copied', 'success', true, 1500);
        e.clearSelection();
    });

    clipboard.on('error', function(e) {
        setTimeout(function() {
            $('.button-copy-permalink').html('Error Copy');
        }, 1000);
        var createToast = notifToast('Permalink', 'Error copy link', 'error', true);
    });

    // Button Icon
    var clipboardIcon = new ClipboardJS('.icon-copy-permalink');

    clipboardIcon.on('success', function(e) {
        var createToast = notifToast('Permalink', 'Link has been copied', 'success', true, 1500);
        e.clearSelection();
    });

    clipboardIcon.on('error', function(e) {
        var createToast = notifToast('Permalink', 'Error copy link', 'error', true);
    });

    var clipboardIconKey = new ClipboardJS('.icon-copy-key');

    clipboardIconKey.on('success', function(e) {
        var createToast = notifToast('Key', 'key has been copied', 'success', true, 1500);
        e.clearSelection();
    });

    clipboardIconKey.on('error', function(e) {
        var createToast = notifToast('Key', 'Error copy key', 'error', true);
    });

    $(".button-open-page").on("click", function() {
        var target = $("#purple-permalink").val();
        window.open(target);
        return false;
    })

    $(".button-test-email").on("click", function() {
        var btn       = $(this),
            modal     = btn.data('purple-modal'),
            showModal = $(modal);

        UIkit.modal(modal).show();
        return false;
    })

    /**
     * 
     * Froala Master Element
     * 
     */

    modifyElementProperties = function() {
        $('#button-element-properties').on("click", function() {
            var btn     = $(this),
                modal   = $('#modal-element-properties'),
                idEl    = modal.find('input[name=element-id]').val(),
                target  = btn.attr('data-purple-target'),
                classes = modal.find('input[name=element-class]').tagEditor('getTags')[0].tags;

            if (classes.length > 0) {
                var bindClass  = classes.join(' ');
            }
            else {
                var bindClass  = '';
            }

            $("#bind-fdb-blocks").find('*[data-tree-id='+target+']').attr('id', idEl);
            $("#bind-fdb-blocks").find('*[data-tree-id='+target+']').attr('class', bindClass);

            UIkit.modal('#modal-element-properties').hide();

            return false;
        })
    }

    /**
     *
     * UIkit Slider Tuning
     * 
     */
    
    changeUikitSliderOption = function() {
        $(".fdb-block-menu-button-slidertuning").on("click", function() {
            var btn      = $(this),
                target   = btn.attr('data-fdb-id'),
                modal    = "#modal-uikit-slider-tuning",
                block    = $("#fdb-"+target),
                option   = $("#fdb-"+target + " > div").attr('uk-slider'),
                splitOpt = option.split(";");

            $(modal).find("#button-change-slidertuning").attr('data-purple-target', target);
            $.each(splitOpt, function(index, chunk) {
                var splitValue = chunk.split(":"),
                    opt        = splitValue[0],
                    val        = splitValue[1];

                    // console.log(opt);
                    // console.log(val);

                $(modal).find("select[name="+opt+"]").val(val);
            });
            UIkit.modal(modal).show();
            return false;
        })

        $("#modal-uikit-slider-tuning").find("#button-change-slidertuning").on("click", function() {
            var btn    = $(this),
                target = btn.attr('data-purple-target'),
                block  = $("#fdb-"+target + "> div"),
                attr   = block.attr('uk-slideshow'),
                centerAttr  = $("#fdb-"+target).attr('data-uikit-slider-center'),
                autoplay    = $("#modal-uikit-slider-tuning").find('select[name=autoplay]').val(),
                interval    = $("#modal-uikit-slider-tuning").find('select[name=autoplay-interval]').val(),
                finite      = $("#modal-uikit-slider-tuning").find('select[name=finite]').val(),
                attrValue   = 'center:' + centerAttr + ';autoplay:' + autoplay + ';finite:' + finite + ';autoplay-interval:' + interval;

            var newBlock = '<div class="uk-position-relative uk-visible-toggle uk-light" data-purple-add-attr="uk-slider" uk-slider="'+attrValue+'">'+block.html()+'</div>'
            $("#fdb-"+target).html(newBlock);
            UIkit.modal("#modal-uikit-slider-tuning").hide();
            return false;
        })
    }

    /**
     *
     * UIkit Slider Main Caption
     * 
     */

    checkSliderCaptionPosition = function() {
        var current     = $(".slider-main-caption").attr('class');
            splitCl     = current.split(' '),
            checkLeft   = splitCl.indexOf("uk-position-center-left"),
            checkCenter = splitCl.indexOf("uk-position-center"),
            checkRight  = splitCl.indexOf("uk-position-center-right");

        if (checkLeft != '-1') {
            return 'center-left';
        }

        if (checkCenter != '-1') {
            return 'center';
        }

        if (checkRight != '-1') {
            return 'center-right';
        }

    }

    clickSliderCaptionButton = function(button, position, target) {
        // console.log(button);
        // console.log(position);
        var btn = $('.fdb-block-slider-caption-to-'+button+'-button');
        if (position == 'center') {
            btn.on("click", function() {
                if (button == 'left') {
                    target.addClass('uk-position-center-left');
                    target.removeClass('uk-position-center');
                    target.removeClass('uk-position-center-right');
                }
                else if (button == 'right') {
                    target.addClass('uk-position-center-right');
                    target.removeClass('uk-position-center');
                    target.removeClass('uk-position-center-left');
                }

                // return false;
            })
        }
        else if (position == 'center-left') {
            btn.on("click", function() {
                if (button == 'left') {
                    return false;
                }
                else if (button == 'right') {
                    target.addClass('uk-position-center');
                    target.removeClass('uk-position-center-right');
                    target.removeClass('uk-position-center-left');
                }

                // return false;
            })
        }
        else if (position == 'center-right') {
            btn.on("click", function() {
                if (button == 'left') {
                    target.addClass('uk-position-center');
                    target.removeClass('uk-position-center-right');
                    target.removeClass('uk-position-center-left');
                }
                else if (button == 'right') {
                    return false;
                }

                // return false;
            })
        }
    }

    moveSliderCaption = function() {
        $(".slider-main-caption").on({
            mouseenter: function () {
                var target     = $(this),
                    currentPos = checkSliderCaptionPosition(),
                    appendLeftBtn  = '<div class="fdb-block-slider-caption-left-button">' +
                        '<a class="fdb-block-slider-caption-to-left-button" uk-tooltip="title: Move Left"><i class="mdi mdi-arrow-left-thick" id=""></i></a>' +
                        '<div>',
                    appendRightBtn = '<div class="fdb-block-slider-caption-right-button">' +
                        '<a class="fdb-block-slider-caption-to-right-button" uk-tooltip="title: Move Right"><i class="mdi mdi-arrow-right-thick" id=""></i></a>' +
                        '<div>';
                // console.log(currentPos);

                target.addClass('fdb-block-selected-slider-caption');
                target.append(appendLeftBtn);
                target.append(appendRightBtn);

                clickSliderCaptionButton('left', currentPos, target);
                clickSliderCaptionButton('right', currentPos, target);
            },
            mouseleave: function () {
                
            }
        })

        $(".slider-main-caption").closest('.fdb-block').on({
            mouseenter: function () {
            },
            mouseleave: function () {
                $(".slider-main-caption").removeClass('fdb-block-selected-slider-caption');
                $(".slider-main-caption").find('.fdb-block-slider-caption-left-button').remove();
                $(".slider-main-caption").find('.fdb-block-slider-caption-right-button').remove();
            }
        })
    }

    /**
     *
     * Froala Blocks Background Color
     * 
     */
    
    changeBackgroundColor = function() {
        $(".fdb-block-menu-button-bgcolor").on("click", function() {
            var btn    = $(this),
                target = btn.attr('data-fdb-id'),
                bg     = btn.attr('data-fdb-bg'),
                modal  = "#modal-change-bgcolor";

            $(modal).find("#button-change-bgcolor").attr('data-purple-target', target);
            $(modal).find("#button-change-bgcolor").attr('data-purple-bg', bg);
            UIkit.modal(modal).show();
            return false;
        })

        $("#modal-change-bgcolor").find("select[name=color-option]").on('change', function() {
            var value = this.value;

            if (value.length > 0) {
                if (value == 'solid') {
                    $("#modal-change-bgcolor").find(".solid-selector").show();
                    $("#modal-change-bgcolor").find(".gradient-selector").hide();
                    $("#modal-change-bgcolor").find("#button-change-bgcolor").attr('data-purple-color-type', value);
                    $("#modal-change-bgcolor").find("#button-change-bgcolor").attr('data-purple-gradient', $("#modal-change-bgcolor").find('input[name=solid-color]').val());
                }
                else if (value == 'gradient') {
                    $("#modal-change-bgcolor").find(".gradient-selector").show();
                    $("#modal-change-bgcolor").find(".solid-selector").hide();

                    $(".gradient-container").on("click", function() {
                        var gradient  = $(this),
                            container = $("#modal-change-bgcolor").find(".gradient-selector"),
                            color     = gradient.data('purple-gradient');

                        container.find(".gradient-container .selected-overlay").remove();
                        gradient.append('<div class="uk-overlay-default uk-position-cover selected-overlay">' +
                                        '<div class="uk-position-center">' +
                                            '<span uk-overlay-icon="icon: check;"></span>' +
                                        '</div>' +
                                     '</div>');
                        $("#modal-change-bgcolor").find("#button-change-bgcolor").removeAttr('disabled');
                        $("#modal-change-bgcolor").find("#button-change-bgcolor").attr('data-purple-color-type', value);
                        $("#modal-change-bgcolor").find("#button-change-bgcolor").attr('data-purple-gradient', color);

                        return false;
                    })
                }

                $("#modal-change-bgcolor").find("#button-change-bgcolor").on("click", function() {
                    var btn    = $(this),
                        target = btn.attr('data-purple-target'),
                        bg     = btn.attr('data-purple-bg'),
                        ctype  = btn.attr('data-purple-color-type'),
                        block  = $("#fdb-"+target),
                        attr   = block.attr('style');
                    
                    if (ctype == 'solid') {
                        var color  = $("#modal-change-bgcolor").find('input[name=solid-color]').val();
                        if (bg == 'section') {
                            block.css('background-color', color);
                            block.css('background-image', '');
                        }
                        else if (bg == 'block') {
                            $(".fdb-block-bgcolor-"+target).css('background-color', color);
                            $(".fdb-block-bgcolor-"+target).css('background-image', '');
                        }
                    }
                    else if (ctype == 'gradient') {
                        var color  = btn.attr('data-purple-gradient');
                        if (bg == 'section') {
                            block.css('background-color', '');
                            block.css('background-image', color);
                        }
                        else if (bg == 'block') {
                            $(".fdb-block-bgcolor-"+target).css('background-color', '');
                            $(".fdb-block-bgcolor-"+target).css('background-image', color);
                        }
                    }

                    // if (typeof attr !== typeof undefined && attr !== false) {
                        
                    // }
                    
                    UIkit.modal("#modal-change-bgcolor").hide();
                    return false;
                })
            }
            else {
                $("#modal-change-bgcolor").find("#button-change-bgcolor").attr('disabled','disabled');
                $("#modal-change-bgcolor").find("#button-change-bgcolor").on("click", function() {
                    return false;
                })
            }
        })
    }

    /**
     *
     * Save Block to File
     * 
     */
    
    saveBlockToFile = function() {
        $(".fdb-block-menu-button-saveblock").on("click", function() {
            $('.fdb-block-menu-button').remove();

            var btn    = $(this),
                target = btn.attr('data-fdb-id'),
                html   = $('#fdb-'+target).get(0).outerHTML,

                modal  = "#modal-save-block";

            $(modal).find("input[name=html]").val(html);
            $(modal).find("#button-save-block").attr('data-purple-target', target);
            UIkit.modal(modal).show();
            return false;
        })

        $("#modal-save-block").find("#button-save-block").off().one('click',function() {
            var btn    = $(this),
                target = btn.attr('data-purple-target'),
                html   = $("#modal-save-block").find('input[name=html]').val(),
                name   = $("#modal-save-block").find('input[name=name]').val(),
                url    = $('#fdb-block-save-url').val(),
                token  = $('#csrf-ajax-token').val(),
                data   = { html:html, name:name, target:target };

            if (name.length > 0) {
                $.ajax({
                    type: "POST",
                    url:  url,
                    headers : {
                        'X-CSRF-Token': token
                    },
                    data: data,
                    cache: false,
                    beforeSend: function() {
                        btn.html('<i class="fa fa-circle-o-notch fa-spin"></i> Saving Block...')
                        btn.attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        // console.log(data);
                        var json    = $.parseJSON(data),
                            status  = (json.status);

                        if (status == 'ok') {
                            btn.html('Block Saved');
                            btn.removeAttr('disabled', 'disabled');
                            setTimeout(function() {
                                btn.html('Save');
                                $("#modal-save-block").find('input[name=name]').val('');
                                $("#modal-save-block").find('input[name=html]').val('');
                                UIkit.modal('#modal-save-block').hide();
                            }, 2000);
                        }
                        else {
                            btn.html('Save');
                            btn.removeAttr('disabled', 'disabled');
                            alert("Can't save block. Please try again.") 
                        }
                    }
                })
            }
            else {
                alert('Please fill the block name and try again.');
            }
        })
            
    }

    /**
     *
     * Delete Saved Block
     * 
     */
    
    $(".button-delete-saved-block").on("click", function() {
        var btn    = $(this),
            modal  = "#modal-delete-saved-block",
            target = btn.data('purple-target'),
            clsBlk = btn.data('purple-class'),
            name   = btn.data('purple-name');

        $(modal).find('.bind-name').html(name);
        $(modal).find('input[name=file]').val(target);
        $(modal).find('input[name=class]').val(clsBlk);

        UIkit.modal(modal).show();
        return false;
    })

    $("#button-delete-saved-block").on("click", function() {
        var btn   = $(this),
            file  = $('#modal-delete-saved-block').find('input[name=file]').val(),
            cls   = $('#modal-delete-saved-block').find('input[name=class]').val(),
            block = $('#saved-block-'+file),
            url   = $('#fdb-block-delete-url').val(),
            token = $('#csrf-ajax-token').val(),
            data  = { file:file, class:cls }

        $.ajax({
            type: "POST",
            url:  url,
            headers : {
                'X-CSRF-Token': token
            },
            data: data,
            cache: false,
            beforeSend: function() {
                btn.html('<i class="fa fa-circle-o-notch fa-spin"></i> Deleting Block...')
                btn.attr('disabled', 'disabled');
            },
            success: function(data) {
                // console.log(data);
                var json    = $.parseJSON(data),
                    status  = (json.status);

                if (status == 'ok') {
                    var jsonFile = (json.json);

                    btn.html('Block Deleted');
                    btn.removeAttr('disabled', 'disabled');
                    $('.js-filter').find('.saved-block-'+jsonFile).remove();
                    $('.js-filter').find('.saved-block-'+jsonFile+'-button').remove();
                    setTimeout(function() {
                        btn.html('Yes, Delete it');
                        $("#modal-delete-saved-block").find('input[name=file]').val('');
                        UIkit.modal('#modal-delete-saved-block').hide();
                    }, 2000);
                }
                else {
                    btn.html('Yes, Delete it');
                    btn.removeAttr('disabled', 'disabled');
                    alert("Can't delete block. Please try again.") 
                }
            }
        })
    })

    /**
     *
     * Froala Blocks UIkit Filter
     * 
     */
    
    setUikitFilterId = function() {
        $(".fdb-block-uk-filter-list-button").on("click", function() {
            var btn     = $(this),
                target  = btn.closest('li').attr('data-filter-id'),
                current = btn.attr('data-current-filter'),
                modal   = "#modal-uikit-set-filter-id";

            $(modal).find("#button-uikit-set-filter-id").attr('data-purple-target', target);
            $(modal).find("#button-uikit-set-filter-id").attr('data-current-filter', current);
            $(modal).find('input[name=category]').val(current);
            UIkit.modal(modal).show();
            return false;
        })

        $("#modal-uikit-set-filter-id").find("#button-uikit-set-filter-id").on("click", function() {
            var btn      = $(this),
                target   = btn.attr('data-purple-target'),
                category = $("#modal-uikit-set-filter-id").find('input[name=category]').val();

            var value  = category.indexOf(" ");
            if (value == -1) {
                $('li[data-filter-id='+target+']').attr('uk-filter-control', "[data-category='"+category+"']")
                UIkit.modal("#modal-uikit-set-filter-id").hide();
            }
            else {
                alert('Please remove spaces in Filter ID');
            }
            
            return false;
        })
    }
    
    setUikitFilterCategory = function() {
        $(".fdb-block-uk-filter-item-button").on("click", function() {
            var btn     = $(this),
                target  = btn.closest('li').attr('data-filter-item'),
                current = btn.attr('data-current-filter'),
                modal   = "#modal-uikit-set-filter-category";

            $(modal).find("#button-uikit-set-filter-category").attr('data-purple-target', target);
            $(modal).find("#button-uikit-set-filter-category").attr('data-current-filter', current);

            var bindSelect = '<select class="form-control" name="category" required>';
            $('.uk-block-filter').each(function(i){
                var filterName = $(this).find('a').text();
                var filterId   = $(this).attr('uk-filter-control');
                var replaceId1 = filterId.replace("[data-category='", '');
                var replaceId2 = replaceId1.replace("']", ''); 

                if (current == replaceId2) {
                    bindSelect += '<option value="'+replaceId2+'" selected="selected">'+filterName+'</option>';
                }
                else {
                    bindSelect += '<option value="'+replaceId2+'">'+filterName+'</option>';
                }
            });
            bindSelect += '</select>';

            $(modal).find('.bind-select-category').html('<label>Filter Category</label>'+bindSelect);
            UIkit.modal(modal).show();
            return false;
        })

        $("#modal-uikit-set-filter-category").find("#button-uikit-set-filter-category").on("click", function() {
            var btn      = $(this),
                target   = btn.attr('data-purple-target'),
                category = $("#modal-uikit-set-filter-category").find('select[name=category]').val();

            $('li[data-filter-item='+target+']').attr('data-category', category);
            UIkit.modal("#modal-uikit-set-filter-category").hide();
            
            return false;
        })
    }

    /**
     *
     * Froala Blocks UIkit Animation
     * 
     */
    
    addUikitAnimationBlock = function() {
        $(".fdb-block-menu-button-animation").on("click", function() {
            var btn    = $(this),
                target = btn.attr('data-fdb-id'),
                modal  = "#modal-uikit-add-animation";

            $(modal).find("#button-uikit-add-animation").attr('data-purple-target', target);
            UIkit.modal(modal).show();
            return false;
        })

        $("#modal-uikit-add-animation").find("#button-uikit-add-animation").on("click", function() {
            var btn    = $(this),
                target = btn.attr('data-purple-target'),
                block  = $("#fdb-"+target),
                anim   = $("#modal-uikit-add-animation").find('select[name=animation]').val();

            if (anim == 'reset') {
                block.removeAttr('uk-scrollspy');
                block.removeClass('uk-scrollspy-inview');
            }
            else {
                block.attr('uk-scrollspy', 'cls: ' + anim + '; repeat: true');
            }
            UIkit.modal("#modal-uikit-add-animation").hide();
            return false;
        })
    }

    /**
     *
     * Froala Blocks Font Awesome Icons
     * 
     */
    
    changeFontAwesomeIcon = function() {
        $('.fdb-font-awesome').on({
            mouseenter: function () {
                var target    = $(this),
                    current   = target.attr('class'),
                    splitCr   = current.split(' '),
                    faIcon    = splitCr.slice(-1)[0],
                    faValue   = target.attr('data-purple-fa-icon'),
                    faColor   = target.attr('data-purple-fa-color'),
                    modal     = $('#modal-font-awesome-icon'),
                    selectBtn = modal.find('.button-select-icon'),
                    iconBtn   = '<div class="fdb-block-font-awesome-button">'+
                                    '<a class="fdb-block-font-awesome-button" uk-tooltip="title: Change Icon">'+
                                        '<i class="mdi mdi-flag" id=""></i>'+
                                    '</a>'+
                                '</div>';
                $(this).append(iconBtn);

                $('.font-awesome-color-container').show();

                var identifier = Math.floor((Math.random() * 100000) + 1);
                    $(this).attr('data-purple-identifier', identifier);
                    selectBtn.attr('data-purple-identifier', identifier);

                $(".fdb-block-font-awesome-button").on("click", function() {
                    UIkit.modal(modal).show();

                    $("#modal-font-awesome-icon-body").animate({
                        scrollTop: $("#modal-font-awesome-icon-body .fa-" + faValue).offset().top -100
                    }, 1500);
                    
                    return false;
                })

                selectBtn.attr('data-purple-icon', faValue);

                modal.find('.purple-font-awesome-icon').removeClass('selected');
                modal.find(".fa-" + faValue).closest('.purple-font-awesome-icon').addClass('selected');
                modal.find(".font-awesome-color").val(faColor);

                modal.find(".purple-font-awesome-icon").on("click", function() {
                    var value = $(this).attr('value');
                    modal.find(".purple-font-awesome-icon").removeClass('selected');
                    $(this).addClass('selected');

                    selectBtn.removeAttr('disabled');
                    selectBtn.attr('data-purple-icon', value);
                })

                selectBtn.on("click", function() {
                    var source    = $(this).attr('data-purple-source'),
                        iconColor = modal.find(".font-awesome-color").val(),
                        onlyColor = iconColor.replace('#',''),
                        lastClass = splitCr.pop(),
                        newClass  = 'fa-' + $(this).attr('data-purple-icon'),
                        id        = $(this).attr('data-purple-identifier');

                    splitCr.push(newClass);
                    var newFontClass = splitCr.join(' ');

                    selectBtn.attr('disabled', 'disabled');
                    if (source == 'button-customizing') {
                        selectBtn.html('<i class="fa fa-circle-o-notch fa-spin"></i> Attaching Icon...');
                        $('#modal-bttns-customizing').find('#attach-icon-for-button').val($(this).attr('data-purple-icon'));
                    }
                    else {
                        selectBtn.html('<i class="fa fa-circle-o-notch fa-spin"></i> Updating Icon...');
                        // target.attr('class', newFontClass);
                        $('.fdb-font-awesome[data-purple-identifier='+id+']').attr('class', newFontClass);
                        $('.fdb-font-awesome[data-purple-identifier='+id+']').css('color', iconColor);
                        $('.fdb-font-awesome[data-purple-identifier='+id+']').attr('data-purple-fa-color', onlyColor);
                    }
                    

                    setTimeout(function() {
                        selectBtn.removeAttr('disabled');
                        selectBtn.html('Select Icon');
                        $('.fdb-font-awesome').removeAttr('data-purple-identifier');
                        UIkit.modal(modal).hide();
                    }, 500);
                    return false;
                })
            },
            mouseleave: function () {
                $(this).find('.fdb-block-font-awesome-button').remove();
                // $('#modal-font-awesome-icon').find(".purple-font-awesome-icon").removeClass('selected');
            }
        })
    }

    /**
     *
     * Froala Blocks Buttons Customizing
     * 
     */
    
    buttonsCustomizing = function() {
        $('.bttn-to-customize').on({
            mouseenter: function () {
                var target    = $(this),
                    current   = $(this).attr('class'),
                    splitCl   = current.split(' '),
                    crText    = $(this).text(),
                    crStyle   = $(this).attr('data-purple-bttn-style'),
                    crSize    = $(this).attr('data-purple-bttn-size'),
                    crBase    = $(this).attr('data-purple-bttn-base'),
                    crIcon    = $(this).attr('data-purple-bttn-icon'),
                    crIconPos = $(this).attr('data-purple-bttn-iconposition'),
                    modal     = $('#modal-bttns-customizing'),
                    selectBtn = modal.find('.button-bttns-customizing');

                $(this).addClass('fdb-block-bttns-target');
                $(this).attr('uk-tooltip', 'title: Click to Customize Button; pos: bottom');

                var identifier = Math.floor((Math.random() * 100000) + 1);
                    $(this).attr('data-purple-identifier', identifier);
                    selectBtn.attr('data-purple-identifier', identifier);

                var findArray = function (haystack, arr) {
                    return arr.some(function (v) {
                        return haystack.indexOf(v) >= 0;
                    });
                };

                if (crText !== undefined) {
                    modal.find('input[name=text]').val(crText);
                }
                if (crStyle !== undefined) {
                    modal.find('select[name=style] option[value="'+crStyle+'"]').attr("selected","selected");
                }
                if (crSize !== undefined) {
                    modal.find('select[name=size] option[value="'+crSize+'"]').attr("selected","selected");
                }
                if (crBase !== undefined) {
                    modal.find('select[name=base] option[value="'+crBase+'"]').attr("selected","selected");
                }
                if (crIcon !== undefined) {
                    modal.find('input[name=icon]').val(crIcon);
                }
                if (crIconPos !== undefined) {
                    modal.find('select[name=icon-position] option[value="'+crIconPos+'"]').attr("selected","selected");
                }

                target.on("click", function() {
                    $('.font-awesome-color-container').hide();
                    UIkit.modal(modal).show();
                    return false;
                })

                $('#browse-icon-for-button').on("click", function() {
                    var source  = $(this).attr('data-purple-source'),
                        modalFa = $('#modal-font-awesome-icon');

                    UIkit.modal(modalFa).show();
                    modalFa.find('.button-select-icon').attr('data-purple-source',source);

                    return false;
                })

                var modalFa = $('#modal-font-awesome-icon');

                modalFa.find(".purple-font-awesome-icon").on("click", function() {
                    var value = $(this).attr('value');
                    modalFa.find(".purple-font-awesome-icon").removeClass('selected');
                    $(this).addClass('selected');

                    modalFa.find('.button-select-icon').removeAttr('disabled');
                    modalFa.find('.button-select-icon').attr('data-purple-icon', value);
                })

                modalFa.find('.button-select-icon').on("click", function() {
                    var source    = $(this).attr('data-purple-source'),
                        iconColor = modalFa.find(".font-awesome-color").val(),
                        onlyColor = iconColor.replace('#',''),
                        newClass  = 'fa-' + $(this).attr('data-purple-icon'),
                        id        = $(this).attr('data-purple-identifier');

                    modalFa.find('.button-select-icon').attr('disabled', 'disabled');
                    modalFa.find('.button-select-icon').html('<i class="fa fa-circle-o-notch fa-spin"></i> Attaching Icon...');
                    $('#modal-bttns-customizing').find('#attach-icon-for-button').val($(this).attr('data-purple-icon'));

                    setTimeout(function() {
                        modalFa.find('.button-select-icon').removeAttr('disabled');
                        modalFa.find('.button-select-icon').html('Select Icon');
                        $('.fdb-font-awesome').removeAttr('data-purple-identifier');
                        UIkit.modal(modalFa).hide();
                    }, 500);
                    setTimeout(function() {
                        UIkit.modal('#modal-bttns-customizing').show();
                    }, 1000);
                    return false;
                })

                selectBtn.on("click", function() {
                    if ($('#modal-bttns-customizing').find('input[name=text]').val().length > 0) {
                        var bttnsStyle    = modal.find('select[name=style]').val(),
                            bttnsSize     = modal.find('select[name=size]').val(),
                            bttnsBase     = modal.find('select[name=base]').val(),
                            bttnsText     = modal.find('input[name=text]').val(),
                            bttnsIcon     = modal.find('input[name=icon]').val(),
                            bttnsIconPos  = modal.find('select[name=icon-position]').val(),
                            id            = $(this).attr('data-purple-identifier'),
                            currentHtml   = $('.bttn-to-customize[data-purple-identifier='+id+']').text();

                        if (bttnsStyle != 'default') {
                            $('.bttn-to-customize[data-purple-identifier='+id+']').attr('data-purple-bttn-style', bttnsStyle);
                            $('.bttn-to-customize[data-purple-identifier='+id+']').addClass(bttnsStyle);
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('btn');
                        }
                        else {
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-simple');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-material-flat');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-gradient');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-stretch');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-minimal');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-bordered');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').addClass('btn');
                        }

                        if (bttnsSize != 'default') {
                            $('.bttn-to-customize[data-purple-identifier='+id+']').attr('data-purple-bttn-size', bttnsSize);
                            $('.bttn-to-customize[data-purple-identifier='+id+']').addClass(bttnsSize);
                        }
                        else {
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-sm');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-md');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-lg');
                        }

                        if (bttnsBase != 'default') {
                            $('.bttn-to-customize[data-purple-identifier='+id+']').attr('data-purple-bttn-base', bttnsBase);
                            $('.bttn-to-customize[data-purple-identifier='+id+']').addClass(bttnsBase);
                        }
                        else {
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-primary');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-danger');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-warning');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-success');
                            $('.bttn-to-customize[data-purple-identifier='+id+']').removeClass('bttn-royal');
                        }     

                        if(bttnsIcon.length > 0) {
                            $('.bttn-to-customize[data-purple-identifier='+id+']').attr('data-purple-bttn-icon', bttnsIcon);
                            $('.bttn-to-customize[data-purple-identifier='+id+']').attr('data-purple-bttn-iconposition', bttnsIconPos);

                            if (bttnsIconPos == 'front') {
                                $('.bttn-to-customize[data-purple-identifier='+id+']').html('<i class="fa fa-' + bttnsIcon + '"></i> ' + bttnsText);

                            }
                            else if (bttnsIconPos == 'back') {
                                $('.bttn-to-customize[data-purple-identifier='+id+']').html(bttnsText + ' <i class="fa fa-' + bttnsIcon + '"></i>');
                            }
                        }

                        UIkit.modal(modal).hide();
                        UIkit.modal(modalFa).hide();
                    }
                    else {
                        alert('Please fill the button text.')
                    }

                    return false;
                })
            },
            mouseleave: function () {
                $(this).removeAttr('uk-tooltip');
                $(this).attr('title', '');
                // $(this).find('.fdb-block-bttns-customizing-button').remove();
                // $(this).removeClass('fdb-block-bttns-target');
            }
        })
    }

    /**
     *
     * Custom Page Code Editor
     * 
     */
    
    function editorFullscreenMode() {
        function toggleEditorFullscreen1() {
            $(this).attr('data-purple-active', 'yes');
            $(this).addClass('active');
            $("#purple-fdb-blocks-preview").removeClass('uk-width-2-3');
            $("#purple-fdb-blocks-preview").addClass('editor-fullscreen-mode');
            $("#purple-fdb-blocks-preview").find('.uk-card-body').addClass('uk-card-body-fullscreen');
            $("#purple-fdb-blocks").removeClass('uk-width-1-3');
            $("#purple-fdb-blocks").addClass('meta-fullscreen-mode');
            $("html").css('overflow', 'hidden');
            $('#fdb-code-editor-ace').css('height', '1000px');
            $('#fdb-css-editor-ace').css('height', '1000px');
            $('#fdb-code-editor-ace').ace({ theme: 'chrome', lang: 'html' })
            $('#fdb-css-editor-ace').ace({ theme: 'chrome', lang: 'css' })

            $(this).one("click", toggleEditorFullscreen2);
        }

        function toggleEditorFullscreen2() {
            $(this).attr('data-purple-active', 'no');
            $(this).removeClass('active');
            $("#purple-fdb-blocks-preview").addClass('uk-width-2-3');
            $("#purple-fdb-blocks-preview").removeClass('editor-fullscreen-mode');
            $("#purple-fdb-blocks-preview").find('.uk-card-body').removeClass('uk-card-body-fullscreen');
            $("#purple-fdb-blocks-preview").removeAttr('style');
            $("#purple-fdb-blocks").addClass('uk-width-1-3');
            $("#purple-fdb-blocks").removeClass('meta-fullscreen-mode');
            $("html").css('overflow', 'auto');
            $('#fdb-code-editor-ace').ace({ theme: 'chrome', lang: 'html' })
            $('#fdb-css-editor-ace').ace({ theme: 'chrome', lang: 'css' })

            $(this).one("click", toggleEditorFullscreen1);
        }
        $("#button-toggle-code-fullscreen").one("click", toggleEditorFullscreen1);

        return $("#button-toggle-code-fullscreen").attr('data-purple-active');
    }

    editorFullscreenMode();

    /**
     *
     * Image Browser
     *
     */

    browseImageButton = function() {
        $(".button-browse-images").on("click", function() {
            var btn       = $(this),
                btnTxt    = btn.html(),
                action    = btn.data('purple-browse-action'),
                content   = btn.data('purple-browse-content'),
                actionurl = btn.data('purple-browse-actionurl'),
                target    = btn.data('purple-browse-target'),
                redirect  = btn.data('purple-redirect'),
                modal     = $("#modal-browse-images");

            btn.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
            btn.attr('disabled','disabled');

            setTimeout(function() {
                if (action == 'update') {
                    var split = content.split('::'),
                        table = split[0],
                        id    = split[1];

                    modal.find(".button-select-image").attr('data-purple-action', 'update');
                    modal.find(".button-select-image").attr('data-purple-table', table);
                    modal.find(".button-select-image").attr('data-purple-id', id);
                    modal.find(".button-select-image").attr('data-purple-redirect', redirect);
                }
                else if (action == 'send-to-input') {
                    modal.find(".button-select-image").attr('data-purple-action', 'send-to-input');
                    modal.find(".button-select-image").attr('data-purple-table', table);
                    modal.find(".button-select-image").attr('data-purple-id', id);
                    modal.find(".button-select-image").attr('data-purple-target', target);
                }
                else if (action == 'froala-section-bg') {
                    var sectionId = btn.attr('data-fdb-id');
                    modal.find(".button-select-image").attr('data-purple-action', 'froala-section-bg');
                    modal.find(".button-select-image").attr('data-purple-froala-block', '#fdb-' + sectionId);
                    if (modal.find(".button-remove-background").length < 1) {
                        modal.find(".uk-modal-footer").find(".uk-text-right").prepend('<button class="btn btn btn-outline-danger uk-margin-right button-remove-background" data-purple-id="'+sectionId+'" data-purple-froala-block="#fdb-'+sectionId+'">Remove Background</button>');

                        $(".button-remove-background").on("click", function() {
                            var block = $(this).attr('data-purple-froala-block'),
                                image = $(".choose-image"),
                                modal = "#modal-browse-images";
                                // console.log(block);

                            $(block).css('background', 'none');
                            $(block).css('background-image', 'none');
                            image.find(".selected-overlay").remove();
                            UIkit.modal(modal).hide();
                            return false;
                        })
                    }
                }
                else if (action == 'froala-block-bg') {
                    var sectionId = btn.attr('data-fdb-id');
                    modal.find(".button-select-image").attr('data-purple-action', 'froala-block-bg');
                    modal.find(".button-select-image").attr('data-purple-froala-block', '#fdb-block-background-' + sectionId);
                    if (modal.find(".button-remove-background").length < 1) {
                        modal.find(".uk-modal-footer").find(".uk-text-right").prepend('<button class="btn btn btn-outline-danger uk-margin-right button-remove-background" data-purple-id="'+sectionId+'" data-purple-froala-block="#fdb-'+sectionId+'">Remove Background</button>');

                        $(".button-remove-background").on("click", function() {
                            var block = $(this).attr('data-purple-froala-block'),
                                image = $(".choose-image"),
                                modal = "#modal-browse-images";
                                // console.log(block);

                            $(block).css('background', 'none');
                            $(block).css('background-image', 'none');
                            image.find(".selected-overlay").remove();
                            UIkit.modal(modal).hide();
                            return false;
                        })
                    }
                }

                UIkit.modal('#modal-browse-images').show();
                btn.removeAttr('disabled');
                btn.html(btnTxt);
            }, 1000);

            return false;
        })
    }

    function browseImagePaging(action) {
        if (action == 'prev') {
            var parent = $('.purple-pagination .prev');
            var target = $('.purple-pagination .prev button');

            var reverseParent = $('.purple-pagination .next');
            var reverseTarget = $('.purple-pagination .next button');
        }
        else if (action == 'next') {
            var parent = $('.purple-pagination .next');
            var target = $('.purple-pagination .next button');

            var reverseParent = $('.purple-pagination .prev');
            var reverseTarget = $('.purple-pagination .prev button');
        }

        // console.log(parent);
        // console.log(target);
        
        target.on("click", function() {
            if (target.parent().hasClass('disabled')) {
                // console.log('disabled');
                return false;
            }
            else {
                $("#modal-browse-images").find('button').attr('disabled', 'disabled');

                var btn  = $(this),
                    link = btn.attr('data-purple-url');
                    pageLink  = link.split('?id='),
                    number    = parseInt(pageLink[1]),
                    totalPage = $('#load-media-list').data('purple-page-total'),
                    limit     = $('#load-media-list').data('purple-page-limit'),
                    multiple  = $('#load-media-list').data('purple-multiple'),
                    images    = $("#modal-browse-images").find('.button-select-image').attr('data-purple-image'),
                    data      = {page: number, limit: limit, multiple: multiple, images: images},
                    token     = $('#csrf-ajax-token').val();

                    // console.log(data);

                if (number > 0) {
                    // console.log(number);
                    // console.log(totalPage);

                    if (action == 'prev') {
                        var conditionChecker = function(){ return totalPage / number == totalPage };
                    }
                    else if (action == 'next') {
                        var conditionChecker = function(){ return totalPage - number == 0 };
                    }

                    $.ajax({
                        type: "POST",
                        url:  link,
                        headers : {
                            'X-CSRF-Token': token
                        },
                        data: data,
                        cache: false,
                        beforeSend: function() {
                            $('#load-media-list').append('<div class="uk-overlay-default uk-position-cover selected-overlay">' +
                                '<div class="uk-position-center">' +
                                    '<div uk-spinner></div>' +
                                '</div>' +
                             '</div>');
                        },
                        success: function(data) {
                            $("#modal-browse-images").find('button').removeAttr('disabled');

                            $('#load-media-list').find(".selected-overlay").remove();
                            reverseParent.removeClass('disabled');

                            if (action == 'prev') {
                                browseImagePaging('next');
                            }
                            else if (action == 'next') {
                                browseImagePaging('prev');
                            }

                            var newPrevPage = number - 1;
                            var newNextPage = number + 1;

                            if (action == 'prev') {
                                target.attr('data-purple-url', pageLink[0] + '?id=' + newPrevPage);
                                reverseTarget.attr('data-purple-url', pageLink[0] + '?id=' + newNextPage);
                            }
                            else if (action == 'next') {
                                target.attr('data-purple-url', pageLink[0] + '?id=' + newNextPage);
                                reverseTarget.attr('data-purple-url', pageLink[0] + '?id=' + newPrevPage);
                            }

                            if (conditionChecker()) {
                                reverseParent.show();

                                parent.addClass('disabled');
                                parent.hide();
                                target.on("click", function() {
                                    return false;
                                })
                            }
                            else {
                                parent.show();
                                reverseParent.show();

                                reverseParent.removeClass('disabled');
                                browseImagePaging(action);
                            }
                            $('#load-media-list').html(data);
                        }
                    })
                }
            }

            return false;
        })
        
    }

    browseImagePaging('prev');
    browseImagePaging('next');

    /**
     *
     * Delete Button
     *
     */

    deleteButton = function(something) {
        $(".button-delete-" + something).on("click", function() {
            var btn         = $(this),
                id          = btn.data('purple-id'),
                title       = btn.data('purple-name'),
                deleteModal = $("#modal-delete-" + something),
                deleteForm  = deleteModal.find("form"),
                deleteID    = deleteForm.find("input[name=id]"),
                deleteTitle = deleteForm.find(".bind-title");

            deleteID.val(id);
            deleteTitle.html(title);

            UIkit.modal('#modal-delete-' + something).show();
            return false;
        })
    }
})