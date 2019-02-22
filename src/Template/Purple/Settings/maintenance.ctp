<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Maintenance</h4>
            </div>
            <div class="card-body">
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>If your site is in <strong>Maintenance Mode</strong>, browser will show the maintenance page to the visitors.</p>
                </div>
                
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
                                <td>Maintenance Mode</td>
                                <td><?= ucwords($settingComingSoon->value) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Maintenance Mode" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingComingSoon->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="maintenance" uk-tooltip="Maintenance Mode"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                        	</tr>
                            <tr>
                                <td>Maintenance Mode Background</td>
                                <td>
                                    <?php 
                                        if ($settingBgComingSoon->value == '') {
                                            echo 'Default';
                                        } 
                                        else {
                                            echo $this->Html->image('/uploads/images/original/' . $settingBgComingSoon->value, ['width' => 50]);
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-browse-images" data-purple-title="Maintenance Mode Background" data-purple-target="#modal-browse_images" data-purple-id="<?= $settingBgComingSoon->id ?>" data-purple-browse-action="update" data-purple-browse-content="settings::<?= $settingBgComingSoon->id ?>" data-purple-browse-actionurl="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxUpdateSetting"]); ?>" data-purple-redirect="maintenance" uk-tooltip="Change Maintenance Mode Background"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
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

<?= $this->element('Dashboard/Modal/browse_images_modal', [
        'selected'     => '', 
        'browseMedias' => $browseMedias
]) ?>

<script>
    $(document).ready(function() {
        // Trigger browse image button to fire modal
        var browseImageBtn = browseImageButton();
    })
</script>