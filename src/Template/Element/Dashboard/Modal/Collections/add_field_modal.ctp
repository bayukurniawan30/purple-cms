<div id="modal-add-field" class="uk-flex-top purple-modal" uk-modal="esc-close: false; bg-close: false">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create(NULL, [
                'id'                    => 'form-add-field',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['controller' => 'Collections', 'action' => 'ajaxAddField']
            ]);
            echo $this->Form->hidden('action', ['id' => 'field_action', 'value' => 'add']);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Add Field</h3>
        </div>
        <div class="uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->label('field_type', 'Field Type');
                    echo $this->Form->select(
                        'field_type',
                        $selectboxFieldTypes,
                        [
                            'id'       => 'field_type',
                            'empty'    => 'Select Field Type', 
                            'class'    => 'form-control',
                            'required' => 'required',
                            'value'    => 'text',
                            'data-purple-component' => 'collection'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('label', 'Field Label');
                    echo $this->Form->text('label', [
                        'id'                     => 'field_label',
                        'class'                  => 'form-control',
                        'placeholder'            => 'Field Label. Max 255 chars.',
                        'data-parsley-maxlength' => '255',
                        'uk-tooltip'             => 'title: Required. max 255 chars.; pos: bottom',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('info', 'Field Info');
                    echo $this->Form->text('info', [
                        'id'                     => 'field_info',
                        'class'                  => 'form-control',
                        'placeholder'            => 'Field Info. Max 255 chars.',
                        'data-parsley-maxlength' => '255',
                        'uk-tooltip'             => 'title: Required. max 255 chars.; pos: bottom',
                    ]);
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('required', 'Required');
                    echo $this->Form->select(
                        'required',
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ],
                        [
                            'empty'    => 'Select Required Field', 
                            'id'       => 'field_required',
                            'class'    => 'form-control',
                            'required' => 'required',
                            'value'    => '1'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <div id="error-get-collection-options" class="uk-alert-danger" uk-alert hidden>
                    <p>Can't load field options. Please close this modal and try again.</p>
                </div>
                <?php
                    echo $this->Form->label('options', 'Additional Options');
                ?>
                <pre id="json-display" class="uk-margin-remove-top uk-padding-small"></pre>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->hidden('field_slug', ['id' => 'field_slug']);
                echo $this->Form->button('Save', [
                    'id'    => 'button-add-field',
                    'class' => 'btn btn-gradient-primary'
                ]);

                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-add-field-modal',
                    'class'        => 'btn btn-outline-primary uk-margin-left uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        var jsonEditor = new JsonEditor('#json-display', JSON.parse('{}'));

        function isJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        var submitAddField,
            submitAddFieldForm   = $("#form-add-field"),
            submitAddFieldButton = submitAddFieldForm.find('button[type=submit]'),
            countClick = 0,
            submitAddFieldButtonNormalState  = submitAddFieldForm.find('button[type=submit]').html();
            submitAddFieldButtonLoadingState = '<i class="fa fa-circle-o-notch fa-spin"></i> Saving...';

        submitAddFieldForm.submit(function(event){
            if (submitAddFieldForm.parsley().isValid()) {
                countClick++;
                if (isJsonString($('#json-display').text())) {
                    if (submitAddField) {
                        submitAddField.abort();
                    }
                    var inputs         = submitAddFieldForm.find("input, textarea, button"),
                        serializedData = new FormData(this);
                    serializedData.append('options', $('#json-display').text());

                    submitAddField = $.ajax({
                        url: submitAddFieldForm.attr('action'),
                        type: "POST",
                        beforeSend: function() {
                            inputs.prop("disabled", true);
                            submitAddFieldButton.html(submitAddFieldButtonLoadingState);
                            submitAddFieldButton.attr('disabled','disabled');
                            progress.start();
                        },
                        data: serializedData,
                        contentType: false,
                        cache: false,
                        processData:false,
                    });
                    submitAddField.done(function (data){
                        progress.end();

                        if (cakeDebug == 'on') {
                            console.log(data);
                        }

                        var json    = $.parseJSON(data),
                            status  = (json.status);

                        if(status == 'error') {
                            var error = (json.error);
                        }

                        if (status == 'ok') {
                            var result = (json.result);
                            var bindingTarget = $('#bind-added-field');
                            var fieldLabel    = $('#field_label').val();
                            var fieldAction   = $('#field_action').val();
                            var selectedFieldType = $('#field_type option:selected').text();

                            if (fieldAction == 'add') {
                                if (bindingTarget.html().length > 0) {
                                    var countList = $('#sortable-items li').length + 1;
                                    
                                    bindingTarget.find('#sortable-items').append('<li id="sortable-' + countList + '" class="uk-width-1-1 uk-margin-remove-top" data-order="' + countList + '" style="position: relative"><div class="sortable-remover" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 5; display: none; background: rgba(255,255,255,.4)"></div><div class="uk-card uk-card-default uk-card-small uk-card-body"><span class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: menu"></span><span class="field-label">' + fieldLabel + '</span><span class="field-type uk-text-muted uk-text-italic uk-text-small uk-margin-left">' + selectedFieldType + '</span><input type="hidden" name="fields[]" value=\'' + JSON.stringify(result) + '\'><div class="uk-inline uk-align-right"><a href="#" class="uk-margin-small-right button-edit-component-field" data-purple-component="collection" data-purple-target="#sortable-' + countList + '" data-purple-modal="#modal-add-field" data-purple-action="edit" data-purple-url="<?= $fieldOptionsUrl ?>" uk-icon="icon: pencil" uk-tooltip="Edit field"></a><a href="#" class="text-danger button-delete-added-field" uk-icon="icon: close"  data-purple-component="collection" data-purple-target="#sortable-' + countList + '" uk-tooltip="Delete field"></a></div></div></li>');
                                }
                                else {
                                    var countList = 1;

                                    bindingTarget.html('<ul id="sortable-items" class="" uk-sortable="handle: .uk-sortable-handle" uk-grid><li id="sortable-' + countList + '" class="uk-width-1-1 uk-margin-remove-top" data-order="' + countList + '" style="position: relative"><div class="sortable-remover" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 5; display: none; background: rgba(255,255,255,.4)"></div><div class="uk-card uk-card-default uk-card-small uk-card-body"><span class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: menu"></span><span class="field-label">' + fieldLabel + '</span><span class="field-type uk-text-muted uk-text-italic uk-text-small uk-margin-left">' + selectedFieldType + '</span><input type="hidden" name="fields[]" value=\'' + JSON.stringify(result) + '\'><div class="uk-inline uk-align-right"><a href="#" class="uk-margin-small-right button-edit-component-field"  data-purple-component="collection" data-purple-target="#sortable-' + countList + '" data-purple-modal="#modal-add-field" data-purple-action="edit" data-purple-url="<?= $fieldOptionsUrl ?>" uk-icon="icon: pencil" uk-tooltip="Edit field"></a><a href="#" class="text-danger button-delete-added-field" uk-icon="icon: close"  data-purple-component="collection" data-purple-target="#sortable-' + countList + '" uk-tooltip="Delete field"></a></div></div></li></ul>');
                                }
                            }
                            else {
                                $(fieldAction).find('.field-label').text(fieldLabel);
                                $(fieldAction).find('.field-type').text(selectedFieldType);
                                $(fieldAction).find('input[type=hidden]').val(JSON.stringify(result));
                            }

                            var createToast = notifToast('Adding Field', 'Field has been added successfully', 'success', true, 1500);
                            UIkit.modal('#modal-add-field').hide();
                            $('#modal-add-field').find('#field_type').val('text').removeClass('parsley-success').removeClass('parsley-error');
                            $('#modal-add-field').find('#field_label').val('').removeClass('parsley-success').removeClass('parsley-error');
                            $('#modal-add-field').find('#field_info').val('').removeClass('parsley-success').removeClass('parsley-error');
                            $('#modal-add-field').find('#field_required').val('1').removeClass('parsley-success').removeClass('parsley-error');

                            submitAddFieldButton.html(submitAddFieldButtonNormalState);

                            $(".button-edit-component-field").on("click", function() {
                                var btn          = $(this),
                                    modal        = btn.data('purple-modal'),
                                    action       = btn.data('purple-action'),
                                    target       = btn.data('purple-target'),
                                    options      = $(target).find('input[type=hidden]').val(),
                                    parseOptions = $.parseJSON(options),
                                    url          = btn.data('purple-url'),
                                    field        = (parseOptions.field_type),
                                    token        = $('#csrf-ajax-token').val();

                                $(modal).find('#error-get-collection-options').prop('hidden', false);
                                $(modal).find('#field_action').val(target);

                                $.ajax({
                                    type: "POST",
                                    url:  url,
                                    headers : {
                                        'X-CSRF-Token': token
                                    },
                                    data: { key:field },
                                    cache: false,
                                    beforeSend: function(){
                                    },
                                    success: function(data){
                                        var json    = $.parseJSON(data),
                                            status  = (json.status);

                                        if (status == 'ok') {
                                            $(modal).find('#error-get-collection-options').prop('hidden', true);
                                            $(modal).find('#field_type').val(parseOptions.field_type);
                                            $(modal).find('#field_label').val(parseOptions.label);
                                            $(modal).find('#field_info').val(parseOptions.info);
                                            $(modal).find('#field_required').val(parseOptions.required);
                                            
                                            var jsonEditor = new JsonEditor('#json-display', (parseOptions.options));
                                        }
                                        else {
                                            $(modal).find('#error-get-collection-options').prop('hidden', false);
                                        }

                                        UIkit.modal(modal).show();
                                        btn.removeAttr('disabled');
                                        $(modal).find('#field_type').focus();
                                    }
                                })

                                return false;
                            })
                            

                            if ($('.button-delete-added-field').length > 0) {
                                $('.button-delete-added-field').on('click', function() {
                                    var btn = $(this),
                                        target = btn.data('purple-target');

                                    $(target).remove();

                                    if ($('#sortable-items li').length == 0) {
                                        $('#bind-added-field').html('');
                                    }
                                    else {
                                        $('#sortable-items li').each(function(index, element) {
                                            var newIndex = index + 1;
                                            $(this).attr('id', 'sortable-' + newIndex);
                                            $(this).attr('data-order', newIndex);
                                            $(this).find('.button-edit-component-field').attr('data-purple-target', '#sortable-' + newIndex);
                                            $(this).find('.button-delete-added-field').attr('data-purple-target', '#sortable-' + newIndex);
                                        })
                                    }

                                    return false;
                                })
                            }
                        }
                        else if (status == 'error') {
                            inputs.prop("disabled", false);
                            submitAddFieldButton.removeAttr('disabled');
                            submitAddFieldButton.html(submitAddFieldButtonNormalState);

                            var createToast = notifToast('Adding Field', error, 'error', true);
                        }
                        else {
                            inputs.prop("disabled", false);
                            submitAddFieldButton.removeAttr('disabled');
                            submitAddFieldButton.html(submitAddFieldButtonNormalState);
                            
                            var createToast = notifToast('Adding Field', 'There is an error with Purple. Please try again', 'error', true);
                        }
                    });
                    submitAddField.fail(function(jqXHR, textStatus) {
                        inputs.prop("disabled", false);
                        submitAddFieldButton.removeAttr('disabled');
                        submitAddFieldButton.html(submitAddFieldButtonNormalState);
                        
                        var createToast = notifToast(jqXHR.statusText, 'Error. Please refresh the page and try again.', 'error', true);
                    });
                    submitAddField.always(function () {
                        inputs.prop("disabled", false);
                    });
                }
                else {
                    alert('Wrong JSON format in Additional Options');
                }
            }

            event.preventDefault();
        });
    })
</script>