<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Pages</h4> 
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text button-add-purple" data-purple-modal="#modal-add-page">
                    <i class="mdi mdi-pencil btn-icon-prepend"></i>
                    Add Page
                </button>
            </div>
            <div class="card-body uk-padding-remove">
                <ul class="uk-grid-collapse purple-page-list non-uikit" uk-grid>
                    <!-- Homepage -->
                    <?php
                        $editUrl = $this->Url->build([
                            'controller' => $this->request->getParam('controller'),
                            'action'     => 'detail',
                            'type'       => 'general',
                            'id'         => 0,
                            'slug'       => 'purple-home-page-builder'
                        ]);
                    ?>
                    <li class="uk-width-1-1 <?php //if ($this->request->getQuery('u') == 'purple-home-page-builder') echo 'uk-animation-shake' ?>">
                        <div class="uk-card uk-card-default uk-card-small uk-card-body ">
                            <span class="uk-margin-small-right" uk-icon="icon: home"></span>Home
                            <div class="uk-inline uk-align-right">
                                <a href="#" class="uk-margin-small-right" uk-icon="icon: home" uk-tooltip="Your Homepage"></a>
                                <a href="#" class="uk-margin-small-right" uk-icon="icon: cog" uk-tooltip="Genaral (Block Editor)"></a>
                                <button class="uk-button uk-button-link"><span uk-icon="more-vertical"></span></button>
                                <div uk-dropdown="mode: click; pos: bottom-right">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <li><a href="<?= $this->Url->build('/', true); ?>" target="_blank">Open</a></li>
                                        <li><a href="<?= $editUrl ?>">Edit</a></li>
                                        <li><a class="button-get-permalink" href="#" data-purple-link="<?= $this->Url->build('/', true); ?>" data-purple-modal="#modal-show-permalink">Get Permalink</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <?php
                        if ($pages->count() > 0):
                            $initPage = 1;
                    ?>
                        <?php foreach ($pages as $page): ?>
                        <?php
                            $editUrl = $this->Url->build([
                                'controller' => $this->request->getParam('controller'),
                                'action'     => 'detail',
                                'type'       => $page->page_template->type,
                                'id'         => $page->id,
                                'slug'       => $page->slug
                            ]);
                            $openUrl = $this->Url->build('/'.$page->slug, true);
                            if ($page->page_template->type == 'general') {
                                $icon = 'move';
                            }
                            elseif ($page->page_template->type == 'blog') {
                                $icon = 'pencil';
                            }
                            elseif ($page->page_template->type == 'custom') {
                                $icon = 'code';
                            }
                        ?>
                        <li class="uk-width-1-1 <?php if ($this->request->getQuery('u') == $page->slug) echo 'uk-animation-shake' ?>">
                            <div class="uk-card uk-card-default uk-card-small uk-card-body" style="<?php if ($initPage == $pagesLimit) echo 'box-shadow: none' ?>">
                                <span class="uk-margin-small-right" uk-icon="icon: <?= $icon ?>"></span><?= $page->title ?>
                                <div class="uk-inline uk-align-right">
                                    <a href="#" class="uk-margin-small-right" uk-icon="icon: cog" uk-tooltip="<?= $page->page_template->name ?>"></a>
                                    <a href="#" class="uk-margin-small-right <?php if ($page->status == '0') echo 'text-secondary' ?>" uk-icon="icon: world" uk-tooltip="<?= $page->text_status ?>"></a>
                                    <a href="#" class="uk-margin-small-right" uk-icon="icon: user" uk-tooltip="<?= $page->admin->display_name ?>"></a>
                                    <button class="uk-button uk-button-link"><span uk-icon="more-vertical"></span></button>
                                    <div uk-dropdown="mode: click; pos: bottom-right">
                                        <ul class="uk-nav uk-dropdown-nav non-uikit">
                                            <li><a href="<?= $openUrl ?>" target="_blank">Open</a></li>
                                            <li><a href="<?= $editUrl ?>">Edit</a></li>
                                            <li><a href="#" class="button-change-page-status" data-purple-id="<?= $page->id ?>" data-purple-status="<?= $page->status ?>" data-purple-name="<?= $page->title ?>" data-purple-modal="#modal-change-page-status">Change Status</a></li>
                                            <li><a class="button-get-permalink" href="#" data-purple-link="<?= $this->Url->build('/'.$page->slug, true); ?>" data-purple-modal="#modal-show-permalink">Get Permalink</a></li>
                                            <li class="uk-nav-divider"></li>
                                            <li><a class="button-delete-purple text-danger" href="#" data-purple-id="<?= $page->id ?>" data-purple-name="<?= $page->title ?>" data-purple-type="<?= $page->page_template->type ?>" data-purple-modal="#modal-delete-page">Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php 
                                $fetchChild = $this->cell('Pages::childPages', [$page]);
                                echo $fetchChild;
                                $initPage++;
                            endforeach; 
                        ?>
                    <?php endif; ?>
                </ul>
            </div>
            <?php
                if ($pagesTotal > $pagesLimit):
            ?>
             <div class="card-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="uk-position-center-left">Page <?= $this->Paginator->counter() ?></div>
                        </div>
                        <div class="col-md-6">
                            <ul class="uk-pagination purple-pagination uk-margin-remove-bottom uk-flex-right">
                                <?php
                                    if ($this->Paginator->current() - 1 <= 0) {
                                        $previousUrl = [
                                            '_name'  => 'adminPagesPagination',
                                            'id'     => $this->Paginator->current() - 0
                                        ];
                                    }
                                    else {
                                        $previousUrl = [
                                            '_name'  => 'adminPagesPagination',
                                            'id'     => $this->Paginator->current() - 1
                                        ];
                                    }

                                    if ($this->Paginator->current() + 1 > $this->Paginator->total()) {
                                        $nextUrl = [
                                            '_name'  => 'adminPagesPagination',
                                            'id'     => $this->Paginator->current() + 0
                                        ];
                                    }
                                    else {
                                        $nextUrl = [
                                            '_name'  => 'adminPagesPagination',
                                            'id'     => $this->Paginator->current() + 1
                                        ];
                                    }

                                    echo $this->Paginator->prev('<span uk-pagination-previous class="uk-margin-small-right"></span> Previous', [
                                        'escape' => false,
                                    ]);
                                    // echo $this->Paginator->numbers();
                                    echo $this->Paginator->next('Next <span uk-pagination-next class="uk-margin-small-left"></span>', [
                                        'escape' => false,
                                    ]);
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>  
            <?php endif ?>
        </div>
    </div>
</div>

<div id="modal-add-page" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($pageAdd, [
                'id'                    => 'form-add-page',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url' 					=> ['action' => 'ajax-add']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Add Page</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->text('title', [
                        'class'                  => 'form-control',
                        'placeholder'            => 'Page Title',
                        'data-parsley-maxlength' => '100',
                        'autofocus'              => 'autofocus',
                        'required'               => 'required'
                    ]);
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'status',
                        [
                            '0' => 'Draft',
                            '1' => 'Publish',
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
                    echo $this->Form->select(
                        'parent',
                        $parentPages,
                        [
                            'empty'    => 'Select Parent',
                            'class'    => 'form-control'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'page_template_id',
                        $pageTemplates,
                        [
                            'empty'    => 'Select Page Template',
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
                'id'    => 'button-add-page',
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

<div id="modal-change-page-status" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($pageStatus, [
                'id'                    => 'form-change-status',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-change-status']
            ]);

            echo $this->Form->hidden('id');
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Change Status of <span class="bind-title"></span></h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'status',
                        [
                            '0' => 'Draft',
                            '1' => 'Publish',
                        ],
                        [
                            'empty'    => 'Select Status',
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
                'id'    => 'button-change-status',
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

<div id="modal-delete-page" class="uk-flex-top purple-modal" uk-modal="bg-close: false">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($pageDelete, [
                'id'                    => 'form-delete-page',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-delete']
            ]);

            echo $this->Form->hidden('id');
            echo $this->Form->hidden('page_type');
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to delete <span class="bind-title"></span>? Navigation that connected to this page will be deleted also.</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);

                echo $this->Form->button('Yes, Delete it', [
                    'id'    => 'button-delete-page',
                    'class' => 'btn btn-gradient-danger uk-margin-left'
                ]);
            ?>
        </div>
        <?php

            echo $this->Form->end();
        ?>
    </div>
</div>

<div id="modal-show-permalink" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Permalink</h3>
        </div>
        <div class="uk-modal-body">
            <div class="form-group">
                <div class="uk-inline" style="width: 100%">
                    <a title="Copy Permalink" class="uk-form-icon uk-form-icon-flip icon-copy-permalink" href="#" uk-icon="icon: copy" data-clipboard-target="#purple-permalink" uk-tooltip="title: Copy Permalink; pos: bottom"></a>
                    <input id="purple-permalink" class="uk-input" type="text" value="" readonly>
                </div>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="btn btn-gradient-primary button-open-page" type="button">Open Page</button>
            <button class="btn btn-outline-primary uk-margin-left uk-modal-close" type="button">Close</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        <?php
            if ($pagesTotal > $pagesLimit):
        ?>
        $('.purple-pagination .prev a').attr('href', '<?= $this->Url->build($previousUrl) ?>')
        $('.purple-pagination .next a').attr('href', '<?= $this->Url->build($nextUrl) ?>')
        <?php
            endif;
        ?>

        var pageAdd = {
            form            : 'form-add-page',
            button          : 'button-add-page',
            action          : 'add',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+pageAdd.button);
        targetButton.one('click',function() {
            ajaxSubmit(pageAdd.form, pageAdd.action, pageAdd.redirectType, pageAdd.redirect, pageAdd.btnNormal, pageAdd.btnLoading);
        })

        var pageStatus = {
            form            : 'form-change-status',
            button          : 'button-change-status',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('index')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+pageStatus.button);
        targetButton.one('click',function() {
            ajaxSubmit(pageStatus.form, pageStatus.action, pageStatus.redirectType, pageStatus.redirect, pageStatus.btnNormal, pageStatus.btnLoading);
        })

        var pageDelete = {
            form            : 'form-delete-page',
            button          : 'button-delete-page',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action')]); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+pageDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(pageDelete.form, pageDelete.action, pageDelete.redirectType, pageDelete.redirect, pageDelete.btnNormal, pageDelete.btnLoading);
        })
    })
</script>