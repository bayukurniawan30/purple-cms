<div id="modal-add-navigation" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($menuAdd, [
                'id'                    => 'form-add-navigation',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url' 					=> ['action' => 'ajax-add']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Add Navigation</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="uk-alert-primary" uk-alert>
                <p>Some themes might not support nested navigation, that makes making a child navigation useless.</p>
            </div>

            <div class="form-group">
                <?php
                    echo $this->Form->label('title', 'Navigation Title');
                    echo $this->Form->text('title', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Navigation Title. Max 100 chars',
                        'data-parsley-maxlength' => '100',
                        'autofocus'              => 'autofocus',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
            <div class="form-group">
                <?php
                    if ($menus->count() > 0):
                        $menuListing = array();
                        foreach ($menus as $parent) {
                            $menuListing[$parent->id] = $parent->title;
                        }

                        echo $this->Form->label('parent', 'Parent');
                        echo $this->Form->select(
                            'parent',
                            $menuListing,
                            [
                                'empty'    => 'Select Parent',
                                'class'    => 'form-control'
                            ]
                        );
                    else:
                        echo $this->Form->label('parent', 'Parent');
                        echo $this->Form->select(
                            'parent',
                            [],
                            [
                                'empty'    => 'Select Parent',
                                'class'    => 'form-control'
                            ]
                        );
                    endif;
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('status', 'Status');
                    echo $this->Form->select(
                        'status',
                        [
                            '0'    => 'Draft',
                            '1'  => 'Publish',
                        ],
                        [
                            'empty'    => 'Select Status',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('point', 'Pointing To');
                    echo $this->Form->select(
                        'point',
                        [
                            'pages'      => 'Page',
                            'customlink' => 'Custom Link',
                        ],
                        [
                            'empty'    => 'Select Pointing Target',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group bind-target-form"></div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-add-navigation',
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
    </div>
    <?php
        echo $this->Form->end();
    ?>
</div>