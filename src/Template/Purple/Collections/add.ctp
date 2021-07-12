<?php
    $fieldOptionsUrl = $this->Url->build([
		'_name'  => 'adminCollectionsAction',
		'action' => 'ajaxGetOptions'
    ]);

    $submitRedirect = $this->Url->build([
        '_name' => 'adminCollections'
    ]);;
?>

<?php
    echo $this->Form->create($collectionAdd, [
        'id'                    => 'form-add-collection',
        'class'                 => '',
        'data-parsley-validate' => '',
        'url'                   => ['action' => 'ajax-add']
    ]);
?>

<div class="row">
    <div class="col-md-12 grid-margin">
        <div id="page-detail-card" class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Collection Information</h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <?php
                        echo $this->Form->hidden('fields', ['value' => '']);
                        echo $this->Form->label('name', 'Collection Name');
                        echo $this->Form->text('name', [
                            'class'                  => 'form-control',
                            'placeholder'            => 'Collection Name. Max 255 chars.',
                            'data-parsley-minlength' => '2',
                            'data-parsley-maxlength' => '255',
                            'uk-tooltip'             => 'title: Required. 2-255 chars.; pos: bottom',
                            'autofocus'              => 'autofocus',
                            'required'               => 'required'
                        ]);
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php
                                echo $this->Form->label('sorting', 'Default Sorting');
                                echo $this->Form->select(
                                    'sorting',
                                    [
                                        'created'  => 'Created', 
                                        'modified' => 'Modified'
                                    ],
                                    [
                                        'empty'    => 'Select Default Sorting', 
                                        'class'    => 'form-control',
                                        'required' => 'required'
                                    ]
                                );
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php
                                echo $this->Form->label('sorting_order', 'Sorting Order');
                                echo $this->Form->select(
                                    'sorting_order',
                                    [
                                        'ASC'  => 'Ascending', 
                                        'DESC' => 'Descending'
                                    ],
                                    [
                                        'empty'    => 'Select Sorting Order', 
                                        'class'    => 'form-control',
                                        'required' => 'required',
                                        'value'    => 'DESC'
                                    ]
                                );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Collection Field</h4>
            </div>
            <div class="card-toolbar">
            	<button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-component-field" data-purple-modal="#modal-add-field" data-purple-component="collection" data-purple-url="<?= $fieldOptionsUrl ?>" data-purple-action="add">
                <i class="mdi mdi-pencil btn-icon-prepend"></i>
                	New Field
                </button>
            </div>
            <div id="bind-added-field" class="card-body"></div>
            <div class="card-footer">
                <?php
                    echo $this->Form->button('Save', [
                        'id'    => 'button-add-collection',
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


<?= $this->element('Dashboard/Modal/Collections/add_field_modal', [
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
            return "Your collection is not saved. Are you sure want to leave?";
        }

        var collectionAdd = {
            form            : 'form-add-collection',
            button          : 'button-add-collection',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect; ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+collectionAdd.button);
        targetButton.one('click',function() {
            window.onbeforeunload = null;
            ajaxSubmit(collectionAdd.form, collectionAdd.action, collectionAdd.redirectType, collectionAdd.redirect, collectionAdd.btnNormal, collectionAdd.btnLoading);
        })
    })
</script>