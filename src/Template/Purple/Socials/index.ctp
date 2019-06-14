<?= $this->Html->css('/master-assets/plugins/jssocials/css/jssocials-theme-'.$socialButtonsTheme.'.css') ?>

<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Socials</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple" data-purple-modal="#modal-add-social">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Add Social Media
                </button>
            </div>
            <div class="card-body uk-padding-remove">
                <?php
                    if ($socials->count() > 0):
                ?>
                <ul id="sortable-items" class="" uk-sortable="handle: .uk-sortable-handle" data-purple-url="<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxReorder"]); ?>" uk-grid>
                    <?php
                        $order = 1;
                        foreach ($socials as $social):
                        	if ($social->name == 'google-plus') {
                        		$socialType = 'Google+';
                        	}
                        	else {
                        		$socialType = ucwords($social->name);
                        	}
                    ?>
                    <li id="sortable-<?= $social->id; ?>" class="uk-width-1-1 uk-margin-remove-top" data-order="<?= $order ?>" style="position: relative">
                        <div class="sortable-remover" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 5; display: none; background: rgba(255,255,255,.4)"></div>
                        <div class="uk-card uk-card-default uk-card-small uk-card-body">
                            <span class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: menu"></span> <?= $socialType ?>

                            <div class="uk-inline uk-align-right">
	                            <a href="<?= $social->link ?>" target="_blank" class="uk-margin-small-right" uk-tooltip="Open Link" uk-icon="icon: <?= $social->name ?>"></a>
                                <button class="uk-button uk-button-link"><span uk-icon="more-vertical"></span></button>
                                <div uk-dropdown="mode: click; pos: bottom-right">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <li><a href="<?= $social->link ?>" target="_blank">Open</a></li>
                                        <li><a class="button-edit-social" href="#" data-purple-id="<?= $social->id ?>" data-purple-name="<?= $social->name ?>" data-purple-link="<?= $social->link ?>" data-purple-modal="#modal-edit-social">Edit</a></li>
                                        <li class="uk-nav-divider"></li>
                                        <li><a class="button-delete-purple" href="#" data-purple-id="<?= $social->id ?>" data-purple-name="<?= $social->name ?>" data-purple-modal="#modal-delete-social">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php
                        $order++;
                    endforeach;
                ?>
                </ul>
                <?php
                    else:
                ?>
                <div class="uk-alert-danger <?php if ($socials->count() == 0) echo 'uk-margin-remove-bottom'; ?>" uk-alert>
                    <p>Can't find social media for your website. You can add a new social media <a href="#" class="button-add-purple" data-purple-modal="#modal-add-social">here</a>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Content Sharing Buttons</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple" data-purple-modal="#modal-edit-sharing-buttons">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Edit Buttons
                </button>
            </div>
            <div class="card-body">
                <div id="content-sharing-butons" style="font-size: <?= $socialButtonsFontSize ?>px"></div>
            </div>
        </div>
    </div>
</div>

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
                    echo $this->Form->text('link', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Social Link',
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

<?php
    if ($socials->count() > 0):
?>
<div id="modal-edit-social" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($socialEdit, [
                'id'                    => 'form-edit-social',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url' 					=> ['action' => 'ajax-update']
            ]);

            echo $this->Form->hidden('id');
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Edit Social Media</h3>
        </div>
        <div class=" uk-modal-body">
        	<div class="form-group">
                <?php
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
                    echo $this->Form->text('link', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Social Link',
                        'data-parsley-type'      => 'url',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        <?php
            echo $this->Form->button('Save', [
                'id'    => 'button-edit-social',
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


<?= $this->element('Dashboard/Modal/delete_modal', [
        'action'     => 'social',
        'form'       => $socialDelete,
        'formAction' => 'ajax-delete'
]) ?>

<?php 
	endif;
?>

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

<script>
    $(document).ready(function() {
        $("#content-sharing-butons").jsSocials({
            showCount: <?php if ($socialButtonsCount == 'true') echo 'true'; else echo 'false' ?>,
            showLabel: <?php if ($socialButtonsLabel == 'true') echo 'true'; else echo 'false' ?>,
            url: "https://purple-cms.com",
            shares: [<?= $socialButtonsShare ?>]
        });

        $('#form-edit-sharing-buttons').find('select[name=theme] option[value="<?= $socialButtonsTheme ?>"]').attr("selected","selected");
        $('#form-edit-sharing-buttons').find('select[name=fontsize] option[value="<?= $socialButtonsFontSize ?>"]').attr("selected","selected");
        $('#form-edit-sharing-buttons').find('select[name=label] option[value="<?= $socialButtonsLabel ?>"]').attr("selected","selected");
        $('#form-edit-sharing-buttons').find('select[name=count] option[value="<?= $socialButtonsCount ?>"]').attr("selected","selected");

        var socialButtons = {
            form            : 'form-edit-sharing-buttons',
            button          : 'button-edit-sharing-buttons',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+socialButtons.button);
        targetButton.one('click',function() {
            ajaxSubmit(socialButtons.form, socialButtons.action, socialButtons.redirectType, socialButtons.redirect, socialButtons.btnNormal, socialButtons.btnLoading);
        })

    	var socialAdd = {
            form            : 'form-add-social',
            button          : 'button-add-social',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+socialAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(socialAdd.form, socialAdd.action, socialAdd.redirectType, socialAdd.redirect, socialAdd.btnNormal, socialAdd.btnLoading);
        })

        <?php
		    if ($socials->count() > 0):
		?>

	        var socialEdit = {
	            form            : 'form-edit-social',
	            button          : 'button-edit-social',
	            action          : 'edit',
	            redirectType    : 'redirect',
	            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
	            btnNormal       : false,
	            btnLoading      : false
	        };

	        var targetButton = $("#"+socialEdit.button);
	        targetButton.one('click',function() {
	            ajaxSubmit(socialEdit.form, socialEdit.action, socialEdit.redirectType, socialEdit.redirect, socialEdit.btnNormal, socialEdit.btnLoading);
	        })

	        var socialDelete = {
	            form            : 'form-delete-social',
	            button          : 'button-delete-social',
	            action          : 'delete',
	            redirectType    : 'redirect',
	            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
	            btnNormal       : false,
	            btnLoading      : false
	        };

	        var targetButton = $("#"+socialDelete.button);
	        targetButton.one('click',function() {
	            ajaxSubmit(socialDelete.form, socialDelete.action, socialDelete.redirectType, socialDelete.redirect, socialDelete.btnNormal, socialDelete.btnLoading);
	        })

	        UIkit.util.on('#sortable-items', 'stop', function () {
                var h = [];
                $("#sortable-items>li").each(function() {
                    h.push($(this).attr('id').substr(9));
                });
                var data  = {order: h + ""},
                    url   = $("#sortable-items").data('purple-url');
                    token = $('#csrf-ajax-token').val();

                $.ajax({
                    type: "POST",
                    url:  url,
                    headers : {
                        'X-CSRF-Token': token
                    },
                    data: data,
                    cache: false,
                    beforeSend: function() {
                        $("#sortable-items>li .uk-sortable-handle").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
                        $("#sortable-items>li .sortable-remover").show();
                    },
                    success: function(data) {
                        $("#sortable-items>li .sortable-remover").hide();
                        $("#sortable-items>li .uk-sortable-handle").html('<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"> <rect x="2" y="4" width="16" height="1"></rect> <rect x="2" y="9" width="16" height="1"></rect> <rect x="2" y="14" width="16" height="1"></rect></svg>');
                    }
                })
            });

    	<?php endif; ?>
	})
</script>    