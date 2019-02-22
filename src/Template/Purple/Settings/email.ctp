<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Server</h4>
            </div>
            <div class="card-body">
                <?php
                    if ($this->request->host() == 'localhost'):
                ?>
                <div class="uk-alert-danger" uk-alert>
                    <a class="uk-alert-close" uk-close></a>
                    <p>Your machine is running on localhost. Sending email will be turned off unless you host your application on an online server.</p>
                </div>
                <?php endif; ?>
                
                
                <?php
                    if ($this->request->host() != 'localhost'):
                        if ($settingSmtpHost->value != '' && $settingSmtpUsername->value != '' && $settingSmtpPassword->value != '' && $settingSmtpSecure->value != '' && $settingSmtpPort->value != ''):
                ?>
                <div uk-grid>
                    <div class="uk-width-1-1">
                        <button type="button" class="btn btn-gradient-primary btn-sm btn-icon-text button-test-email uk-align-right uk-margin-small-bottom" data-purple-modal="#modal-test-email">
                            <i class="mdi mdi-pencil btn-icon-prepend"></i>
                            Test Send Email
                        </button>
                    </div>
                </div>
                <?php 
                        endif;    
                    endif; 
                ?>
                
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-divider">
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
                                <td>SMTP Host</td>
                                <td><?= $settingSmtpHost->value == '' ? 'None' : $settingSmtpHost->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="SMTP Host" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSmtpHost->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change SMTP Host"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>SMTP Auth</td>
                                <td><?= $settingSmtpAuth->value == '' ? 'None' : $settingSmtpAuth->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="SMTP Auth" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSmtpAuth->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change SMTP Auth"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>SMTP Username</td>
                                <td><?= $settingSmtpUsername->value == '' ? 'None' : $settingSmtpUsername->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="SMTP Username" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSmtpUsername->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change SMTP Username"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>SMTP Password</td>
                                <td><?= $settingSmtpPassword->value == '' ? 'None' : str_repeat('*', strlen($settingSmtpPassword->value)); ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="SMTP Password" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSmtpPassword->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change SMTP Password"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>SMTP Secure</td>
                                <td><?= $settingSmtpSecure->value == '' ? 'None' : $settingSmtpSecure->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="SMTP Secure" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSmtpSecure->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change SMTP Secure"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>SMTP Port</td>
                                <td><?= $settingSmtpPort->value == '' ? 'None' : $settingSmtpPort->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="SMTP Port" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSmtpPort->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change SMTP Port"><i class="mdi mdi-pencil"></i> Change</button>
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
                <h4 class="card-title uk-margin-remove-bottom">Sender</h4>
            </div>
            <div class="card-body">
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-divider">
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
                                <td>Sender Email <span class="mdi mdi-information-outline text-primary" uk-tooltip="Purple use SMTP username as sender by default."></span></td>
                                <td>
                                    <?php 
                                        if ($settingSenderEmail->value == '') {
                                            if ($settingSmtpUsername->value == '')
                                                echo 'None';
                                            else
                                                echo $settingSmtpUsername->value;
                                        }
                                        else {
                                            echo $settingSenderEmail->value;
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Sender Email" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSenderEmail->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change Sender Email"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Sender Name</td>
                                <td><?= $settingSenderName->value == '' ? 'None' : $settingSenderName->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Sender Name" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingSenderName->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="email" uk-tooltip="Change Sender Name"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
     </div>
</div>

<div id="modal-test-email" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($settingsTestEmail, [
                'id'                    => 'form-test-email', 
                'class'                 => 'pt-3', 
                'data-parsley-validate' => '',
                'url' 					=> ['action' => 'ajax-send-test-email']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Test Send Email</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->text('email', [
                        'type'        => 'email',
                        'class'       => 'form-control', 
                        'placeholder' => 'Email',
                        'autofocus'   => 'autofocus',
                        'required'    => 'required'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php   
            echo $this->Form->button('Send', [
                'id'    => 'button-test-email',
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
    <?php
        echo $this->Form->end();
    ?>
</div>

<div id="modal-edit-settings" class="uk-flex-top purple-modal" uk-modal>
    <div id="load-edit-settings" class="uk-modal-dialog uk-margin-auto-vertical">
    </div>
</div>
    
<script>
    $(document).ready(function() {
        var sendEmail = {
            form            : 'form-test-email', 
            button          : 'button-test-email',
            action          : 'add', 
            redirectType    : 'redirect', 
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>', 
            btnNormal       : 'Send', 
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Sending Email...' 
        };
        
        var targetButton = $("#"+sendEmail.button);
        targetButton.one('click',function() {
            ajaxSubmit(sendEmail.form, sendEmail.action, sendEmail.redirectType, sendEmail.redirect, sendEmail.btnNormal, sendEmail.btnLoading);
        })
    })
</script>