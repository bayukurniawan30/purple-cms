<?php
    $backUrl = $this->Url->build([
        '_name' => 'adminCollections'
    ]);

    echo $this->Form->create(NULL, [
        'id'                    => 'form-add-collection-data',
        'class'                 => '',
        'data-parsley-validate' => '',
        'url'                   => ['action' => 'ajax-add-data']
    ]);

    echo $this->Form->hidden('collection_id', ['value' => $collection->id]);
?>

<div class="row">
    <div class="col-md-8 grid-margin">
        <div class="uk-child-width-1-1" uk-grid>
            <?php
                $textInput = [];
                $decodeFields = json_decode($collection->fields, true);
                if (count($decodeFields) > 0):
                    foreach ($decodeFields as $field):
                        $decodeField  = json_decode($field, true);
                        if (strpos($decodeField['field_type'], 'connecting_') !== false) {
                            $collectionId = (int)str_replace('connecting_', '', $decodeField['field_type']);
                            $printElement = $this->element('Dashboard/Collections/Fields/connecting', ['uid' => $decodeField['uid'], 'id' => $collectionId, 'field_type' => $decodeField['field_type'], 'label' => $decodeField['label'], 'info' => $decodeField['info'], 'required' => $decodeField['required'], 'options' => $decodeField['options']]);
                        }
                        else {
                            $printElement = $this->element('Dashboard/Collections/Fields/' . $decodeField['field_type'], ['uid' => $decodeField['uid'], 'field_type' => $decodeField['field_type'], 'label' => $decodeField['label'], 'info' => $decodeField['info'], 'required' => $decodeField['required'], 'options' => $decodeField['options']]);
                        }

                        if ($decodeField['field_type'] == 'text') {
                            $textInput[$decodeField['uid']] = $decodeField['label'];
                        }
            ?>
            <div>
                <?php
                    if ($decodeField['field_type'] != 'image' && $decodeField['field_type'] != 'gallery'):
                ?>
                <div id="data-<?= $decodeField['uid'] ?>" class="card">
                    <div class="card-header">
                        <h4 class="card-title uk-margin-remove-bottom"><?= $decodeField['label'] ?></h4>
                    </div>
                    <div class="card-body">
                        <?php
                            echo $printElement;
                        ?>
                    </div>
                </div>
                <?php
                    else:
                        echo $printElement;
                    endif;
                ?>
            </div>
            <?php
                    endforeach;
                endif;
            ?>
        </div>
    </div>

    <div class="col-md-4 grid-margin">
        <div uk-sticky="offset: 100">
            <div class="uk-grid-small uk-flex uk-flex-right" uk-grid>
                <div class="uk-width-1-1 uk-width-auto@m">
                    <button type="button" class="btn btn-gradient-success btn-toolbar-card btn-sm btn-icon-text" onclick="location.href='<?= $backUrl ?>'">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                        Back
                    </button>
                </div>
                <div class="uk-width-1-1 uk-width-auto@m">
                    <?php
                        echo $this->Form->button('<i class="mdi mdi-content-save-outline btn-icon-prepend"></i> Save ' . $collection->name, [
                            'id'    => 'button-add-collection-data',
                            'class' => 'btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left',
                            'type'  => 'submit'
                        ]);
                    ?>
                </div>
            </div>

            <?php
                if (count($textInput) > 0):
            ?>
            <div class="uk-grid-small uk-margin-medium-bottom" uk-grid>
                <div class="uk-width-1-1">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title uk-margin-remove-bottom">Generate Safe URL</h4>
                        </div>
                        <div class="card-body uk-padding-remove">
                            <ul class="uk-list uk-list-divider uk-margin-remove">
                                <li class="uk-padding-small uk-margin-remove-top">
                                    Create Slug
                                    <div class="uk-inline uk-align-right" style="margin-bottom: 0">
                                        <?php
                                            echo $this->Form->checkbox('create_slug', ['class' => 'slug-switch', 'value' => '1', 'checked' => false, 'required' => false]);
                                        ?>
                                    </div>
                                </li>
                            </ul>

                            <div id="toggle-slug-target" class="form-group uk-padding-small" hidden>
                                <?php
                                    echo $this->Form->select('slug_target',
                                        $textInput,
                                        [
                                            'id'       => 'select-slug-target',
                                            'empty'    => 'Select Target Field',
                                            'class'    => 'form-control',
                                            'required' => false
                                        ]
                                    );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                endif;
            ?>

            <?php
                $jsonView = [];
                $decodeFields = json_decode($collection->fields, true);
                if (count($decodeFields) > 0):
                    foreach ($decodeFields as $field):
                        $decodeField = json_decode($field, true);
                        array_push($jsonView, $decodeField);
                    endforeach;
                endif;
            ?>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-1">
                    <pre id="json-display" class="uk-padding-small uk-height-large"><?= json_encode($jsonView, JSON_PRETTY_PRINT) ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    echo $this->Form->end();
?>

<?= $this->Html->script('/master-assets/plugins/switchery/switchery.min.js'); ?>
<script type="text/javascript">
    var elems = Array.prototype.slice.call(document.querySelectorAll('.slug-switch'));

    elems.forEach(function(html) {
        var switchery = new Switchery(html, { color: '#9a55ff', jackColor: '#ffffff', size: 'small' });
    });

    var changeCheckbox   = document.querySelector('.slug-switch'),
        toggleSlugTarget = document.getElementById('toggle-slug-target'),
        selectSlugTarget = document.getElementById('select-slug-target');

    changeCheckbox.onchange = function() {
        if (changeCheckbox.checked == true) {
            toggleSlugTarget.removeAttribute('hidden');
            selectSlugTarget.setAttribute('required', true);
        }
        else {
            toggleSlugTarget.setAttribute('hidden', true);
            selectSlugTarget.removeAttribute('required');
        }
    };
</script>

<script type="text/javascript">
    $(document).ready(function() {
        window.onbeforeunload = function() {
            return "Your collection data is not saved. Are you sure want to leave?";
        }

        // var jsonEditor = new JsonEditor('#json-display', JSON.parse(''));

        var collectionAddData = {
            form            : 'form-add-collection-data',
            button          : 'button-add-collection-data',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $backUrl; ?>',
            btnNormal       : false,
            btnLoading      : '<i class="mdi mdi-loading mdi-spin btn-icon-prepend"></i> Saving <?= $collection->name ?>'
        };

        var targetButton = $("#"+collectionAddData.button);
        targetButton.one('click',function() {
            window.onbeforeunload = null;
            ajaxSubmit(collectionAddData.form, collectionAddData.action, collectionAddData.redirectType, collectionAddData.redirect, collectionAddData.btnNormal, collectionAddData.btnLoading);
        })
    })
</script>