<div id="modal-edit-sharing-buttons" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($socialButtons, [
                'id'                    => 'form-edit-sharing-buttons',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-sharing-buttons']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Edit Sharing Buttons</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->label('theme', 'Theme');
                    echo $this->Form->select(
                        'theme',
                        [
                            'flat'    => 'Flat',
                            'classic' => 'Classic',
                            'minima'  => 'Minima',
                            'plain'   => 'Plain'
                        ],
                        [
                            'empty'    => 'Select Theme',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('fontsize', 'Font Size');
                    echo $this->Form->select(
                        'fontsize',
                        [
                            '8'  => '8',
                            '10' => '10',
                            '12' => '12',
                            '14' => '14',
                            '16' => '16',
                            '18' => '18'
                        ],
                        [
                            'empty'    => 'Select Font Size',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('label', 'Button Label');
                    echo $this->Form->select(
                        'label',
                        [
                            'true'  => 'Show',
                            'false' => "Don't show"
                        ],
                        [
                            'empty'    => 'Select Label',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('count', 'Counter');
                    echo $this->Form->select(
                        'count',
                        [
                            'true'  => 'Show',
                            'false' => "Don't show"
                        ],
                        [
                            'empty'    => 'Select Counter',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Save', [
                    'id'    => 'button-edit-sharing-buttons',
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