<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

<div class="row">
    <div class="col-md-<?= $menus->count() > 0 ? '6' : '12' ?> grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Navbar Menu</h4>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple" data-purple-modal="#modal-add-navigation">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Add Menu
                </button>
            </div>
            <div class="card-body uk-padding-remove">
                <?php
                    if ($menus->count() > 0):
                ?>
                <ul id="sortable-items" class="display-list" uk-sortable="handle: .uk-sortable-handle" data-purple-url="<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxReorderMenu"]); ?>" uk-grid>
                    <?php
                        $order = 1;
                        foreach ($menus as $menu):
                            $submenuUrl = $this->Url->build([
                                '_name'  => 'adminNavigationChild',
                                'parent' => $menu->id
                            ]);

                            if ($menu->page_id == NULL) {
                                $pointing  = 'customlink';
                                $target    = $menu->target;
                                $permalink = $target;
                            }
                            else {
                                $pointing  = 'pages';
                                $target    = $menu->page_id;
                                $permalink = $this->Url->build('/'.$menu->page->slug, true);
                            }
                    ?>
                    <li id="sortable-<?= $menu->id; ?>" class="uk-width-1-1 uk-margin-remove-top" data-order="<?= $order ?>" style="position: relative">
                        <div class="sortable-remover" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 5; display: none; background: rgba(255,255,255,.4)"></div>
                        <div class="uk-card uk-card-default uk-card-small uk-card-body <?php if ($menu->id == $this->request->getParam('parent')) echo 'selected-parent-navigation bg-success' ?>">
                            <span class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: menu"></span><?= $menu->title ?>

                            <div class="uk-inline uk-align-right">
                                <?php
                                    if ($menu->has_sub == '1'):
                                ?>
                                <a href="<?= $submenuUrl ?>" class="uk-margin-small-right" uk-icon="icon: list" uk-tooltip="Click to view child navigations"></a>
                                <?php endif; ?>
                                <a href="#" class="uk-margin-small-right <?php if ($menu->status == '0') echo 'text-secondary' ?>" uk-icon="icon: world" uk-tooltip="<?= $menu->text_status ?>"></a>
                                <a href="#" class="uk-margin-small-right" uk-icon="icon: user" uk-tooltip="<?= $menu->admin->get('display_name') ?>"></a>
                                <button class="uk-button uk-button-link"><span uk-icon="more-vertical"></span></button>
                                <div uk-dropdown="mode: click; pos: bottom-right">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <li><a href="#">Open</a></li>
                                        <li><a class="button-edit-navigation" href="#" data-purple-id="<?= $menu->id ?>" data-purple-navtype="menu" data-purple-title="<?= $menu->title ?>" data-purple-status="<?= $menu->status ?>" data-purple-point="<?= $pointing ?>" data-purple-hassub="<?= $menu->has_sub ?>" data-purple-target="<?= $target ?>" data-purple-modal="#modal-edit-navigation">Edit</a></li>
                                        <li><a class="button-get-permalink" href="#" data-purple-link="<?= $permalink ?>" data-purple-modal="#modal-show-permalink">Get Permalink</a></li>
                                        <li class="uk-nav-divider"></li>
                                        <?php
                                            if ($menu->has_sub == '1'):
                                        ?>
                                        <li><a class="button-disallowed-delete" href="#" data-purple-modal="#modal-disallowed-delete">Delete</a></li>
                                        <?php
                                            else:
                                        ?>
                                        <li><a class="button-delete-purple" href="#" data-purple-id="<?= $menu->id ?>" data-purple-name="<?= $menu->title ?>" data-purple-entity="menu" data-purple-modal="#modal-delete-navigation">Delete</a></li>
                                        <?php endif; ?>
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
                <div class="uk-alert-danger <?php if ($menus->count() == 0) echo 'uk-margin-remove-bottom'; ?>" uk-alert>
                    <p>Can't find menu for your website. You can add a new menu <a href="#" class="button-add-purple" data-purple-modal="#modal-add-navigation">here</a>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
        if ($this->request->getParam('parent') != NULL):
            echo $this->element('Dashboard/Navigation/child');
        endif;
    ?>
</div>

<?= $this->element('Dashboard/Modal/Navigation/add_modal'); ?>

<?php
    if ($menus->count() > 0) {
        echo $this->element('Dashboard/Modal/Navigation/edit_modal');
        echo $this->element('Dashboard/Modal/Navigation/delete_modal');
        echo $this->element('Dashboard/Modal/permalink_modal');
    }
?>

<script>
    $(document).ready(function() {
        $('select[name=point]').on('change', function() {
            var value = this.value;

            if (value == 'pages') {
                $(".bind-target-form").html('<?php
                    if ($pages->count() > 0):
                        $pageListing = array();
                        foreach ($pages as $page) {
                            $pageListing[$page->id] = $page->title;
                        }

                        echo $this->Form->select(
                            'target',
                            $pageListing,
                            [
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
                                'empty'    => 'Select Page',
                                'class'    => 'form-control',
                                'required' => 'required'
                            ]
                        );
                    endif;
                ?>');
            }
            else if (value == 'customlink') {
                $(".bind-target-form").html('<?php
                    echo $this->Form->text('target', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Custom Link',
                        'data-parsley-maxlength' => '150',
                        'required'               => 'required'
                    ]);
                ?>');
            }
        });

        var navigationAdd = {
            form            : 'form-add-navigation',
            button          : 'button-add-navigation',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+navigationAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(navigationAdd.form, navigationAdd.action, navigationAdd.redirectType, navigationAdd.redirect, navigationAdd.btnNormal, navigationAdd.btnLoading);
        })

        <?php
            if ($menus->count() > 0):
        ?>
            var navigationEdit = {
                form            : 'form-edit-navigation',
                button          : 'button-edit-navigation',
                action          : 'edit',
                redirectType    : 'redirect',
                redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
                btnNormal       : false,
                btnLoading      : false
            };

            var targetButton = $("#"+navigationEdit.button);
            targetButton.one('click',function() {
                ajaxSubmit(navigationEdit.form, navigationEdit.action, navigationEdit.redirectType, navigationEdit.redirect, navigationEdit.btnNormal, navigationEdit.btnLoading);
            })

            var navigationDelete = {
                form            : 'form-delete-navigation',
                button          : 'button-delete-navigation',
                action          : 'delete',
                redirectType    : 'redirect',
                redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
                btnNormal       : false,
                btnLoading      : false
            };

            var targetButton = $("#"+navigationDelete.button);
            targetButton.one('click',function() {
                ajaxSubmit(navigationDelete.form, navigationDelete.action, navigationDelete.redirectType, navigationDelete.redirect, navigationDelete.btnNormal, navigationDelete.btnLoading);
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
                        var createToast = notifToast('Reordering Menus', 'Success reordering menus', 'success', true);
                    }
                    else {
                        var createToast = notifToast('Reordering Menus', 'There is an error with Purple. Please try again', 'error', true);
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

        <?php
            if ($this->request->getParam('parent') != NULL):
                if ($submenus->count() > 0):
        ?>
            UIkit.util.on('#sortable-child-items', 'stop', function () {
                var h = [];
                var parent = <?= $this->request->getParam('parent') ?>;
                $("#sortable-child-items>li").each(function() {
                    h.push($(this).attr('id').substr(9));
                });
                var data  = {parent:parent, order: h + ""},
                    url   = $("#sortable-child-items").data('purple-url');
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
                        $("#sortable-child-items>li .uk-sortable-handle").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
                        $("#sortable-child-items>li .sortable-remover").show();
                    }
                });
                ajaxProcessing.done(function(msg) {
                    if (cakeDebug == 'on') {
                        console.log(msg);
                    }

                    var json    = $.parseJSON(msg),
                        status  = (json.status);

                    if (status == 'ok') {
                        $("#sortable-child-items>li .sortable-remover").hide();
                        $("#sortable-child-items>li .uk-sortable-handle").html('<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"> <rect x="2" y="4" width="16" height="1"></rect> <rect x="2" y="9" width="16" height="1"></rect> <rect x="2" y="14" width="16" height="1"></rect></svg>');
                        var createToast = notifToast('Reordering Submenus', 'Success reordering submenus', 'success', true);
                    }
                    else {
                        var createToast = notifToast('Reordering Submenus', 'There is an error with Purple. Please try again', 'error', true);
                    }
                });
                ajaxProcessing.fail(function(jqXHR, textStatus) {
                    var createToast = notifToast(jqXHR.statusText, 'There is an error with Purple. Please try again', 'error', true);
                });
                ajaxProcessing.always(function () {
                    $('input, button, textarea, select').prop("disabled", false);
                });
            });
        <?php
                endif;
            endif;
        ?>
    });
</script>
