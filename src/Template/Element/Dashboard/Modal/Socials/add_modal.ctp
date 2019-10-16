<div id="modal-add-social" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($socialAdd, [
                'id'                    => 'form-add-social',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url' 					=> ['action' => 'ajax-add']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Add Social Media</h3>
        </div>
        <div class=" uk-modal-body">
        	<div class="form-group">
                <?php
                    echo $this->Form->label('name', 'Select Social Media');
                    echo $this->Form->select(
                        'name',
                        [
							'facebook'    => 'Facebook',
							'instagram'   => 'Instagram',
							'twitter'     => 'Twitter',
							'google-plus' => 'Google+',
							'youtube'     => 'Youtube',
							'pinterest'   => 'Pinterest',
							'github'      => 'Github'
                        ],
                        [
                            'empty'    => 'Select Social Media',
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->label('link', 'Social Link');
                    echo $this->Form->text('link', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Social Link. E.g. https://facebook.com/youraccount',
                        'data-parsley-type'      => 'url',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-add-social',
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