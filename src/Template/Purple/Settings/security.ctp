<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Form Security</h4>
            </div>
            <div class="card-body">
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Purple use Google reCaptcha V3 to submit comment and contact message. Go to <a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank">Google reCaptcha</a> to get the keys.</p>
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
                                <td>Google reCaptcha Sitekey</td>
                                <td><?= $settingRecaptchaSitekey->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Google reCaptcha Sitekey" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingRecaptchaSitekey->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="security" uk-tooltip="Change Google reCaptcha Sitekey"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Google reCaptcha Secret</td>
                                <td><?= $settingRecaptchaSecret->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Google reCaptcha Secret" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingRecaptchaSecret->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="security" uk-tooltip="Change Google reCaptcha Secret"><i class="mdi mdi-pencil"></i> Change</button>
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
                <h4 class="card-title uk-margin-remove-bottom">2FA (Two-Factor Authentication)</h4>
            </div>
            <div class="card-body">
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Enable 2FA to increase the security of your account. If enabled, you have to add phone number in the user profile. Purple CMS uses <a href="https://authy.com/" class="uk-text-bold" target="_blank">Authy</a> as 2FA service. To make the 2FA process easier, please download and install Authy app in your smartphone.</p>
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
                                <td>2FA (Two-Factor Authentication)<br><small class="text-muted">By default, Purple will ask for a verification code if you sign in from diffrent device.</small></td>
                                <td><?= ucwords($settingTwoFAuth->value) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="2FA (Two Factor Authentication)" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingTwoFAuth->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="security" uk-tooltip="Change 2FA (Two-Factor Authentication)"><i class="mdi mdi-pencil"></i> Change</button>
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
                <h4 class="card-title uk-margin-remove-bottom">Production Key</h4>
            </div>
            <div class="card-body">
                <div class="uk-alert-primary" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Production key is used when you want to move to production server or make your website live.</p>
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
                                <td>Key</td>
                                <td><?= $productionKey ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Production Key" data-purple-target="#modal-edit-settings" data-purple-id="0" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="security" uk-tooltip="View Production Key"><i class="mdi mdi-key"></i> View</button>
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