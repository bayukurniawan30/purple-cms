<div id="modal-edit-post-category" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($blogCategoryEdit, [
                'id'                    => 'form-edit-post-category',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['controller' => 'BlogCategories', 'action' => 'ajax-update']
            ]);

            echo $this->Form->hidden('id');
            echo $this->Form->hidden('page_id');
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Add Category</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <label>Category Name</label>
                <?php
                    echo $this->Form->text('name', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Category Name',
                        'data-parsley-minlength' => '2',
                        'data-parsley-maxlength' => '100',
                        'autofocus'              => 'autofocus',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Save', [
                    'id'                  => 'button-edit-post-category',
                    'class'               => 'btn btn-gradient-primary',
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