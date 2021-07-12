<?php
    $backUrl = $this->Url->build([
        '_name' => 'adminCollectionsViewData',
        'data'  => $collection->slug
    ]);

    echo $this->Form->create(NULL, [
        'id'                    => 'form-edit-collection-data',
        'class'                 => '',
        'data-parsley-validate' => '',
        'url'                   => ['action' => 'ajax-update-data']
    ]);

    echo $this->Form->hidden('collection_id', ['value' => $collection->id]);
    echo $this->Form->hidden('id', ['value' => $collectionData->id]);
?>

<div class="row">
    <div class="col-md-8 grid-margin">
        <div class="uk-child-width-1-1" uk-grid>
            <?php
                $decodeContent = json_decode($collectionData->content, true);
                $decodeFields  = json_decode($collection->fields, true);
                if (count($decodeFields) > 0):
                    foreach ($decodeFields as $field):
                        $decodeField = json_decode($field, true);
                        $printElement = $this->element('Dashboard/Collections/Fields/' . $decodeField['field_type'], ['uid' => $decodeField['uid'], 'field_type' => $decodeField['field_type'], 'label' => $decodeField['label'], 'info' => $decodeField['info'], 'required' => $decodeField['required'], 'value' => $decodeContent[$decodeField['uid']]['value'], 'options' => $decodeField['options']]);
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
                            'id'    => 'button-edit-collection-data',
                            'class' => 'btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text uk-margin-small-left',
                            'type'  => 'submit'
                        ]);
                    ?>
                </div>
            </div>

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

<script type="text/javascript">
    $(document).ready(function() {
        window.onbeforeunload = function() {
            return "Your collection data is not saved. Are you sure want to leave?";
        }

        // var jsonEditor = new JsonEditor('#json-display', JSON.parse(''));

        var collectionEditData = {
            form            : 'form-edit-collection-data',
            button          : 'button-edit-collection-data',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $backUrl; ?>',
            btnNormal       : false,
            btnLoading      : '<i class="mdi mdi-loading mdi-spin btn-icon-prepend"></i> Saving <?= $collection->name ?>'
        };

        var targetButton = $("#"+collectionEditData.button);
        targetButton.one('click',function() {
            window.onbeforeunload = null;
            ajaxSubmit(collectionEditData.form, collectionEditData.action, collectionEditData.redirectType, collectionEditData.redirect, collectionEditData.btnNormal, collectionEditData.btnLoading);
        })
    })
</script>