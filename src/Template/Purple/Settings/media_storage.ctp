<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Media Storage Settings</h4>
            </div>
            <div class="card-body">
                <?php
                    if ($settingMediaStorage->value == 'awss3'):
                ?>
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p><strong>Amazon AWS S3</strong> is a simple web services interface that you can use to store and retrieve any amount of data, at any time, from anywhere on the web. <a href="https://aws.amazon.com/s3/" target="_blank">Learn more about Amazon AWS S3</a></p>
                </div>
                <?php
                    endif;
                ?>
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider table-settings">
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
                                <td>Media Storage</td>
                                <td>
                                    <?php
                                        if ($settingMediaStorage->value == 'server') {
                                            echo 'Same Host (Default)';
                                        }
                                        elseif ($settingMediaStorage->value == 'awss3') {
                                            echo 'Amazon AWS S3';
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Media Storage" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingMediaStorage->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="media-storage" uk-tooltip="Change Media Storage"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
        if ($settingMediaStorage->value == 'awss3'):
    ?>
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Amazon AWS S3 Settings</h4>
            </div>
            <div class="card-body">
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider table-settings">
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
                                <td>Access Key</td>
                                <td><?= $settingAwsS3AccessKey->value == '' ? 'Not set' : $settingAwsS3AccessKey->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="AWS S3 Access Key" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingAwsS3AccessKey->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="media-storage" uk-tooltip="Change AWS S3 Access Key"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Secret Key</td>
                                <td><?= $settingAwsS3SecretKey->value == '' ? 'Not set' : $settingAwsS3SecretKey->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="AWS S3 Secret Key" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingAwsS3SecretKey->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="media-storage" uk-tooltip="Change AWS S3 Secret Key"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Region</td>
                                <td><?= $settingAwsS3Region->value == '' ? 'Not set' : $settingAwsS3Region->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="AWS S3 Region" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingAwsS3Region->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="media-storage" uk-tooltip="Change AWS S3 Region"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Bucket</td>
                                <td><?= $settingAwsS3Bucket->value == '' ? 'Not set' : $settingAwsS3Bucket->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="AWS S3 Bucket" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingAwsS3Bucket->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="media-storage" uk-tooltip="Change AWS S3 Bucket"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
        endif;
    ?>
</div>

<div id="modal-edit-settings" class="uk-flex-top purple-modal" uk-modal>
    <div id="load-edit-settings" class="uk-modal-dialog uk-margin-auto-vertical">
    </div>
</div>