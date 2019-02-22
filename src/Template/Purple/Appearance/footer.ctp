<?php
    if ($leftSfooter == 'NULL') {
        $leftColumn = '';
    }
    else {
        $leftColumn = html_entity_decode($leftSfooter);
    }

    if ($rightSfooter == 'NULL') {
        $rightColumn = '';
    }
    else {
        $rightColumn = html_entity_decode($rightSfooter);
    }
?>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Footer</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-edit-sfooter" data-purple-modal="#modal-edit-sfooter">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Edit
                </button>
            </div>
            <div class="card-body">
                <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                    <thead>
                        <?php
                            echo $this->Html->tableHeaders([
                                'Column',
                                'Content'
                            ]);
                        ?>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="150">Left</td>
                            <td><?= $leftColumn ?></td>
                        </tr>
                        <tr>
                            <td>Right</td>
                            <td><?= $rightColumn ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal-edit-sfooter" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($sfooterEdit, [
                'id'                    => 'form-edit-footer',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-update-footer']
            ]);

            echo $this->Form->hidden('id', ['value' => $footerId]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Edit Footer</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="uk-alert-primary" uk-alert>
                <p>HTML tags allowed are <code>&lt;a&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, and <code>&lt;span&gt;</code>.</p>
            </div>

            <div class="form-group">
                <?php
                    echo $this->Form->text('left', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Left Column',
                        'data-parsley-maxlength' => '255',
                        'autofocus'              => 'autofocus',
                        'value'                  => $leftColumn
                    ]);
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->text('right', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Right Column',
                        'data-parsley-maxlength' => '255',
                        'value'                  => $rightColumn
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-save-footer',
                'class' => 'btn btn-gradient-primary'
            ]);

            echo $this->Form->button('Cancel', [
                'id'           => 'button-close-modal',
                'class'        => 'btn btn-outline-primary uk-margin-left uk-modal-close',
                'type'         => 'button',
                'data-target'  => '.purple-modal'
            ]);
        ?>
        </div>
    </div>
    <?php
        echo $this->Form->end();
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var footerUpdate = {
            form            : 'form-edit-footer', 
            button          : 'button-save-footer',
            action          : 'edit', 
            redirectType    : 'redirect', 
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), 'action' => $this->request->getParam('action')]); ?>', 
            btnNormal       : false, 
            btnLoading      : false 
        };
        
        var targetButton = $("#"+footerUpdate.button);
        targetButton.one('click',function() {
            ajaxSubmit(footerUpdate.form, footerUpdate.action, footerUpdate.redirectType, footerUpdate.redirect, footerUpdate.btnNormal, footerUpdate.btnLoading);
        });
    })
</script>