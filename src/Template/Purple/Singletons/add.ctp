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
    echo $this->Form->create($singletonAdd, [
        'id'                    => 'form-add-singleton',
        'class'                 => '',
        'data-parsley-validate' => '',
        'url'                   => ['action' => 'ajax-add']
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
                        echo $this->Form->hidden('fields', ['value' => '']);
                        echo $this->Form->label('name', 'Singleton Name');
                        echo $this->Form->text('name', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Singleton Name. Max 255 chars.',
                            'data-parsley-minlength' => '2',
                            'data-parsley-maxlength' => '255',
                            'uk-tooltip'             => 'title: Required. 2-255 chars.; pos: bottom',
                            'autofocus'              => 'autofocus',
                            'required'               => 'required'
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
            <div id="bind-added-field" class="card-body"></div>
            <div class="card-footer">
                <?php
                    echo $this->Form->button('Save', [
                        'id'    => 'button-add-singleton',
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
                                echo $this->Form->checkbox('status', ['class' => 'js-switch', 'value' => '1', 'checked' => true, 'required' => false]);
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

        var singletonAdd = {
            form            : 'form-add-singleton',
            button          : 'button-add-singleton',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect; ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+singletonAdd.button);
        targetButton.one('click',function() {
            window.onbeforeunload = null;
            ajaxSubmit(singletonAdd.form, singletonAdd.action, singletonAdd.redirectType, singletonAdd.redirect, singletonAdd.btnNormal, singletonAdd.btnLoading);
        })
    })
</script>