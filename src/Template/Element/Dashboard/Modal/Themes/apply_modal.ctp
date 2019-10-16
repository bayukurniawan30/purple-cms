<div id="modal-apply-theme" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($themeApply, [
                'id'                    => 'form-apply-theme',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-apply-theme']
            ]);

            echo $this->Form->hidden('name');
            echo $this->Form->hidden('folder');
            echo $this->Form->hidden('active', ['value' => $themeName]);
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to set <span class="bind-title"></span> as active theme?</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);

                echo $this->Form->button('Yes, Apply Now', [
                    'id'    => 'button-apply-theme',
                    'class' => 'btn btn-gradient-primary uk-margin-left'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>