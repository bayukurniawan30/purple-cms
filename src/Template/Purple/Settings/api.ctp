<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">API Access Key</h4>
            </div>
            <div class="card-body">
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>API access key is used for API request from external application with. Used in <strong>POST</strong>, <strong>PUT</strong>, <strong>PATCH</strong>, or <strong>DELETE</strong> request.</p>
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
                                <td>API Access Key</td>
                                <td><?= $settingApiAccessKey->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="API Access Key" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingApiAccessKey->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="api" uk-tooltip="View API Access Key"><i class="mdi mdi-key"></i> View</button>
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