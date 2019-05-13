<div id="modal-add-post-category" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($blogCategoryAdd, [
                'id'                    => 'form-add-post-category',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['controller' => 'BlogCategories', 'action' => 'ajax-add']
            ]);

            if ($pageId == '') {
                $pageTarget = 'NULL';
            }
            else {
                $pageTarget = $pageId;
            }
            echo $this->Form->hidden('page_id', ['value' => $pageTarget]);
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
                    'id'                  => 'button-add-post-category',
                    'class'               => 'btn btn-gradient-primary',
                    'data-purple-action'  => $afterSubmit, // refresh or load
                    'data-purple-target'  => $loadTarget,
                    'data-purple-loadurl' => $loadUrl
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