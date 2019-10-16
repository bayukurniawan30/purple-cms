<div id="modal-delete-theme" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($themeDelete, [
                'id'                    => 'form-delete-theme',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-delete']
            ]);

            echo $this->Form->hidden('folder');
            echo $this->Form->hidden('name');
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to delete <span class="bind-title"></span>?</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);

                echo $this->Form->button('Yes, Delete it', [
                    'id'    => 'button-delete-theme',
                    'class' => 'btn btn-gradient-danger uk-margin-left'
                ]);
            ?>
        </div>
        <?php

            echo $this->Form->end();
        ?>
    </div>
</div>