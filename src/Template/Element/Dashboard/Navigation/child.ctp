<div class="col-md-6 grid-margin ">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title uk-margin-remove-bottom">Child Navigation of <?= $activeParent->title ?></h4>
        </div>
        <div class="card-body <?php if ($submenus->count() > 0) echo 'uk-padding-remove' ?>">
            <?php
                if ($submenus->count() > 0):
            ?>
            <ul id="sortable-child-items" class="" uk-sortable="handle: .uk-sortable-handle" data-purple-url="<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => "ajaxReorderSubmenu"]); ?>" uk-grid>
                <?php
                    $order = 1;
                    foreach ($submenus as $submenu):
                        if ($submenu->page_id == NULL) {
                            $pointing  = 'customlink';
                            $target    = $submenu->target;
                            $permalink = $target;
                        }
                        else {
                            $pointing  = 'pages';
                            $target    = $submenu->page_id;
                            $permalink = $this->Url->build('/'.$submenu->page->slug, true);
                        }
                ?>
                <li id="sortable-<?= $submenu->id; ?>" class="uk-width-1-1 uk-margin-remove-top" data-order="<?= $order ?>" style="position: relative">
                    <div class="sortable-remover" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 5; display: none; background: rgba(255,255,255,.4)"></div>
                    <div class="uk-card uk-card-default uk-card-small uk-card-body ">
                        <span class="uk-sortable-handle uk-margin-small-right" uk-icon="icon: menu"></span><?= $submenu->title ?>

                        <div class="uk-inline uk-align-right">
                            <a href="#" class="uk-margin-small-right <?php if ($submenu->status == '0') echo 'text-secondary' ?>" uk-icon="icon: world" uk-tooltip="<?= $submenu->text_status ?>"></a>
                            <a href="#" class="uk-margin-small-right" uk-icon="icon: user" uk-tooltip="<?= $submenu->admin->get('display_name') ?>"></a>
                            <button class="uk-button uk-button-link"><span uk-icon="more-vertical"></span></button>
                            <div uk-dropdown="mode: click; pos: bottom-right">
                                <ul class="uk-nav uk-dropdown-nav">
                                    <li><a href="#">Open</a></li>
                                    <li><a class="button-edit-navigation" href="#" data-purple-id="<?= $submenu->id ?>" data-purple-navtype="submenu" data-purple-title="<?= $submenu->title ?>" data-purple-status="<?= $submenu->status ?>" data-purple-point="<?= $pointing ?>" data-purple-target="<?= $target ?>" data-purple-modal="#modal-edit-navigation">Edit</a></li>
                                    <li><a class="button-get-permalink" href="#" data-purple-link="<?= $permalink ?>" data-purple-modal="#modal-show-permalink">Get Permalink</a></li>
                                    <li class="uk-nav-divider"></li>
                                    <li><a class="button-delete-purple" href="#" data-purple-id="<?= $submenu->id ?>" data-purple-name="<?= $submenu->title ?>" data-purple-entity="submenu" data-purple-modal="#modal-delete-navigation">Delete</a></li>
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
            <div class="uk-alert-danger" uk-alert>
                <p>Can't find child navigation. You can add a new child navigation <a href="#" class="button-add-purple" data-purple-modal="#modal-add-navigation">here</a>.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>