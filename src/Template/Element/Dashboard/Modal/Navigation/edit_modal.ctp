<div id="modal-edit-navigation" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($menuEdit, [
                'id'                    => 'form-edit-navigation',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url' 					=> ['action' => 'ajax-update']
            ]);

            echo $this->Form->hidden('id');
            echo $this->Form->hidden('navtype');
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Edit Navigation</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="uk-alert-primary" uk-alert>
                <p>If your navigation has sub navigation(s), targeting navigation to Page or Custom Link is not used.</p>
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
                            'empty'    => 'Select Point',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group bind-target-form">
                <?php
                    if ($pages->count() > 0):
                        $pageListing = array();
                        foreach ($pages as $page) {
                            $pageListing[$page->id] = $page->title;
                        }

                        echo $this->Form->select(
                            'target',
                            $pageListing,
                            [
                                'id'       => 'select-target-form',
                                'empty'    => 'Select Page',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    else:
                        echo $this->Form->select(
                            'target',
                            [],
                            [
                                'id'       => 'select-target-form',
                                'empty'    => 'Select Page',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    endif;

                    echo $this->Form->text('target', [
                        'id'                     => 'input-target-form',
                        'class'                  => 'form-control',
                        'placeholder'            => 'Custom Link',
                        'data-parsley-maxlength' => '150',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-edit-navigation',
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