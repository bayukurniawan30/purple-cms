<?= $this->Html->css('/master-assets/plugins/jssocials/css/jssocials-theme-'.$socialButtonsTheme.'.css') ?>

<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

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
                <ul id="sortable-items" class="display-list" uk-sortable="handle: .uk-sortable-handle" data-purple-url="<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxReorder"]); ?>" uk-grid>
                    <?php
                        $order = 1;
                        $instagramAccount = false;
                        foreach ($socials as $social):
                        	if ($social->name == 'google-plus') {
                        		$socialType = 'Google+';
                        	}
                        	else {
                        		$socialType = ucwords($social->name);
                            }
                            
                            if ($social->name == 'instagram' && $social->link != '') {
                                $instagramAccount = true;
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
</div>

<?php
    if ($socials->count() > 0 && $instagramAccount == true):
?>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Latest Instagram Posts</h4>
            </div>
            <div class="card-body <?= ($igMedias == NULL || $igMedias == false || count($igMedias) == 0) ? 'uk-padding-remove' : '' ?>">
                <?php
                    if ($igMedias == NULL || $igMedias == false || count($igMedias) == 0):
                ?>
                <div class="uk-alert-warning uk-margin-remove-bottom" uk-alert>
                    <p><strong>Private Account</strong> : Your account is private. Change to public to view your posts.</p>
                </div>
                <?php
                    else:
                ?>
                <div class="uk-child-width-1-6@s" uk-grid>
                    <?php
                        $i = 1;
                        foreach ($igMedias as $igMedia):
                    ?>
                    <div>
                        <a href="<?= $igMedia->getLink() ?>" target="_blank"><div class="uk-background-contain uk-height-medium uk-panel uk-box-shadow-small <?= $i == 1 ? 'target-height' : '' ?> ig-post" style="background-image: url(<?= 'data:image/jpg;base64,' . base64_encode(file_get_contents($igMedia->getImageHighResolutionUrl())) ?>);">
                        </div></a>
                    </div>
                    <?php
                            ++$i;
                        endforeach;
                    ?>
                </div>
                <?php
                    endif;
                ?>
            </div>
        </div>
    </div>
</div>
<?php
    endif;
?>

<div class="row">
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

<?= $this->element('Dashboard/Modal/Socials/add_modal'); ?>

<?php
    if ($socials->count() > 0):
        echo $this->element('Dashboard/Modal/Socials/edit_modal');
        echo $this->element('Dashboard/Modal/delete_modal', [
            'action'     => 'social',
            'form'       => $socialDelete,
            'formAction' => 'ajax-delete'
        ]);
	endif;
?>

<?= $this->element('Dashboard/Modal/Socials/sharing_modal'); ?>

<script>
    $(document).ready(function() {
        <?php
            if ($socials->count() > 0 && $instagramAccount == true):
        ?>
        var getHeight = $('.target-height').outerWidth();
        $('.ig-post').css('height', getHeight + 'px');
        <?php
            endif;
        ?>

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

                var ajaxProcessing = $.ajax({
                    type: "POST",
                    url:  url,
                    headers : {
                        'X-CSRF-Token': token
                    },
                    data: data,
                    cache: false,
                    beforeSend: function() {
                        $('input, button, textarea, select').prop("disabled", true);
                        $("#sortable-items>li .uk-sortable-handle").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
                        $("#sortable-items>li .sortable-remover").show();
                    }
                });
                ajaxProcessing.done(function(msg) {
                    if (cakeDebug == 'on') {
                        console.log(msg);
                    }

                    var json    = $.parseJSON(msg),
                        status  = (json.status);

                    if (status == 'ok') {
                        $("#sortable-items>li .sortable-remover").hide();
                        $("#sortable-items>li .uk-sortable-handle").html('<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"> <rect x="2" y="4" width="16" height="1"></rect> <rect x="2" y="9" width="16" height="1"></rect> <rect x="2" y="14" width="16" height="1"></rect></svg>');
                        var createToast = notifToast('Reordering Social Media Links', 'Success reordering social media links', 'success', true);
                    }
                    else {
                        var createToast = notifToast('Reordering Social Media Links', 'There is an error with Purple. Please try again', 'error', true);

                    }
                });
                ajaxProcessing.fail(function(jqXHR, textStatus) {
                    var createToast = notifToast(jqXHR.statusText, 'There is an error with Purple. Please try again', 'error', true);
                });
                ajaxProcessing.always(function () {
                    $('input, button, textarea, select').prop("disabled", false);
                });
            });

    	<?php endif; ?>
	})
</script>    