<?php
    date_default_timezone_set($timezone);
    $now  = date('Y-m-d H:i:s');
    $date = date($settingDateFormat->value, strtotime($now));
    $time = date($settingTimeFormat->value, strtotime($now));
?>

<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Website Settings</h4>
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
                                <td>Site Name</td>
                                <td><?= $settingSiteName->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Site Name" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSiteName->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Site Name"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Tagline</td>
                                <td><?= $settingTagLine->value == '' ? 'None' : $settingTagLine->value ?></td>
                                <td>
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Tagline" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingTagLine->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Tagline"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Website Email</td>
                                <td><?= $settingEmail->value == '' ? 'None' : $settingEmail->value ?></td>
                                <td>
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Website Email" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingEmail->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Website Email"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td><?= $settingPhone->value == '' ? 'None' : $settingPhone->value ?></td>
                                <td>
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Phone" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingPhone->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Phone"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td><?= $settingAddress->value == '' ? 'None' : $settingAddress->value ?></td>
                                <td>
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Address" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingAddress->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Address"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Date and Time</h4>
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
                                <td>Date Format</td>
                                <td><?= $date ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Site Name" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingDateFormat->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Date Format"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Time Format</td>
                                <td><?= $time ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Site Name" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingTimeFormat->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Time Format"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Timezone</td>
                                <td><?= $settingTimezone->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Site Name" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingTimezone->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="general" uk-tooltip="Change Timezone"><i class="mdi mdi-pencil"></i> Change</button>
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