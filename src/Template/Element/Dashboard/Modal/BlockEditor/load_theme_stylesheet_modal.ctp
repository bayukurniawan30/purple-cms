<div id="modal-theme-stylesheet" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($pageLoadCss, [
                'id'                    => 'form-load-css', 
                'class'                 => 'pt-3', 
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajaxLoadThemeStylesheets']
            ]);
        ?>
    	<div class="uk-modal-header">
            <h3 class="uk-modal-title">Select Stylesheet to be Loaded</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="uk-alert-primary" uk-alert>
                <p><strong>Note</strong> : By clicking <strong>Load CSS</strong> button, this page will be reloaded without saving your works. Please save your works before loading theme stylesheets.</p>
            </div>

            <div class="form-group">
                <?php
                    foreach ($themeStylesheets as $stylesheet):
                        $checked = false;
                        
                        if ($loadedThemeCSS != NULL) {
                            if (in_array($stylesheet, $loadedThemeCSS)) {
                                $checked = true;
                            }
                        }
                ?>
                <div class="uk-margin">
                    <label><input class="uk-checkbox" type="checkbox" name="stylesheet[]" value="<?= $stylesheet ?>" <?= $checked == true ? 'checked' : '' ?>> <?= $stylesheet ?></label>
                </div>
                <?php
                    endforeach;
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Load CSS (Reload Page)', [
                    'id'    => 'button-load-css',
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
</div>