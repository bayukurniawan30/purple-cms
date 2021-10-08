<?php
    $fieldOptionsUrl = $this->Url->build([
		'_name'  => 'adminSingletonsAction',
		'action' => 'ajaxGetOptions'
    ]);

    $submitRedirect = $this->Url->build([
        '_name' => 'adminSingletons'
    ]);;
?>

<?php
    echo $this->Form->create($singletonEdit, [
        'id'                    => 'form-edit-singleton',
        'class'                 => '',
        'data-parsley-validate' => '',
        'url'                   => ['action' => 'ajax-update']
    ]);
?>

<div class="row">
    <div class="col-md-12 grid-margin">
        <div id="page-detail-card" class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Singleton Information</h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <?php
                        echo $this->Form->hidden('id', ['value' => $singleton->id]);
                        echo $this->Form->hidden('fields', ['value' => '']);
                        echo $this->Form->label('name', 'Singleton Name');
                        echo $this->Form->text('name', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Singleton Name. Max 255 chars.',
                            'data-parsley-minlength' => '2',
                            'data-parsley-maxlength' => '255',
                            'uk-tooltip'             => 'title: Required. 2-255 chars.; pos: bottom',
                            'autofocus'              => 'autofocus',
                            'required'               => 'required',
                            'value'                  => $singleton->name
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Singleton Field</h4>
            </div>
            <div class="card-toolbar">
            	<button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-component-field" data-purple-modal="#modal-add-field" data-purple-component="singleton" data-purple-url="<?= $fieldOptionsUrl ?>" data-purple-action="add">
                <i class="mdi mdi-pencil btn-icon-prepend"></i>
                	New Field
                </button>
            </div>
            <div id="bind-added-field" class="card-body">
                <?php
                    $decodeFields = json_decode($singleton->fields, true);
                    if (count($decodeFields) > 0):
                ?>
                <ul id="sortable-items" class="display-list" uk-sortable="handle: .uk-sortable-handle" uk-grid>
                <?php
                        $countList = 1;
                        foreach ($decodeFields as $field):
                            $decodeField   = json_decode($field);
                            $fieldTypeName = $this->cell('Singletons::fieldTypeName', [$decodeField->field_type])->render();
                ?>
                    <li id="sortable-<?= $countList ?>" class="uk-width-1-1 uk-margin-remove-top" data-order="<?= $countList ?>" style="position: relative">
                        <div class="sortable-remover" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 5; display: none; background: rgba(255,255,255,.4)"></div>
                        <div class="uk-card uk-card-default uk-card-small uk-card-body">
                            <span class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: menu"></span>
                            <span class="field-label"><?= $decodeField->label ?></span><span class="field-type uk-text-muted uk-text-italic uk-text-small uk-margin-left"><?= $fieldTypeName ?></span>
                            <input type="hidden" name="fields[]" value='<?= $field ?>'>
                            
                            <div class="uk-inline uk-align-right">
                                <a href="#" class="uk-margin-small-right button-edit-component-field" data-purple-target="#sortable-<?= $countList ?>" data-purple-component="singleton" data-purple-modal="#modal-add-field" data-purple-action="edit" data-purple-url="<?= $fieldOptionsUrl ?>" uk-icon="icon: pencil" uk-tooltip="Edit field"></a>
                                <a href="#" class="text-danger button-delete-added-field" uk-icon="icon: close" data-purple-component="singleton" data-purple-target="#sortable-<?= $countList ?>" uk-tooltip="Delete field"></a>
                            </div>
                        </div>
                    </li>
                <?php
                            $countList++;
                        endforeach;
                ?>
                </ul>
                <?php
                    endif;
                ?>
            </div>
            <div class="card-footer">
                <?php
                    echo $this->Form->button('Save', [
                        'id'    => 'button-edit-singleton',
                        'class' => 'btn btn-gradient-primary'
                    ]);

                    echo $this->Form->button('Cancel', [
                        'class'   => 'btn btn-outline-secondary uk-margin-left',
                        'type'    => 'button',
                        'onclick' => 'location.href = \''.$submitRedirect.'\''
                    ]);
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-4 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Settings</h4>
            </div>
            <div class="card-body uk-padding-remove">
                <ul class="uk-list uk-list-divider uk-margin-remove">
                    <li class="uk-padding-small uk-margin-remove-top">
                        Publish
                        <div class="uk-inline uk-align-right" style="margin-bottom: 0">
                            <?php
                                echo $this->Form->checkbox('status', ['class' => 'js-switch', 'value' => '1', 'checked' => $singleton->status == '1' ? true : false, 'required' => false]);
                            ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
    echo $this->Form->end();
?>


<?= $this->element('Dashboard/Modal/Singletons/add_field_modal', [
    'fieldOptionsUrl' => $fieldOptionsUrl
]) ?>

<?= $this->Html->script('/master-assets/plugins/switchery/switchery.min.js'); ?>
<script type="text/javascript">
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function(html) {
        var switchery = new Switchery(html, { color: '#9a55ff', jackColor: '#ffffff', size: 'small' });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        window.onbeforeunload = function() {
            return "Your singleton is not saved. Are you sure want to leave?";
        }

        var singletonEdit = {
            form            : 'form-edit-singleton',
            button          : 'button-edit-singleton',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect; ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+singletonEdit.button);
        targetButton.one('click',function() {
            window.onbeforeunload = null;
            ajaxSubmit(singletonEdit.form, singletonEdit.action, singletonEdit.redirectType, singletonEdit.redirect, singletonEdit.btnNormal, singletonEdit.btnLoading);
        })
    })
</script>