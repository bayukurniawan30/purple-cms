<?php
    date_default_timezone_set($timezone);
    $now  = date('Y-m-d H:i:s');
     
    echo $this->Form->create($settingsStandardModal, [
        'id'                    => 'form-update-setting', 
        'class'                 => 'pt-3', 
        'data-parsley-validate' => '',
        'url' 					=> ['action' => 'ajax-update']
    ]);
?>
<div class="uk-modal-header">
    <h3 class="uk-modal-title"></h3>
</div>
<div class=" uk-modal-body">
    <?php
        echo $this->Form->hidden('id');
        echo $this->Form->hidden('redirect');
    ?>
    <div class="form-group">
        <?php
            echo $this->Form->label('value', 'Setting Value');
            
            if ($name == 'email') {
                echo $this->Form->control('value', [
                    'label'             => false,
                    'class'             => 'form-control', 
                    'placeholder'       => $title,
                    'type'              => 'email',
                    'data-parsley-type' => 'email',
                    'required'          => 'required',
                    'autofocus'         => 'autofocus',
                    'value'             => $value
                ]);
            }
            elseif ($name == 'tagline') {
                echo $this->Form->text('value', [
                    'class'                  => 'form-control', 
                    'placeholder'            => $title,
                    'data-parsley-maxlength' => '100',
                    'autofocus'              => 'autofocus',
                    'value'                  => $value
                ]);
            }
            elseif ($name == 'postlimitperpage') {
                echo $this->Form->text('value', [
                    'type'              => 'number',
                    'class'             => 'form-control', 
                    'placeholder'       => $title,
                    'data-parsley-type' => 'integer',
                    'min'               => '1',
                    'autofocus'         => 'autofocus',
                    'value'             => $value
                ]);
            }
            elseif ($name == 'postpermalink') {
                $permalinkDNUrl = $this->Url->build([
                    '_name' => 'specificPost',
                    'year'  => date('Y'),
                    'month' => date('m'),
                    'date'  => date('d'),
                    'post'  => 'sample-post',
                ], true);

                $permalinkMNUrl = $this->Url->build([
                    '_name' => 'specificPostMonth',
                    'year'  => date('Y'),
                    'month' => date('m'),
                    'post'  => 'sample-post',
                ], true);

                $permalinkPNUrl = $this->Url->build([
                    '_name' => 'specificPostName',
                    'post'  => 'sample-post',
                ], true);

                echo $this->Form->select(
                    'value',
                    [
                        'day-name'   => $permalinkDNUrl.' (Default)', 
                        'month-name' => $permalinkMNUrl, 
                        'post-name'  => $permalinkPNUrl 
                    ],
                    [
                        'empty'    => 'Select Permalink Format', 
                        'class'    => 'form-control',
                        'required' => 'required'
                    ]
                );
            }
            elseif ($name == 'dateformat') {
                echo $this->Form->select(
                    'value',
                    [
                        'F d, Y' => date('F d, Y', strtotime($now)), 
                        'Y-n-d'  => date('Y-n-d', strtotime($now)), 
                        'n/d/Y'  => date('n/d/Y', strtotime($now)), 
                        'd/n/Y'  => date('d/n/Y', strtotime($now)) 
                    ],
                    [
                        'empty'    => 'Select Format', 
                        'class'    => 'form-control',
                        'required' => 'required'
                    ]
                );
            }
            elseif ($name == 'timeformat') {
                echo $this->Form->select(
                    'value',
                    [
                        'g:i a' => date('g:i a', strtotime($now)), 
                        'g:i A' => date('g:i A', strtotime($now)), 
                        'G:i'   => date('G:i', strtotime($now)), 
                    ],
                    [
                        'empty'    => 'Select Format', 
                        'class'    => 'form-control',
                        'required' => 'required'
                    ]
                );
            }
            elseif ($name == 'timezone') {
                $timezoneListing = array();
                foreach ($timezoneList as $list):
                    $timezoneListing[$list] = $list;
                endforeach;
                
                echo $this->Form->select(
                    'value',
                    $timezoneListing,
                    [
                        'empty'    => 'Select Your Timezone', 
                        'class'    => 'form-control',
                        'required' => 'required'
                    ]
                );
            }
            elseif ($name == 'metadescription') {
                echo $this->Form->textarea('value', [
                    'class'                  => 'form-control', 
                    'placeholder'            => 'Meta Description, for best result, maximum 155 characters',
                    'data-parsley-maxlength' => '155',
                    'required'               => 'required',
                    'autofocus'              => 'autofocus',
                    'value'                  => $value

                ]);
            }
            elseif ($name == 'recaptchasitekey' || $name == 'recaptchasecret') {
                echo $this->Form->text('value', [
                    'class'                  => 'form-control', 
                    'placeholder'            => $title,
                    'data-parsley-maxlength' => '75',
                    'autofocus'              => 'autofocus',
                    'value'                  => $value
                ]);
            }
            elseif ($name == 'ldjson') {
                echo $this->Form->select(
                    'value',
                    [
                        'enable'  => 'Enable', 
                        'disable' => 'Disable' 
                    ],
                    [
                        'empty'    => 'Select Value', 
                        'class'    => 'form-control',
                        'required' => 'required'
                    ]
                );
            }
            elseif ($name == 'smtppassword') {
                echo $this->Form->password('value', [
                    'class'       => 'form-control', 
                    'placeholder' => $title,
                    'required'    => 'required',
                    'autofocus'   => 'autofocus',
                    'value'       => $value

                ]);
            }
            elseif ($name == 'smtpsecure') {
                echo $this->Form->select(
                    'value',
                    [
                        'tls' => 'TLS',
                        'ssl' => 'SSL'
                    ],
                    [
                        'empty' => 'Select SMTP Encryption', 
                        'class' => 'form-control'
                    ]
                );
            }
            elseif ($name == 'smtpport') {
                echo $this->Form->select(
                    'value',
                    [
                        '25'  => '25',
                        '465' => '465',
                        '587' => '587'
                    ],
                    [
                        'empty' => 'Select SMTP Port', 
                        'class' => 'form-control'
                    ]
                );
            }
            elseif ($name == 'senderemail') {
                echo $this->Form->control('value', [
                    'label'             => false,
                    'class'             => 'form-control', 
                    'placeholder'       => $title,
                    'type'              => 'email',
                    'data-parsley-type' => 'email',
                    'autofocus'         => 'autofocus',
                    'value'             => $value
                ]);
            }
            elseif ($name == 'defaultbackgroundlogin') {
                echo $this->Form->select(
                    'value',
                    [
                        'yes' => 'Default', 
                        'no'  => 'Custom Background' 
                    ],
                    [
                        'empty'    => 'Select Format', 
                        'class'    => 'form-control',
                        'required' => 'required'
                    ]
                );
            }
            elseif ($name == 'googleanalyticscode') {
                echo $this->Form->textarea('value', [
                    'id'          => 'fdb-code-editor-ace',
                    'class'       => 'form-control uk-height-medium', 
                    'placeholder' => 'Place your Google Analytics Code here',
                    'rows'        => '5',
                    'autofocus'   => 'autofocus',
                    'value'       => $value

                ]);
            }
            elseif ($name == 'comingsoon') {
                echo $this->Form->select(
                    'value',
                    [
                        'disable' => 'Disable',
                        'enable'  => 'Enable'
                    ],
                    [
                        'empty' => 'Select Maintenance Mode', 
                        'class' => 'form-control'
                    ]
                );
            }
            elseif ($name == 'productionkey') {
                echo '<div class="uk-inline" style="width: 100%"><a title="Copy Production Key" class="uk-form-icon uk-form-icon-flip icon-copy-key" href="#" uk-icon="icon: copy" data-clipboard-target="#purple-key"></a>';
                echo $this->Form->text('value', [
                    'id'          => 'purple-key',
                    'class'       => 'uk-input', 
                    'placeholder' => $title,
                    'value'       => $value,
                    'readonly'    => 'readonly'
                ]);
                echo '</div>';
            }
            elseif ($name == 'apiaccesskey') {
                echo '<div class="uk-inline" style="width: 100%"><a title="Copy API Access Key" class="uk-form-icon uk-form-icon-flip icon-copy-key" href="#" uk-icon="icon: copy" data-clipboard-target="#purple-key"></a>';
                echo $this->Form->text('value', [
                    'id'          => 'purple-key',
                    'class'       => 'uk-input', 
                    'placeholder' => $title,
                    'value'       => $value,
                    'readonly'    => 'readonly'
                ]);
                echo '</div>';
            }
            elseif ($name == 'apisecretkey') {
                echo '<div class="uk-inline" style="width: 100%"><a title="Copy API Secret Key" class="uk-form-icon uk-form-icon-flip icon-copy-key" href="#" uk-icon="icon: copy" data-clipboard-target="#purple-key"></a>';
                echo $this->Form->text('value', [
                    'id'          => 'purple-key',
                    'class'       => 'uk-input', 
                    'placeholder' => $title,
                    'value'       => $value,
                    'readonly'    => 'readonly'
                ]);
                echo '</div>';
            }
            else {
                echo $this->Form->text('value', [
                    'class'                  => 'form-control', 
                    'placeholder'            => $title,
                    'data-parsley-maxlength' => '500',
                    'required'               => 'required',
                    'autofocus'              => 'autofocus',
                    'value'                  => $value
                ]);
            }
        ?>
    </div> 
</div>
<div class="uk-modal-footer uk-text-right">
    <?php 
        if ($name != 'productionkey') {  
            echo $this->Form->button('Save', [
                'id'    => 'button-update-setting',
                'class' => 'btn btn-gradient-primary'
            ]);
        }
    
        echo $this->Form->button('Cancel', [
            'id'          => 'button-close-modal',
            'class'       => 'btn btn-outline-primary uk-margin-left uk-modal-close',
            'type'        => 'button',
            'data-target' => '.purple-modal'
        ]);
    ?>
</div>
<?php
	echo $this->Form->end();
?>

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($name == 'dateformat' || $name == 'timeformat' || $name == 'timezone' || $name == 'smtpsecure' || $name == 'smtpport' || $name == 'defaultbackgroundlogin' || $name == 'comingsoon' || $name == 'postpermalink' || $name == 'ldjson'):
        ?>
        $('#form-update-setting').find('select[name=value] option[value="<?= $value ?>"]').attr("selected","selected");
        <?php
            elseif ($name == 'tagline' || $name == 'senderemail'):
        ?>
        $('#form-update-setting').find('input[name=value]').removeAttr('required');
        <?php
            elseif ($name == 'googleanalyticscode'):
        ?>
        $('#fdb-code-editor-ace').ace({ theme: 'chrome', lang: 'html' }).removeAttr('required');
        <?php
            endif;
        ?>
        
        $('#form-update-setting').parsley();
        
        var settingUpdate = {
            form            : 'form-update-setting', 
            button          : 'button-update-setting',
            action          : 'edit', 
            redirectType    : 'redirect', 
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), 'action' => $redirect]); ?>', 
            btnNormal       : false, 
            btnLoading      : false 
        };
        
        var targetButton = $("#"+settingUpdate.button);
        targetButton.one('click',function() {
            ajaxSubmit(settingUpdate.form, settingUpdate.action, settingUpdate.redirectType, settingUpdate.redirect, settingUpdate.btnNormal, settingUpdate.btnLoading);
        });
    })
</script>