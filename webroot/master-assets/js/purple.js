(function ($) {
    'use strict';
    $(function () {
        /**
         *
         * Froala Blocks
         *
         */
        function fullscreenMode() {
            function toggleFullscreen1() {
                $(this).attr('data-purple-active', 'yes');
                $(this).addClass('active');
                $("#purple-fdb-blocks-preview").removeClass('uk-width-2-3');
                $("#purple-fdb-blocks-preview").addClass('fdb-fullscreen-mode');
                $("#purple-fdb-blocks").removeClass('uk-width-1-3');
                $("#purple-fdb-blocks").addClass('fdb-selector-fullscreen-mode');
                $("#purple-fdb-blocks-preview .uk-card-header").removeAttr('uk-sticky');
                $(".preview-screen-modifier").show();
                $("html").css('overflow', 'hidden');

                $("#button-toggle-desktop-screen").click(function() {
                    $("#bind-fdb-blocks").animate({width: "100%"});
                    return false;
                })

                $("#button-toggle-tablet-screen").click(function() {
                    UIkit.modal('#modal-device-screen').hide();

                    var html       = $("#bind-fdb-blocks").html();
                    var previewUrl = $(this).data('purple-page');
                    $("#modal-device-screen").find('.uk-modal-dialog').css('width', '768px');
                    $("#modal-device-screen").find('.uk-modal-body').html('<div class="text-center uk-padding-large"><div uk-spinner="ratio: 2"></div></div>');
                    $("#modal-device-screen").find('.screen-type').html('Tablet');
                    setTimeout(function() {
                        UIkit.modal('#modal-device-screen').show();
                    }, 1000);
                    setTimeout(function() {
                        $("#modal-device-screen").find('.uk-modal-body').html('<object width="100%" height="550" data="'+previewUrl+'"></object>');
                    }, 2000);
                    return false;
                })

                $("#button-toggle-phone-screen").click(function() {
                    UIkit.modal('#modal-device-screen').hide();

                    var html       = $("#bind-fdb-blocks").html();
                    var previewUrl = $(this).data('purple-page');
                    $("#modal-device-screen").find('.uk-modal-dialog').css('width', '480px');
                    $("#modal-device-screen").find('.uk-modal-body').html('<div class="text-center uk-padding-large"><div uk-spinner="ratio: 2"></div></div>');
                    $("#modal-device-screen").find('.screen-type').html('Mobile');
                    setTimeout(function() {
                        UIkit.modal('#modal-device-screen').show();
                    }, 1000);
                    setTimeout(function() {
                        $("#modal-device-screen").find('.uk-modal-body').html('<object width="100%" height="500" data="'+previewUrl+'"></object>');
                    }, 2000);
                    return false;
                })

                $(this).one("click", toggleFullscreen2);
            }

            function toggleFullscreen2() {
                $(this).attr('data-purple-active', 'no');
                $(this).removeClass('active');
                $("#purple-fdb-blocks-preview").addClass('uk-width-2-3');
                $("#purple-fdb-blocks-preview").removeClass('fdb-fullscreen-mode');
                $("#purple-fdb-blocks-preview").removeAttr('style');
                $("#purple-fdb-blocks").addClass('uk-width-1-3');
                $("#purple-fdb-blocks").removeClass('fdb-selector-fullscreen-mode');
                $("#purple-fdb-blocks-preview .uk-card-header").attr('uk-sticky', 'offset: 70');
                $(".preview-screen-modifier").hide();
                $("#bind-fdb-blocks").removeAttr('style');
                $("html").css('overflow', 'auto');
                $(this).one("click", toggleFullscreen1);
            }
            $("#button-toggle-fullscreen").one("click", toggleFullscreen1);

            return $("#button-toggle-fullscreen").attr('data-purple-active');
        }

        fullscreenMode();

        function editingMode() {
            function toggleEditing1() {
                $(this).attr('data-purple-active', 'yes');
                $(this).addClass('active');
                $("#button-toggle-tuning").attr('data-purple-active', 'no');
                $("#button-toggle-tuning").removeClass('active');
                $('.fdb-blocks-mode').html('<small>Editing Mode</small>');
                $('#button-save-page, .fdb-button-option-divider').hide();
                console.log('toggleEditing1');
                $(this).one("click", toggleEditing2);
            }

            function toggleEditing2() {
                $(this).attr('data-purple-active', 'no');
                $(this).removeClass('active');
                $("#button-toggle-tuning").attr('data-purple-active', 'yes');
                $("#button-toggle-tuning").addClass('active');
                $('body .fdb-editor').froalaEditor('destroy');
                $('.fdb-blocks-mode').html('<small>Tuning Mode</small>');
                $('#button-save-page, .fdb-button-option-divider').show();
                console.log('toggleEditing2');
                $(this).one("click", toggleEditing1);
            }
            $("#button-toggle-editing").one("click", toggleEditing1);

            return $("#button-toggle-editing").attr('data-purple-active');
        }

        editingMode();

        function tuningMode() {
            function toggleTuning1() {
                $(this).attr('data-purple-active', 'yes');
                $(this).addClass('active');
                $("#button-toggle-editing").attr('data-purple-active', 'no');
                $("#button-toggle-editing").removeClass('active');
                $('body .fdb-editor').froalaEditor('destroy');
                $('.fdb-blocks-mode').html('<small>Tuning Mode</small>');
                $('#button-save-page, .fdb-button-option-divider').show();
                console.log('toggleTuning1');
                $(this).one("click", toggleTuning2);
            }

            function toggleTuning2() {
                $(this).attr('data-purple-active', 'no');
                $(this).removeClass('active');
                $("#button-toggle-editing").attr('data-purple-active', 'yes');
                $("#button-toggle-editing").addClass('active');
                $('.fdb-blocks-mode').html('<small>Editing Mode</small>');
                $('#button-save-page, .fdb-button-option-divider').hide();
                console.log('toggleTuning2');
                $(this).one("click", toggleTuning1);
            }
            $("#button-toggle-tuning").one("click", toggleTuning1);

            return $("#button-toggle-tuning").attr('data-purple-active');
        };

        tuningMode();

        function formatFactory(html) {
            function parse(html, tab = 0) {
                var tab;
                var html = $.parseHTML(html, null, true);
                var formatHtml = new String();

                function setTabs () {
                    var tabs = new String(),
                        i;

                    for (i=0; i < tab; i++){
                      tabs += '\t';
                    }
                    return tabs;
                };


                $.each( html, function( i, el ) {
                    if (el.nodeName == '#text') {
                        if (($(el).text().trim()).length) {
                            formatHtml += setTabs() + $(el).text().trim() + '\n';
                        }
                    } else {
                        var innerHTML = $(el).html().trim();
                        $(el).html(innerHTML.replace('\n', '').replace(/ +(?= )/g, ''));


                        if ($(el).children().length) {
                            $(el).html('\n' + parse(innerHTML, (tab + 1)) + setTabs());
                            var outerHTML = $(el).prop('outerHTML').trim();
                            formatHtml += setTabs() + outerHTML + '\n';

                        } else {
                            var outerHTML = $(el).prop('outerHTML').trim();
                            formatHtml += setTabs() + outerHTML + '\n';
                        }
                    }
                });

                return formatHtml;
            };

            return parse(html.replace(/(\r\n|\n|\r)/gm," ").replace(/ +(?= )/g,''));
        };

        $("#button-toggle-code").click(function() {
            if(tuningMode() == 'yes') {
                var modal     = $(this).data('purple-modal'),
                    html      = $("#bind-fdb-blocks").html(),
                    beautify  = formatFactory(html),
                    url       = $(this).data('purple-url'),
                    id        = $(this).data('purple-id'),
                    actionUrl = $(this).data('purple-actionurl'),
                    redirect  = $(this).data('purple-redirect'),
                    token     = $('#csrf-ajax-token').val();

                $.ajax({
                    type: "POST",
                    url:  url,
                    headers : {
                        'X-CSRF-Token': token
                    },
                    data: { id:id, url:actionUrl, redirect:redirect, html:beautify },
                    cache: false,
                    beforeSend: function() {
                    },
                    success: function(data) {
                        UIkit.modal(modal).show();
                        $(modal).find('.uk-modal-body').html('<div class="text-center uk-padding-large"><div uk-spinner="ratio: 2"></div></div>');
                        setTimeout(function() {
                            $(modal).find('.uk-modal-body').html(data);
                        }, 1500);
                    }
                })
            }
            else {
                alert('Please enter tuning mode before editing source code.');
            }

            return false;
        })

        $('.fdb-block').on({
            mouseenter: function () {
                if(tuningMode() == 'yes') {
                    $(this).addClass('fdb-block-selected');
                    var id = $(this).attr('data-fdb-id');

                    if ($(this).hasClass('fdb-uikit-slider-tuning')) {
                        var sliderTuning = '<a id="fdb-block-menu-button-slidertuning-'+id+'" class="fdb-block-menu-button-slidertuning" data-fdb-id="'+id+'" uk-tooltip="title: Change Slider Options"><i class="mdi mdi-tune" id=""></i></a>';
                    }
                    else {
                        var sliderTuning = '';
                    }

                    var menuBtn = '<div class="fdb-block-menu-button">' + 
                        '<a id="fdb-block-menu-button-move-'+id+'" class="fdb-block-menu-button-move uk-sortable-handle" data-fdb-id="'+id+'" uk-tooltip="title: Move Block"><i class="mdi mdi-cursor-move" id=""></i></a>' + 
                        '<a id="fdb-block-menu-button-image-'+id+'" class="fdb-block-menu-button-image button-browse-images" data-fdb-id="'+id+'" data-purple-browse-action="froala-section-bg" uk-tooltip="title: Change Background Image"><i class="mdi mdi-image" id=""></i></a>' + 
                        '<a id="fdb-block-menu-button-bgcolor-'+id+'" class="fdb-block-menu-button-bgcolor" data-fdb-id="'+id+'" data-fdb-bg="section" uk-tooltip="title: Change Background Color"><i class="mdi mdi-format-color-fill" id=""></i></a>' +
                        '<a id="fdb-block-menu-button-animation-'+id+'" class="fdb-block-menu-button-animation" data-fdb-id="'+id+'" uk-tooltip="title: Add Animation on Scroll"><i class="mdi mdi-animation" id=""></i></a>' + 
                        sliderTuning +
                        '<a id="fdb-block-menu-button-movedown-'+id+'" class="fdb-block-menu-button-delete" data-fdb-id="'+id+'" uk-tooltip="title: Delete Block"><i class="mdi mdi-delete" id=""></i></a>' +
                    '</div>';

                    $(this).append(menuBtn);

                    UIkit.sortable('#bind-fdb-blocks', {
                        handle: '.uk-sortable-handle'
                    });

                    var browseImageBtn        = browseImageButton();
                    var changeBgcolorBtn      = changeBackgroundColor();
                    var changeUikitSliderBtn  = changeUikitSliderOption();
                    var moveSliderCaptionBtn  = moveSliderCaption();
                    var addUikitAnimationBtn  = addUikitAnimationBlock();
                    var fontAwesomeIconBtn    = changeFontAwesomeIcon();
                    var buttonsCustomizingBtn = buttonsCustomizing();

                    $('.fdb-block-menu-button-delete').click(function() {
                        var btn = $(this),
                            idBtn  = btn.data('fdb-id'),
                            block = $('#fdb-'+idBtn);

                        block.remove();

                        return false;
                    })
                }
            },
            mouseleave: function () {
                $(this).removeClass('fdb-block-selected');
                $(this).find('.fdb-block-menu-button').remove();
            }
        })

        $(".fdb-block-bgcolor").on({
                mouseenter: function () {
                    if(tuningMode() == 'yes') {
                        $(this).addClass('fdb-block-selected');

                        var menuBtn = '<div class="fdb-block-menu-button-right"><a id="fdb-block-menu-button-bgcolor-'+id+'" class="fdb-block-menu-button-bgcolor" data-fdb-id="'+id+'" data-fdb-bg="block" uk-tooltip="title: Change Background Color"><i class="mdi mdi-format-color-fill" id=""></i></a></div>';

                        $(this).append(menuBtn);

                        var changeBgcolorBtn = changeBackgroundColor();
                    }
                },
                mouseleave: function () {
                    $(this).removeClass('fdb-block-selected');
                    $(this).find('.fdb-block-menu-button-right').remove();
                }
        });

        $('.fdb-block-background').on({
                mouseenter: function () {
                    if(tuningMode() == 'yes') {
                        $(this).addClass('fdb-block-selected');

                        var id = $(this).attr('data-fdb-id');
                        var menuBtn = '<div class="fdb-block-menu-button-right"><a id="fdb-block-menu-button-image-'+id+'" class="fdb-block-menu-button-image button-browse-images" data-fdb-id="'+id+'" data-purple-browse-action="froala-block-bg" uk-tooltip="title: Change Background Image"><i class="mdi mdi-image" id=""></i></a></div>';

                        $(this).append(menuBtn);
                    }
                },
                mouseleave: function () {
                    $(this).removeClass('fdb-block-selected');
                    $(this).find('.fdb-block-menu-button-right').remove();
                }
        });

        function blockCopyInitial() {
            $(".fdb-block-copy").on({
                mouseenter: function () {
                    if(tuningMode() == 'yes') {
                        var copyBlock = $(this);
                        var id = $(this).attr('data-fdb-id');

                        $(this).addClass('fdb-block-selected');

                        var menuBtn = '<div class="fdb-block-menu-button-right"><a id="fdb-block-menu-button-copy-'+id+'" class="fdb-block-menu-button-copy" data-fdb-id="'+id+'" uk-tooltip="title: Copy Block"><i class="mdi mdi-content-copy" id=""></i></a><a id="fdb-block-menu-button-delete-block-'+id+'" class="fdb-block-menu-button-delete-block" data-fdb-id="'+id+'" uk-tooltip="title: Delete Block"><i class="mdi mdi-delete" id=""></i></a></div>';

                        $(this).append(menuBtn);

                        $('.fdb-block-menu-button-copy').click(function() {
                            var btn    = $(this),
                                idBtn  = btn.data('fdb-id'),
                                parent = copyBlock.parent();

                            parent.append(copyBlock.clone());
                            parent.find('.fdb-block-menu-button-right').remove();
                            blockCopyInitial();
                            froalaEditorInitial();

                            return false;
                        })

                        $('.fdb-block-menu-button-delete-block').click(function() {
                            var btn   = $(this),
                                idBtn = btn.data('fdb-id');

                            copyBlock.remove();

                            return false;
                        })
                    }
                },
                mouseleave: function () {
                    $(this).removeClass('fdb-block-selected');
                    $(this).find('.fdb-block-menu-button-right').remove();
                }
            });
        }

        blockCopyInitial();

        function froalaEditorInitial() {
            $('.fdb-editor').on({
                mouseenter: function () {
                    if(editingMode() == 'yes') {
                        var token                  = $("#csrf-ajax-token").val();
                        var froalaManagerLoadUrl   = $("#froala-load-url").val();
                        var froalaImageUploadUrl   = $("#froala-image-upload-url").val();
                        var froalaFileUploadUrl    = $("#froala-file-upload-url").val();
                        var froalaVideoUploadUrl   = $("#froala-video-upload-url").val();

                        $(this).froalaEditor({
                            theme: 'royal',
                            toolbarInline: true,
                            charCounterCount: false,
                            toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', 'color', 'fontSize', '-', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'indent', 'outdent', '-', 'insertImage', 'insertLink', 'insertFile', 'insertVideo', 'undo', 'redo', '-', 'fontAwesome', 'emoticons', 'specialCharacters', 'insertTable', 'insertHR', 'selectAll'],
                            toolbarVisibleWithoutSelection: true,
                            enter: $.FroalaEditor.ENTER_DIV,
                            imageManagerLoadURL: froalaManagerLoadUrl,
                            imageUploadURL: froalaImageUploadUrl,
                            fileUploadURL: froalaFileUploadUrl,
                            videoUploadURL: froalaVideoUploadUrl,
                            imageMaxSize: 3 * 1024 * 1024,
                            imageAllowedTypes: ['jpeg', 'jpg', 'png'],
                            fileMaxSize: 5 * 1024 * 1024,
                            fileAllowedTypes: ['*'],
                            videoMaxSize: 20 * 1024 * 1024,
                            videoAllowedTypes: ['mp4', 'm4v', 'ogg', 'webm'],
                            requestHeaders: {
                                'X-CSRF-Token': token
                            }

                        });
                        $(this).on('froalaEditor.initialized', function (e, editor) {
                            $("section#fdb-"+id).removeClass('fdb-block-selected');
                            $("section#fdb-"+id).find('.fdb-block-menu-button').remove();
                        });
                    }
                },
                mouseleave: function () {
                    //$(this).froalaEditor('destroy');
                }
            });
        }

        froalaEditorInitial();

        $(".fdb-blocks").click(function() {
            var template  = $(this),
                number    = template.data('purple-number'),
                filter    = template.data('purple-filter'),
                url       = template.data('purple-url'),
                urlReload = template.data('purple-urlreload'),
                token     = $('#csrf-ajax-token').val();

            template.find(".selected-overlay").remove();
            template.append('<div class="uk-overlay-default uk-position-cover selected-overlay">' +
                '<div class="uk-position-center">' +
                    '<div uk-spinner></div>' +
                '</div>' +
             '</div>');

            $("#button-save-page").click(function() {
                return false;
            })
            $("#button-save-page").html('<i class="fa fa-circle-o-notch fa-spin"></i>');

            $.ajax({
                type: "POST",
                url:  url,
                headers : {
                    'X-CSRF-Token': token
                },
                data: { number:number, filter:filter },
                cache: false,
                beforeSend: function() {
                },
                success: function(msg) {
                    console.log(msg);

                    $("#button-save-page").html('<i class="mdi mdi-content-save"></i> Save');
                    $("#button-save-page").click(function() {
                        return true;
                    })
                    $('html, body').animate({
                        scrollTop: $("#bottom-of-builder").offset().top
                    }, 1000);
                    
                    var json    = $.parseJSON(msg),
				        status  = (json.status),
                        id      = (json.id),
                        html    = (json.html);

                    if(status == 'ok') {
                        template.find(".selected-overlay").remove();
                        $("#bind-fdb-blocks").find(".fdb-blocks-empty").remove();
                        var html = html.replace(/{bind.id}/g, id);
                        $("#bind-fdb-blocks").append(html);

                        $("section#fdb-"+id).on({
                                mouseenter: function () {
                                    console.log(tuningMode());
                                    if(tuningMode() == 'yes') {
                                        $(this).addClass('fdb-block-selected');

                                        if ($(this).hasClass('fdb-uikit-slider-tuning')) {
                                            var sliderTuning = '<a id="fdb-block-menu-button-slidertuning-'+id+'" class="fdb-block-menu-button-slidertuning" data-fdb-id="'+id+'" uk-tooltip="title: Change Slider Options"><i class="mdi mdi-tune" id=""></i></a>';
                                        }
                                        else {
                                            var sliderTuning = '';
                                        }

                                        var menuBtn = '<div class="fdb-block-menu-button">' +
                                            '<a id="fdb-block-menu-button-move-'+id+'" class="fdb-block-menu-button-move uk-sortable-handle" data-fdb-id="'+id+'" uk-tooltip="title: Move Block"><i class="mdi mdi-cursor-move" id=""></i></a>' +
                                            '<a id="fdb-block-menu-button-image-'+id+'" class="fdb-block-menu-button-image button-browse-images" data-fdb-id="'+id+'" data-purple-browse-action="froala-section-bg" uk-tooltip="title: Change Background Image"><i class="mdi mdi-image" id=""></i></a>' +
                                            '<a id="fdb-block-menu-button-bgcolor-'+id+'" class="fdb-block-menu-button-bgcolor" data-fdb-id="'+id+'" data-fdb-bg="section" uk-tooltip="title: Change Background Color"><i class="mdi mdi-format-color-fill" id=""></i></a>' +
                                            '<a id="fdb-block-menu-button-animation-'+id+'" class="fdb-block-menu-button-animation" data-fdb-id="'+id+'" uk-tooltip="title: Add Animation on Scroll"><i class="mdi mdi-animation" id=""></i></a>' +
                                            sliderTuning +
                                            '<a id="fdb-block-menu-button-movedown-'+id+'" class="fdb-block-menu-button-delete" data-fdb-id="'+id+'" uk-tooltip="title: Delete Block"><i class="mdi mdi-delete" id=""></i></a>' +
                                        '</div>';

                                        $(this).append(menuBtn);

                                        UIkit.sortable('#bind-fdb-blocks', {
                                            handle: '.uk-sortable-handle'
                                        });

                                        var browseImageBtn        = browseImageButton();
                                        var changeBgcolorBtn      = changeBackgroundColor();
                                        var changeUikitSliderBtn  = changeUikitSliderOption();
                                        var moveSliderCaptionBtn  = moveSliderCaption();
                                        var addUikitAnimationBtn  = addUikitAnimationBlock();
                                        var fontAwesomeIconBtn    = changeFontAwesomeIcon();
                                        var buttonsCustomizingBtn = buttonsCustomizing();

                                        $('.fdb-block-menu-button-delete').click(function() {
                                            var btn    = $(this),
                                                idBtn  = btn.data('fdb-id'),
                                                block  = $('#fdb-'+idBtn);

                                            block.remove();

                                            return false;
                                        })
                                    }
                                },
                                mouseleave: function () {
                                    $(this).removeClass('fdb-block-selected');
                                    $(this).find('.fdb-block-menu-button').remove();
                                }
                        });

                        $(".fdb-block-bgcolor").on({
                                mouseenter: function () {
                                    if(tuningMode() == 'yes') {
                                        $(this).addClass('fdb-block-selected');

                                        var menuBtn = '<div class="fdb-block-menu-button-right"><a id="fdb-block-menu-button-bgcolor-'+id+'" class="fdb-block-menu-button-bgcolor" data-fdb-id="'+id+'" data-fdb-bg="block" uk-tooltip="title: Change Background Color"><i class="mdi mdi-format-color-fill" id=""></i></a></div>';

                                        $(this).append(menuBtn);

                                        var changeBgcolorBtn = changeBackgroundColor();
                                    }
                                },
                                mouseleave: function () {
                                    $(this).removeClass('fdb-block-selected');
                                    $(this).find('.fdb-block-menu-button-right').remove();
                                }
                        });

                        $(".fdb-block-background").on({
                                mouseenter: function () {
                                    if(tuningMode() == 'yes') {
                                        $(this).addClass('fdb-block-selected');

                                        var menuBtn = '<div class="fdb-block-menu-button-right"><a id="fdb-block-menu-button-image-'+id+'" class="fdb-block-menu-button-image button-browse-images" data-fdb-id="'+id+'" data-purple-browse-action="froala-block-bg" uk-tooltip="title: Change Background Image"><i class="mdi mdi-image" id=""></i></a></div>';

                                        $(this).append(menuBtn);
                                    }
                                },
                                mouseleave: function () {
                                    $(this).removeClass('fdb-block-selected');
                                    $(this).find('.fdb-block-menu-button-right').remove();
                                }
                        });

                        function blockCopy() {
                            $(".fdb-block-copy").on({
                                mouseenter: function () {
                                    if(tuningMode() == 'yes') {
                                        var copyBlock = $(this);
                                        $(this).addClass('fdb-block-selected');

                                        var menuBtn = '<div class="fdb-block-menu-button-right"><a id="fdb-block-menu-button-copy-'+id+'" class="fdb-block-menu-button-copy" data-fdb-id="'+id+'" uk-tooltip="title: Copy Block"><i class="mdi mdi-content-copy" id=""></i></a><a id="fdb-block-menu-button-delete-block-'+id+'" class="fdb-block-menu-button-delete-block" data-fdb-id="'+id+'" uk-tooltip="title: Delete Block"><i class="mdi mdi-delete" id=""></i></a></div>';

                                        $(this).append(menuBtn);

                                        $('.fdb-block-menu-button-copy').click(function() {
                                            var btn    = $(this),
                                                idBtn  = btn.data('fdb-id'),
                                                parent = copyBlock.parent();

                                            console.log(html);

                                            parent.append(copyBlock.clone());
                                            parent.find('.fdb-block-menu-button-right').remove();
                                            blockCopy();
                                            froalaEditorInitial();

                                            return false;
                                        })

                                        $('.fdb-block-menu-button-delete-block').click(function() {
                                            var btn   = $(this),
                                                idBtn = btn.data('fdb-id');

                                            copyBlock.remove();

                                            return false;
                                        })
                                    }
                                },
                                mouseleave: function () {
                                    $(this).removeClass('fdb-block-selected');
                                    $(this).find('.fdb-block-menu-button-right').remove();
                                    $(this).find('img').removeClass('img-to-change-src');
                                }
                            });
                        }

                        blockCopy();

                        $("section#fdb-"+id).find('.fdb-editor').on({
                                mouseenter: function () {
                                    console.log(editingMode());
                                    if(editingMode() == 'yes') {
                                        var token                  = $("#csrf-ajax-token").val();
                                        var froalaManagerLoadUrl   = $("#froala-load-url").val();
                                        var froalaImageUploadUrl   = $("#froala-image-upload-url").val();
                                        var froalaFileUploadUrl    = $("#froala-file-upload-url").val();
                                        var froalaVideoUploadUrl   = $("#froala-video-upload-url").val();

                                        $(this).froalaEditor({
                                            theme: 'royal',
                                            toolbarInline: true,
                                            charCounterCount: false,
                                            toolbarButtons: ['bold', 'italic', 'underline', 'fontFamily', 'color', 'fontSize', '-', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'indent', 'outdent', '-', 'insertImage', 'insertLink', 'insertFile', 'insertVideo', 'undo', 'redo', '-', 'fontAwesome', 'emoticons', 'specialCharacters', 'insertTable', 'insertHR', 'selectAll'],
                                            toolbarVisibleWithoutSelection: true,
                                            enter: $.FroalaEditor.ENTER_DIV,
                                            imageManagerLoadURL: froalaManagerLoadUrl,
                                            imageUploadURL: froalaImageUploadUrl,
                                            fileUploadURL: froalaFileUploadUrl,
                                            videoUploadURL: froalaVideoUploadUrl,
                                            imageMaxSize: 3 * 1024 * 1024,
                                            imageAllowedTypes: ['jpeg', 'jpg', 'png'],
                                            fileMaxSize: 5 * 1024 * 1024,
                                            fileAllowedTypes: ['*'],
                                            videoMaxSize: 20 * 1024 * 1024,
                                            videoAllowedTypes: ['mp4', 'm4v', 'ogg', 'webm'],
                                            requestHeaders: {
                                                'X-CSRF-Token': token
                                            }

                                        })
                                          .on('froalaEditor.file.uploaded', function (e, editor, response) {
                                            // File was uploaded to the server.
                                            console.log(response);
                                          })
                                          .on('froalaEditor.file.inserted', function (e, editor, $file, response) {
                                            // File was inserted in the editor.
                                            console.log(response);
                                          })
                                          .on('froalaEditor.file.error', function (e, editor, error, response) {
                                            // Bad link.
                                            console.log(response);
                                            console.log(error);
                                          })

                                          .on('froalaEditor.video.uploaded', function (e, editor, response) {
                                            // File was uploaded to the server.
                                            console.log(response);
                                          })
                                          .on('froalaEditor.video.inserted', function (e, editor, $file, response) {
                                            // File was inserted in the editor.
                                            console.log(response);
                                          })
                                          .on('froalaEditor.video.error', function (e, editor, error, response) {
                                            // Bad link.
                                            console.log(response);
                                            console.log(error);
                                          })

                                          .on('froalaEditor.image.uploaded', function (e, editor, response) {
                                            // File was uploaded to the server.
                                            console.log(response);
                                          })
                                          .on('froalaEditor.image.inserted', function (e, editor, $file, response) {
                                            // File was inserted in the editor.
                                            console.log(response);
                                          })
                                          .on('froalaEditor.image.error', function (e, editor, error, response) {
                                            // Bad link.
                                            console.log(response);
                                            console.log(error);
                                          })

                                        $(this).on('froalaEditor.initialized', function (e, editor) {
                                            $("section#fdb-"+id).removeClass('fdb-block-selected');
                                            $("section#fdb-"+id).find('.fdb-block-menu-button').remove();
                                        });
                                    }
                                    else {
                                        $(this).click(function() {
                                            return false;
                                        })
                                    }
                                },
                                mouseleave: function () {
//                                        $(this).froalaEditor('destroy');
                                }
                        });
                    }
                    else {
                        alert('Oops. Something error. Please try again.');
                    }
                }
            })

            return false;
        })

        $("#button-toggle-blocks").click(function() {
            if($('#purple-fdb-blocks:visible').length) {
                $('#purple-fdb-blocks').hide();
                $('#purple-fdb-blocks-preview').removeClass('uk-width-2-3');
                $('#purple-fdb-blocks-preview').addClass('uk-width-1-1');
                if($('#purple-fdb-blocks-preview').hasClass('fdb-fullscreen-mode')) {
                    $('#purple-fdb-blocks-preview').css('width', '100%');
                    $('#purple-fdb-blocks-preview').css('left', '0');
                    $(".preview-screen-modifier").show();
                }
                else {
                    $(".preview-screen-modifier").hide();
                }
            }
            else {
                $('#purple-fdb-blocks').show();
                $('#purple-fdb-blocks-preview').removeClass('uk-width-1-1');
                $('#purple-fdb-blocks-preview').addClass('uk-width-2-3');
                if($('#purple-fdb-blocks-preview').hasClass('fdb-fullscreen-mode')) {
                    $('#purple-fdb-blocks-preview').css('width', '75%');
                    $('#purple-fdb-blocks-preview').css('left', '25%');
                    $(".preview-screen-modifier").show();
                }
                else {
                    $(".preview-screen-modifier").hide();
                }
            }
        });

        /**
         *
         * Purple Media / Images, Documents, Video
         *
         */
        $(".button-download-media").click(function () {
            var btn  = $(this),
                url  = btn.data('purple-url'),
                name = btn.data('purple-name')

            var fileDownload = fileDownloader(url, name);
            return false;
        })

        $(".media-link-to-image").click(function () {
            var id      = $(this).data('purple-id'),
                image   = $(this).data('purple-image'),
                host    = $(this).data('purple-host'),
                by      = $(this).data('purple-by'),
                created = $(this).data('purple-created'),
                desc    = $(this).data('purple-description'),
                title   = $(this).attr('title'),
                modal   = $("#modal-full-content");
            modal.find(".uk-background-contain").css('background-image', 'url(' + image + ')');
            modal.find("form input[name=id]").val(id);
            modal.find("form input[name=title]").val(title);
            modal.find("form textarea[name=description]").val(desc);
            modal.find("form input[name=path]").val(host + image);
            modal.find(".bind-created").html('Uploaded at ' + created);
            modal.find(".bind-by").html('Uploaded by ' + by);
            modal.find("form .button-delete-media-image").attr('data-id', id);
            UIkit.modal('#modal-full-content').show();

            var clipboard   = new ClipboardJS('#button-clipboard-js'),
                targetLabel = modal.find("form label[for=path]").html();

            clipboard.on('success', function(e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
                modal.find("form label[for=path]").html('URL <span class="text-primary">Copied</span>');
                setTimeout(function() {
                    modal.find("form label[for=path]").html('URL');
                }, 2500);
                e.clearSelection();
            });

            clipboard.on('error', function(e) {
                console.error('Action:', e.action);
                console.error('Trigger:', e.trigger);
                modal.find("form label[for=path]").html('URL <span class="text-danger">Error. Text is not copied</span>');
                setTimeout(function() {
                    modal.find("form label[for=path]").html('URL');
                }, 2500);
            });


            modal.find("form .button-delete-media-image").click(function() {
                UIkit.modal('#modal-full-content').hide();
                setTimeout(function() {
                    var deleteModal = $("#modal-delete-media"),
                        deleteForm  = deleteModal.find("form"),
                        deleteID    = deleteForm.find("input[name=id]"),
                        deleteTitle = deleteForm.find(".bind-title");

                    deleteID.val(id);
                    deleteTitle.html(title);

                    UIkit.modal('#modal-delete-media').show();
                }, 500);
                return false;
            });

            return false;
        });

        $('#collapse-uploader-result').on('hidden.bs.collapse', function () {
            $("#button-toggle-progress").html('<i class="mdi mdi-format-list-bulleted-type btn-icon-prepend"></i> Show Progress');
        });

        $('#collapse-uploader-result').on('show.bs.collapse', function () {
            $("#button-toggle-progress").html('<i class="mdi mdi-format-list-bulleted-type btn-icon-prepend"></i> Hide Progress');
        });

        $(".button-edit-media").click(function () {
            var id      = $(this).data('purple-id'),
                file    = $(this).data('purple-file'),
                host    = $(this).data('purple-host'),
                by      = $(this).data('purple-by'),
                created = $(this).data('purple-created'),
                desc    = $(this).data('purple-description'),
                title   = $(this).data('purple-title'),
                modal   = $("#modal-edit-media");
            modal.find("form input[name=id]").val(id);
            modal.find("form input[name=title]").val(title);
            modal.find("form textarea[name=description]").val(desc);
            modal.find("form input[name=path]").val(host + file);
            modal.find(".bind-created").html('Uploaded at ' + created);
            modal.find(".bind-by").html('Uploaded by ' + by);
            UIkit.modal('#modal-edit-media').show();

            var clipboard   = new ClipboardJS('#button-clipboard-js'),
                targetLabel = modal.find("form label[for=path]").html();

            clipboard.on('success', function(e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
                modal.find("form label[for=path]").html(targetLabel + ' <span class="text-primary">Copied</span>');

                e.clearSelection();
            });

            clipboard.on('error', function(e) {
                console.error('Action:', e.action);
                console.error('Trigger:', e.trigger);
                modal.find("form label[for=path]").html(targetLabel + ' <span class="text-danger">Error. Text is not copied</span>');

            });

            return false;
        });

        $(".button-delete-media").click(function() {
            var btn         = $(this),
                id          = btn.data('purple-id'),
                title       = btn.data('purple-name'),
                deleteModal = $("#modal-delete-media"),
                deleteForm  = deleteModal.find("form"),
                deleteID    = deleteForm.find("input[name=id]"),
                deleteTitle = deleteForm.find(".bind-title");

            deleteID.val(id);
            deleteTitle.html(title);

            UIkit.modal('#modal-delete-media').show();
            return false;
        })

        /**
         *
         * Purple Settings / General, Email, SEO, Coming Soon
         *
         */
        $(".button-link-to-modal-setting").click(function () {
            var btn      = $(this),
                btnTxt   = $(this).html(),
                id       = $(this).data('purple-id'),
                target   = $(this).data('purple-target'),
                modal    = $($(this).data('purple-target')),
                title    = $(this).data('purple-title'),
                url      = $(this).data('purple-url'),
                redirect = $(this).data('purple-redirect'),
                token    = $('#csrf-ajax-token').val();

            btn.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
            btn.attr('disabled','disabled');

            $.ajax({
                type: "POST",
                url:  url,
                headers : {
                    'X-CSRF-Token': token
                },
                data: { id:id, title:title, redirect:redirect },
                cache: false,
                beforeSend: function(){
                },
                success: function(data){
                    modal.find('#load-edit-settings').html(data);
                    modal.find('.uk-modal-title').html(title);
                    modal.find('input[name=id]').val(id);
                    modal.find('input[name=redirect]').val(redirect);

                    UIkit.modal(modal).show();
                    btn.removeAttr('disabled');
                    modal.find('input[name=value], textarea[name=value], select[name=value]').focus();
                    btn.html(btnTxt);
                }
            })

            return false;
        })
	});
})(jQuery);