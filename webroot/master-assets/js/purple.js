(function ($) {
    'use strict';
    $(function () {
        /**
         *
         * Froala Blocks
         *
         */

        function treePanelContent() {
            var currentPage = window.location.href,
                checkUrl    = "pages/general"
            if (currentPage.indexOf(checkUrl) !== -1) {
                // Show spinner and make tree panel transparent
                $('.btn-spinner-tree-panel').removeClass('uk-invisible');
                $("#tree-panel").css('opacity', '.5');

                // Add an identifier to all element inside #bind-fdb-blocks
                $("#bind-fdb-blocks").find('*').each(function() {
                    var random = Math.floor((Math.random() * 100000000000) + 1);
                    $(this).attr('data-tree-id', random);
                })

                $("#bind-fdb-blocks").find('style').removeAttr('data-tree-id');
                $("#bind-fdb-blocks").find('svg').removeAttr('data-tree-id');
                $("#bind-fdb-blocks").find('polyline').removeAttr('data-tree-id');

                var blockHtml = $('#bind-fdb-blocks').html();
                
                if ($('.fdb-blocks-empty').length == 0) {
                    var json = html2json(blockHtml);
                    var jsonString  = JSON.stringify(json);
                    var jsonString2 = JSON.stringify(json, ' ', '  ');

                    var cssPath = $('#tree-css-path').get(0).outerHTML,
                        jsPath  = $('#tree-js-path').get(0).outerHTML,
                        data  = { htmldata:jsonString, css:cssPath, js:jsPath },
                        url   = $("#tree-panel").data('purple-url'),
                        token = $('#csrf-ajax-token').val();
                        

                    $.ajax({
                        type: "POST",
                        url:  url,
                        headers : {
                            'X-CSRF-Token': token
                        },
                        data: data,
                        cache: false,
                        beforeSend: function() {
                            // Show spinner and make tree panel transparent
                        $('.btn-spinner-tree-panel').removeClass('uk-invisible');
                        $("#tree-panel").css('opacity', '.5');
                        },
                        success: function(data) {
                            // Hide spinner and make tree panel solid
                            $('.btn-spinner-tree-panel').addClass('uk-invisible');
                            $("#tree-panel").css('opacity', '1');

                            var json    = $.parseJSON(data),
                                status  = (json.status),
                                content = (json.content);

                            if (status == 'ok') {
                                $("#tree-panel").addClass('uk-height-medium');
                                $("#tree-panel").css('overflow-y', 'scroll');
                                $("#tree-panel").css('overflow-x', 'scroll');
                                $("#tree-panel").html(content);
                                $('#tree-panel').removeClass('uk-padding-remove');

                                $('#tree-panel').jstree({
                                    "core" : {
                                        "themes" : {
                                            "stripes" : false,
                                        }
                                    },
                                })

                                $('#tree-panel').jstree("destroy");

                                $("#tree-panel").html(content);
                                $('#tree-panel').on('hover_node.jstree', function (e, data) {
                                    var oElement = $("#" + data.node.id)[0];
                                    var treeId   = oElement.attributes["data-purple-tree-id"].value;
                                    $("#bind-fdb-blocks").find('*').removeClass('tree-selected-node');
                                    $("#bind-fdb-blocks").find('*[data-tree-id='+treeId+']').addClass('tree-selected-node');
                                    $("#modal-element-properties").find('input[name=element-class]').tagEditor('destroy');
                                }).on('dehover_node.jstree', function (e, data) {
                                    var oElement = $("#" + data.node.id)[0];
                                    var treeId   = oElement.attributes["data-purple-tree-id"].value;
                                    $("#bind-fdb-blocks").find('*').removeClass('tree-selected-node');
                                }).on('changed.jstree', function (e, data) {
                                    var oElement        = $("#" + data.node.id)[0];
                                    var propertiesModal = '#modal-element-properties';
                                    var treeId          = oElement.attributes["data-purple-tree-id"].value;
                                    var treeTag         = oElement.attributes["data-purple-tree-tag"].value;
                                    var elId            = oElement.attributes["data-purple-tree-hash"].value;
                                    var elClass         = oElement.attributes["data-purple-tree-class"].value;

                                    // Autocomplete List
                                    if (treeTag == 'a' || treeTag == 'button') {
                                        var autoCompleteList = ["btn-primary","btn-secondary","btn-success","btn-info","btn-warning","btn-danger","btn-link","btn-outline-primary","btn-outline-secondary","btn-outline-sucess","btn-outline-info","btn-outline-warning","btn-outline-danger","btn-lg","btn-sm","btn-block"];
                                    }
                                    else if (treeTag == 'img') {
                                        var autoCompleteList = ["img-fluid","img-thumbnail"];
                                    }
                                    else if (treeTag == 'div') {
                                        var autoCompleteList = ["container","container-fluid","row","col-md-1","col-md-2","col-md-3","col-md-4","col-md-5","col-md-6","col-md-7","col-md-8","col-md-9","col-md-10","col-md-11","col-md-12","col-lg-1","col-lg-2","col-lg-3","col-lg-4","col-lg-5","col-lg-6","col-lg-7","col-lg-8","col-lg-9","col-lg-10","col-lg-11","col-lg-12","col-xl-1","col-xl-2","col-xl-3","col-xl-4","col-xl-5","col-xl-6","col-xl-7","col-xl-8","col-xl-9","col-xl-10","col-xl-11","col-xl-12","col","col-1","col-2","col-3","col-4","col-5","col-6","col-7","col-8","col-9","col-10","col-11","col-12","bg-primary","bg-success","bg-info","bg-warning","bg-danger","bg-inverse"];
                                    }
                                    else if (treeTag == 'p' || treeTag == 'h1' || treeTag == 'h2' || treeTag == 'h3' || treeTag == 'h4' || treeTag == 'h5' || treeTag == 'h6' || treeTag == 'span') {
                                        var autoCompleteList = ["text-lowercase","text-uppercase","text-capitalize","text-muted","text-primary","text-success","text-info","text-warning","text-danger","text-white"];
                                    } 
                                    else {
                                        var autoCompleteList = [];
                                    }

                                    if (elClass != 'empty-class') {
                                        var splitElClass = elClass.split('::');
                                        $(propertiesModal).find('input[name=element-class]').tagEditor({ 
                                            initialTags: splitElClass,
                                            autocomplete: {
                                                delay: 0, 
                                                position: { collision: 'flip' },
                                                source: autoCompleteList
                                            },
                                            maxTags: 50,
                                            placeholder: "Class (Max 50 classes)"
                                        });
                                    }
                                    else {
                                        $(propertiesModal).find('input[name=element-class]').tagEditor({ 
                                            initialTags: ['initial-tag'],
                                            autocomplete: {
                                                delay: 0, 
                                                position: { collision: 'flip' },
                                                source: autoCompleteList
                                            },
                                            maxTags: 50,
                                            placeholder: "Class (Max 50 classes)"
                                        });

                                        $(propertiesModal).find('input[name=element-class]').tagEditor('removeTag', 'initial-tag');
                                    }
                                    $("#bind-fdb-blocks").find('*').removeClass('tree-selected-node');
                                    $("#bind-fdb-blocks").find('*[data-tree-id='+treeId+']').addClass('tree-selected-node');
                                    if (elId != 'empty-id') {
                                        $(propertiesModal).find('input[name=element-id]').val(elId);
                                    }
                                    else {
                                        $(propertiesModal).find('input[name=element-id]').val('');
                                    }

                                    $(propertiesModal).find('#button-element-properties').attr('data-purple-target', treeId);

                                    UIkit.modal(propertiesModal).show();

                                    modifyElementProperties();
                                }).jstree({
                                    "core" : {
                                        "themes" : {
                                            "stripes" : false,
                                        }
                                    },
                                })
                            }
                            else {
                                alert("Can't load tree panel. Please reload the page.");
                            }
                        }
                    })
                }
                else {
                    $('#tree-panel').addClass('uk-padding-remove');
                    $('#tree-panel').html('<div class="tree-panel-empty text-center" uk-alert>Empty Content</div>');
                }
            }
        }

        treePanelContent();

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

                $("#button-toggle-desktop-screen").on("click", function() {
                    $("#bind-fdb-blocks").animate({width: "100%"});
                    return false;
                })

                $("#button-toggle-tablet-screen").on("click", function() {
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

                $("#button-toggle-phone-screen").on("click", function() {
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
                // console.log('toggleEditing1');
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
                // console.log('toggleEditing2');
                $(this).one("click", toggleEditing1);

                treePanelContent();
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
                // console.log('toggleTuning1');
                $(this).one("click", toggleTuning2);

                treePanelContent();
            }

            function toggleTuning2() {
                $(this).attr('data-purple-active', 'no');
                $(this).removeClass('active');
                $("#button-toggle-editing").attr('data-purple-active', 'yes');
                $("#button-toggle-editing").addClass('active');
                $('.fdb-blocks-mode').html('<small>Editing Mode</small>');
                $('#button-save-page, .fdb-button-option-divider').hide();
                // console.log('toggleTuning2');
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

        $("#button-toggle-code").on("click", function() {
            if(tuningMode() == 'yes') {
                $("#hidden-fdb-blocks").html($("#bind-fdb-blocks").html());
                $("#hidden-fdb-blocks").find('*').removeAttr('data-tree-id');

                var modal     = $(this).data('purple-modal'),
                    html      = $("#hidden-fdb-blocks").html(),
                    beautify  = formatFactory(html),
                    url       = $(this).data('purple-url'),
                    id        = $(this).data('purple-id'),
                    actionUrl = $(this).data('purple-actionurl'),
                    redirect  = $(this).data('purple-redirect'),
                    token     = $('#csrf-ajax-token').val();

                $(this).find('i').removeClass('mdi mdi-code-tags');
                $(this).find('i').addClass('fa fa-circle-o-notch fa-spin');

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
                            $("#button-toggle-code").find('i').attr('class', 'mdi mdi-code-tags');
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
                        '<a id="fdb-block-menu-button-saveblock-'+id+'" class="fdb-block-menu-button-saveblock" data-fdb-id="'+id+'" uk-tooltip="title: Save Block"><i class="mdi mdi-content-save-settings" id=""></i></a>' +
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
                    var saveBlockToFileBtn    = saveBlockToFile();

                    $('.fdb-block-menu-button-delete').on("click", function() {
                        var btn = $(this),
                            idBtn  = btn.data('fdb-id'),
                            block = $('#fdb-'+idBtn);

                        block.remove();

                        setTimeout(function() {
                            treePanelContent();
                        }, 1000);

                        return false;
                    })
                }
            },
            mouseleave: function () {
                $(this).removeClass('fdb-block-selected');
                $(this).find('.fdb-block-menu-button').remove();

                setTimeout(function() {
                    treePanelContent();
                }, 1000);
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
                        if ($(this).hasClass('uk-block-filter')) {
                            $(this).addClass('uk-block-filter-selected');

                            var min    = 10000; 
                            var max    = 99999;  
                            var random = Math.floor(Math.random() * (+max - +min)) + +min;
                            if ($(this).hasClass('uk-filter-hovered')) {
                            }
                            else {
                                $(this).attr('data-filter-id', random);
                            }

                            var currentId  = $(this).attr('uk-filter-control');
                            var replaceId1 = currentId.replace("[data-category='", '');
                            var replaceId2 = replaceId1.replace("']", '');

                            var moveBtn   = '<a id="fdb-block-menu-button-move-'+id+'" class="fdb-block-menu-button-move uk-sortable-handle-filter" data-fdb-id="'+id+'" uk-tooltip="title: Move Filter"><i class="mdi mdi-cursor-move" id=""></i></a>';
                            var filterBtn = moveBtn + '<a id="fdb-block-uk-filter-list-button-'+id+'" class="fdb-block-uk-filter-list-button" data-current-filter="'+replaceId2+'" data-fdb-id="'+id+'" uk-tooltip="title: Set Filter ID"><i class="mdi mdi-settings" id=""></i></a>';
                            var filterItemBtn = '';

                            $(this).addClass('uk-filter-hovered');
                        }
                        else if ($(this).hasClass('uk-block-filter-item')) {
                            var min    = 10000; 
                            var max    = 99999;  
                            var random = Math.floor(Math.random() * (+max - +min)) + +min;
                            if ($(this).hasClass('uk-filter-item-hovered')) {
                            }
                            else {
                                $(this).attr('data-filter-item', random);
                            }

                            var currentFilter = $(this).attr('data-category');

                            var moveBtn       = '<a id="fdb-block-menu-button-move-'+id+'" class="fdb-block-menu-button-move uk-sortable-handle-filter-item" data-fdb-id="'+id+'" uk-tooltip="title: Move Item"><i class="mdi mdi-cursor-move" id=""></i></a>';
                            if ($(this).hasClass('uk-sortable-lightbox')) {
                                var filterItemBtn = moveBtn;
                            }
                            else {
                                var filterItemBtn = moveBtn + '<a id="fdb-block-uk-filter-item-button-'+id+'" class="fdb-block-uk-filter-item-button" data-current-filter="'+currentFilter+'" data-fdb-id="'+id+'" uk-tooltip="title: Set Filter Category"><i class="mdi mdi-filter-variant" id=""></i></a>';
                            }
                            var filterBtn     = '';
                        }
                        else {
                            var filterBtn     = '';
                            var filterItemBtn = '';
                        }

                        var menuBtn = '<div class="fdb-block-menu-button-right">'+filterBtn+filterItemBtn+'<a id="fdb-block-menu-button-copy-'+id+'" class="fdb-block-menu-button-copy" data-fdb-id="'+id+'" uk-tooltip="title: Copy Block"><i class="mdi mdi-content-copy" id=""></i></a><a id="fdb-block-menu-button-delete-block-'+id+'" class="fdb-block-menu-button-delete-block" data-fdb-id="'+id+'" uk-tooltip="title: Delete Block"><i class="mdi mdi-delete" id=""></i></a></div>';

                        $(this).append(menuBtn);

                        var setUikitFilterIdBtn       = setUikitFilterId();
                        var setUikitFilterCategoryBtn = setUikitFilterCategory();

                        $('.fdb-block-menu-button-copy').on("click", function() {
                            var btn    = $(this),
                                idBtn  = btn.data('fdb-id'),
                                parent = copyBlock.parent();

                            btn.closest('li').removeClass('uk-filter-hovered');
                            btn.closest('li').removeClass('uk-filter-item-hovered');
                            parent.append(copyBlock.clone());
                            parent.find('.fdb-block-menu-button-right').remove();
                            blockCopyInitial();
                            froalaEditorInitial();

                            return false;
                        })

                        $('.fdb-block-menu-button-delete-block').on("click", function() {
                            var btn   = $(this),
                                idBtn = btn.data('fdb-id');

                            copyBlock.remove();

                            setTimeout(function() {
                                treePanelContent();
                            }, 1000);

                            return false;
                        })
                    }
                },
                mouseleave: function () {
                    $(this).removeClass('fdb-block-selected');
                    if ($(this).hasClass('uk-block-filter')) {
                        $(this).removeClass('uk-block-filter-selected');
                    }
                    $(this).find('.fdb-block-menu-button-right').remove();
                    setTimeout(function() {
                        treePanelContent();
                    }, 1000);
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
                    setTimeout(function() {
                        treePanelContent();
                    }, 1000);
                }
            });
        }

        froalaEditorInitial();

        $(".fdb-blocks").on("click", function() {
            var template  = $(this),
                number    = template.data('purple-number'),
                filter    = template.data('purple-filter'),
                svId      = template.data('purple-id'),
                url       = template.data('purple-url'),
                urlReload = template.data('purple-urlreload'),
                themeUrl  = template.data('purple-theme-webroot'),
                token     = $('#csrf-ajax-token').val();

            template.find(".selected-overlay").remove();
            template.append('<div class="uk-overlay-default uk-position-cover selected-overlay">' +
                '<div class="uk-position-center">' +
                    '<div uk-spinner></div>' +
                '</div>' +
             '</div>');

            $("#button-save-page").on("click", function() {
                return false;
            })
            $("#button-save-page").html('<i class="mdi mdi-spin mdi-loading"></i> Fetching...');

            $.ajax({
                type: "POST",
                url:  url,
                headers : {
                    'X-CSRF-Token': token
                },
                data: { number:number, filter:filter, svId:svId },
                cache: false,
                beforeSend: function() {
                },
                success: function(msg) {
                    $("#button-save-page").html('<i class="mdi mdi-content-save"></i> Save');
                    $("#button-save-page").on("click", function() {
                        return true;
                    })
                    
                    var json    = $.parseJSON(msg),
				        status  = (json.status),
                        id      = (json.id),
                        html    = (json.html);

                    if(status == 'ok') {
                        template.find(".selected-overlay").remove();
                        $("#bind-fdb-blocks").find(".fdb-blocks-empty").remove();
                        var html = html.replace(/{bind.id}/g, id);
                        var html = html.replace(/{theme.webroot}/g, themeUrl);
                        $("#bind-fdb-blocks").append(html);

                        $('html, body').animate({
                            scrollTop: $("section#fdb-"+id).offset().top - 120
                        }, 1000);

                        treePanelContent();

                        $("section#fdb-"+id).on({
                                mouseenter: function () {
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
                                            '<a id="fdb-block-menu-button-saveblock-'+id+'" class="fdb-block-menu-button-saveblock" data-fdb-id="'+id+'" uk-tooltip="title: Save Block"><i class="mdi mdi-content-save-settings" id=""></i></a>' +
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
                                        var saveBlockToFileBtn    = saveBlockToFile();

                                        $('.fdb-block-menu-button-delete').on("click", function() {
                                            var btn    = $(this),
                                                idBtn  = btn.data('fdb-id'),
                                                block  = $('#fdb-'+idBtn);

                                            block.remove();
                                            
                                            setTimeout(function() {
                                                treePanelContent();
                                            }, 1000);

                                            return false;
                                        })
                                    }
                                },
                                mouseleave: function () {
                                    $(this).removeClass('fdb-block-selected');
                                    $(this).find('.fdb-block-menu-button').remove();

                                    setTimeout(function() {
                                        treePanelContent();
                                    }, 1000);
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
                                        var id = $(this).attr('data-fdb-id');

                                        $(this).addClass('fdb-block-selected');
                                        if ($(this).hasClass('uk-block-filter')) {
                                            $(this).addClass('uk-block-filter-selected');

                                            var min    = 10000; 
                                            var max    = 99999;  
                                            var random = Math.floor(Math.random() * (+max - +min)) + +min;
                                            if ($(this).hasClass('uk-filter-hovered')) {
                                            }
                                            else {
                                                $(this).attr('data-filter-id', random);
                                            }

                                            var currentId  = $(this).attr('uk-filter-control');
                                            var replaceId1 = currentId.replace("[data-category='", '');
                                            var replaceId2 = replaceId1.replace("']", '');

                                            var moveBtn   = '<a id="fdb-block-menu-button-move-'+id+'" class="fdb-block-menu-button-move uk-sortable-handle-filter" data-fdb-id="'+id+'" uk-tooltip="title: Move Filter"><i class="mdi mdi-cursor-move" id=""></i></a>';
                                            var filterBtn = moveBtn + '<a id="fdb-block-uk-filter-list-button-'+id+'" class="fdb-block-uk-filter-list-button" data-current-filter="'+replaceId2+'" data-fdb-id="'+id+'" uk-tooltip="title: Set Filter ID"><i class="mdi mdi-settings" id=""></i></a>';
                                            var filterItemBtn = '';

                                            $(this).addClass('uk-filter-hovered');
                                        }
                                        else if ($(this).hasClass('uk-block-filter-item')) {
                                            var min    = 10000; 
                                            var max    = 99999;  
                                            var random = Math.floor(Math.random() * (+max - +min)) + +min;
                                            if ($(this).hasClass('uk-filter-item-hovered')) {
                                            }
                                            else {
                                                $(this).attr('data-filter-item', random);
                                            }

                                            var currentFilter = $(this).attr('data-category');

                                            var moveBtn       = '<a id="fdb-block-menu-button-move-'+id+'" class="fdb-block-menu-button-move uk-sortable-handle-filter-item" data-fdb-id="'+id+'" uk-tooltip="title: Move Item"><i class="mdi mdi-cursor-move" id=""></i></a>';
                                            if ($(this).hasClass('uk-sortable-lightbox')) {
                                                var filterItemBtn = moveBtn;
                                            }
                                            else {
                                                var filterItemBtn = moveBtn + '<a id="fdb-block-uk-filter-item-button-'+id+'" class="fdb-block-uk-filter-item-button" data-current-filter="'+currentFilter+'" data-fdb-id="'+id+'" uk-tooltip="title: Set Filter Category"><i class="mdi mdi-filter-variant" id=""></i></a>';
                                            }
                                            var filterBtn     = '';
                                        }
                                        else {
                                            var filterBtn     = '';
                                            var filterItemBtn = '';
                                        }

                                        var menuBtn = '<div class="fdb-block-menu-button-right">'+filterBtn+filterItemBtn+'<a id="fdb-block-menu-button-copy-'+id+'" class="fdb-block-menu-button-copy" data-fdb-id="'+id+'" uk-tooltip="title: Copy Block"><i class="mdi mdi-content-copy" id=""></i></a><a id="fdb-block-menu-button-delete-block-'+id+'" class="fdb-block-menu-button-delete-block" data-fdb-id="'+id+'" uk-tooltip="title: Delete Block"><i class="mdi mdi-delete" id=""></i></a></div>';

                                        $(this).append(menuBtn);

                                        var setUikitFilterIdBtn       = setUikitFilterId();
                                        var setUikitFilterCategoryBtn = setUikitFilterCategory();

                                        $('.fdb-block-menu-button-copy').on("click", function() {
                                            var btn    = $(this),
                                                idBtn  = btn.data('fdb-id'),
                                                parent = copyBlock.parent();

                                            btn.closest('li').removeClass('uk-filter-hovered');
                                            btn.closest('li').removeClass('uk-filter-item-hovered');
                                            parent.append(copyBlock.clone());
                                            parent.find('.fdb-block-menu-button-right').remove();
                                            blockCopy();
                                            froalaEditorInitial();

                                            return false;
                                        })

                                        $('.fdb-block-menu-button-delete-block').on("click", function() {
                                            var btn   = $(this),
                                                idBtn = btn.data('fdb-id');

                                            copyBlock.remove();

                                            setTimeout(function() {
                                                treePanelContent();
                                            }, 1000);

                                            return false;
                                        })
                                    }
                                },
                                mouseleave: function () {
                                    $(this).find('img').removeClass('img-to-change-src');
                                    $(this).removeClass('fdb-block-selected');
                                    if ($(this).hasClass('uk-block-filter')) {
                                        $(this).removeClass('uk-block-filter-selected');
                                    }
                                    $(this).find('.fdb-block-menu-button-right').remove();
                                    setTimeout(function() {
                                        treePanelContent();
                                    }, 1000);
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
                                        $(this).on("click", function() {
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

        $("#button-toggle-blocks").on("click", function() {
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
            var id       = $(this).data('purple-id'),
                image    = $(this).data('purple-image'),
                host     = $(this).data('purple-host'),
                by       = $(this).data('purple-by'),
                created  = $(this).data('purple-created'),
                desc     = $(this).data('purple-description'),
                title    = $(this).attr('title'),
                nextUrl  = $(this).attr('data-purple-next-url'),
                prevUrl  = $(this).attr('data-purple-previous-url'),
                modal    = $("#modal-full-content");
            modal.find(".uk-background-contain").css('background-image', 'url(' + image + ')');
            modal.find("#media-image-next-url").attr('href', nextUrl);
            modal.find("#media-image-previous-url").attr('href', prevUrl);
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


            modal.find("form .button-delete-media-image").on("click", function() {
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

        $(".button-delete-media").on("click", function() {
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