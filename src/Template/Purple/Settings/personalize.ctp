<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Dashboard Personalize</h4>
            </div>
            <div class="card-body">
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                        <thead>
                            <?php
                                echo $this->Html->tableHeaders([
                                    'Setting Name',
                                    'Value',
                                    ['Action' => ['class' => 'uk-width-small text-center']]
                                ]);
                            ?>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Default Background Login</td>
                                <td>
                                    <?= $settingDefaultBgLogin->value == 'yes' ? 'Yes' : 'Custom Background' ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Default Background Login" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingDefaultBgLogin->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="personalize" uk-tooltip="Change Default Background Login"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <?php
                                if ($settingDefaultBgLogin->value == 'no'):
                            ?>
                            <tr>
                                <td>Image Background Login</td>
                                <td>
                                    <?php
                                        if ($settingBgLogin->value == '') {
                                            echo 'Default';
                                        } 
                                        else {
                                            echo $this->Html->image('/uploads/images/original/' . $settingBgLogin->value, ['width' => 50]);
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-browse-images" data-purple-title="Login Background" data-purple-target="#modal-browse_images" data-purple-id="<?= $settingBgLogin->id ?>" data-purple-browse-action="update" data-purple-browse-content="settings::<?= $settingBgLogin->id ?>" data-purple-browse-actionurl="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxUpdateSetting"]); ?>" data-purple-redirect="personalize" uk-tooltip="Change Image Background Login"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-edit-settings" class="uk-flex-top purple-modal" uk-modal>
    <div id="load-edit-settings" class="uk-modal-dialog uk-margin-auto-vertical">
    </div>
</div>

<?php
    if ($settingDefaultBgLogin->value == 'no'):
?>
<?= $this->element('Dashboard/Modal/browse_images_modal', [
        'selected'     => '', 
        'browseMedias' => $browseMedias
]) ?>
<?php endif; ?>

<script>
    $(document).ready(function() {
        // Trigger browse image button to fire modal
        var browseImageBtn = browseImageButton();
    })
</script>