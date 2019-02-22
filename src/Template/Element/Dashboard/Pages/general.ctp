<div class="uk-child-width-expand@s" uk-grid>
    <?php
        if ($this->request->getParam('id') == 0 && $this->request->getParam('slug') == 'purple-home-page-builder'):
    ?>
    <input id="form-input-title" type="hidden" class="" value="Home">
    <?php
        else:
    ?>
    <div class="uk-width-1-1">
        <div class="uk-card uk-card-default uk-card-small uk-margin-top">
            <div class="uk-card-header uk-padding-small">
                <h4 class="card-title"><small>Page Title</small></h4>
            </div>
            <div class="uk-card-body">
                <div class="form-group">
                    <input id="form-input-title" type="text" class="form-control" name="title" data-parsley-maxlength="100" placeholder="Page Title (Max 100 charecters)" value="<?= $pages->title ?>" required>
                </div>

                <?php
                    $objectUrl = $this->Url->build([
                        '_name' => 'adminPagesHtmlBlocks',
                        'file'  => $tmpFile
                    ]);
                ?>
                <!-- <object id="object-tmp-html" width="100%" height="500" data="<?= $objectUrl ?>"></object> -->
            </div>
        </div>
    </div>
    <?php
        endif;
    ?>
    <div id="purple-fdb-blocks" class="uk-width-1-3@m">
        <div class="uk-card uk-card-default uk-card-small" uk-filter="target: .js-filter">
            <div class="uk-card-header uk-padding-small">
                <h4 class="card-title"><small>Blocks</small>
                    <div class="uk-inline uk-align-right" style="margin-bottom: 0">
                        <button class="uk-button uk-button-link"><i class="mdi mdi-filter-outline"></i></button>
                        <div uk-dropdown="mode: click; pos: bottom-right">
                            <ul class="uk-nav uk-dropdown-nav">
                                <li uk-filter-control><a href="#">All</a></li>
                                <li class="uk-nav-divider"></li>
                                <li uk-filter-control=".fdb-call-to-action"><a href="#">Call to Action</a></li>
                                <li uk-filter-control=".fdb-contents"><a href="#">Contents</a></li>
                                <li uk-filter-control=".fdb-features"><a href="#">Features</a></li>
                                <li uk-filter-control=".fdb-teams"><a href="#">Teams</a></li>
                                <li uk-filter-control=".fdb-contacts"><a href="#">Contacts</a></li>
                                <li uk-filter-control=".uikit-lightbox"><a href="#">Lightbox</a></li>
                                <li uk-filter-control=".uikit-slider"><a href="#">Slider</a></li>
                                <li class="uk-nav-divider"></li>
                                <li uk-filter-control=".theme-blocks"><a href="#">Theme Blocks <span class="mdi mdi-information-outline text-primary" uk-tooltip="Special Blocks from active Theme. The looks of the theme block might be slightly different from what is seen on the front-end page because the .css file from the used theme. Please remove the block if you change the active theme. Only use block from active theme."></span></a></li>
                            </ul>
                        </div>
                    </div>
                </h4>
            </div>

            <div class="uk-card-body">
                <div class="js-filter uk-height-large" style="overflow-y: scroll">
                    <!-- Froala Blocks => Call to Action -->
                    <?php foreach ($fdbCallToAction as $blocksCallToAction): ?>
                    <?php
                        $fullImage = $this->request->getAttribute("webroot") . 'master-assets/plugins/froala-blocks/images/call-to-action/' . $blocksCallToAction;
                        $number    = str_replace('.jpg', '', $blocksCallToAction);
                    ?>
                    <div class="fdb-image-container uk-margin-small-bottom fdb-call-to-action fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="call_to_action" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                        <img src="<?= $fullImage ?>" class="img-fluid" width="100%">
                    </div>
                    <?php endforeach; ?>

                    <hr>

                    <!-- Froala Blocks => Contents -->
                    <?php foreach ($fdbContents as $blocksContents): ?>
                    <?php
                        $fullImage = $this->request->getAttribute("webroot") . 'master-assets/plugins/froala-blocks/images/contents/' . $blocksContents;
                        $number    = str_replace('.jpg', '', $blocksContents);
                    ?>
                    <div class="fdb-image-container uk-margin-small-bottom fdb-contents fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="contents" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                        <img src="<?= $fullImage ?>" class="img-fluid" width="100%">
                    </div>
                    <?php endforeach; ?>

                    <!-- Froala Blocks => Features -->
                    <?php foreach ($fdbFeatures as $blocksFeatures): ?>
                    <?php
                        $fullImage = $this->request->getAttribute("webroot") . 'master-assets/plugins/froala-blocks/images/features/' . $blocksFeatures;
                        $number    = str_replace('.jpg', '', $blocksFeatures);
                    ?>
                    <div class="fdb-image-container uk-margin-small-bottom fdb-features fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="features" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                        <img src="<?= $fullImage ?>" class="img-fluid" width="100%">
                    </div>
                    <?php endforeach; ?>

                    <!-- Froala Blocks => Teams -->
                    <?php foreach ($fdbTeams as $blocksTeams): ?>
                    <?php
                        $fullImage = $this->request->getAttribute("webroot") . 'master-assets/plugins/froala-blocks/images/teams/' . $blocksTeams;
                        $number    = str_replace('.jpg', '', $blocksTeams);
                    ?>
                    <div class="fdb-image-container uk-margin-small-bottom fdb-teams fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="teams" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                        <img src="<?= $fullImage ?>" class="img-fluid" width="100%">
                    </div>
                    <?php endforeach; ?>

                    <!-- Froala Blocks => Contacts -->
                    <?php foreach ($fdbContacts as $blocksContacts): ?>
                    <?php
                        $fullImage = $this->request->getAttribute("webroot") . 'master-assets/plugins/froala-blocks/images/contacts/' . $blocksContacts;
                        $number    = str_replace('.jpg', '', $blocksContacts);
                    ?>
                    <div class="fdb-image-container uk-margin-small-bottom fdb-contacts fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="contacts" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                        <img src="<?= $fullImage ?>" class="img-fluid" width="100%">
                    </div>
                    <?php endforeach; ?>

                    <!-- Froala Blocks => Lightbox -->
                    <?php foreach ($fdbLightbox as $blocksLightbox): ?>
                    <?php
                        $fullImage = $this->request->getAttribute("webroot") . 'master-assets/plugins/froala-blocks/images/lightbox/' . $blocksLightbox;
                        $number    = str_replace('.jpg', '', $blocksLightbox);
                    ?>
                    <div class="fdb-image-container uk-margin-small-bottom uikit-lightbox fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="lightbox" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                        <img src="<?= $fullImage ?>" class="img-fluid" width="100%">
                    </div>
                    <?php endforeach; ?>

                    <!-- Froala Blocks => Slider -->
                    <?php foreach ($fdbSlider as $blocksSlider): ?>
                    <?php
                        $fullImage = $this->request->getAttribute("webroot") . 'master-assets/plugins/froala-blocks/images/slider/' . $blocksSlider;
                        $number    = str_replace('.jpg', '', $blocksSlider);
                    ?>
                    <div class="fdb-image-container uk-margin-small-bottom uikit-slider fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="slider" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                        <img src="<?= $fullImage ?>" class="img-fluid" width="100%">
                    </div>
                    <?php endforeach; ?>

                    <!-- Theme Blocks -->
                    <?php 
                        if ($themeBlocks):
                            foreach ($themeBlocks as $themeBlock): 
                                $fileBlock   = file_get_contents(PLUGINS .'EngageTheme/webroot/blocks/'.$themeBlock);
                                $decodeBlock = json_decode($fileBlock, true);

                                foreach($decodeBlock["options"] as $options):
                    ?>
                        <div class="fdb-image-container uk-margin-small-bottom theme-blocks fdb-blocks" data-purple-number="<?= $number ?>" data-purple-filter="<?= $themeBlock ?>" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxThemeBlocks"]); ?>" data-purple-urlreload="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaBlocksReload"]); ?>">
                            <div class="uk-card uk-card-default uk-card-body uk-text-center bg-primary text-white"><strong><?= $options['theme'] ?></strong> Theme Block<br><small><?= $options['name'] ?></small></div>
                        </div>
                    <?php 
                                endforeach; 
                            endforeach; 
                        endif;
                    ?>
                </div>
            </div>
        </div>

        <?php
            if ($this->request->getParam('id') == 0 && $this->request->getParam('slug') == 'purple-home-page-builder'):
        ?>
        <input id="form-input-metakeywords" type="hidden" class="" value="">
        <input id="form-input-metadescription" type="hidden" class="" value="">
        <?php 
            else:
        ?>
        <div class="uk-card uk-card-default uk-card-small uk-margin-top">
            <div class="uk-card-header uk-padding-small">
                <h4 class="card-title"><small>SEO (Search Engine Optimization)</small></h4>
            </div>
            <div class="uk-card-body">
                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input id="form-input-metakeywords" type="text" class="form-control" data-parsley-maxlength="100" placeholder="Meta Keywords (Best practice is 10 keyword phrases)" value="<?php if ($query != NULL) echo $query->general->meta_keywords ?>">
                </div>
                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea id="form-input-metadescription" class="form-control" rows="4" data-parsley-maxlength="150" placeholder="Meta Description (Max 150 chars)"><?php if ($query != NULL) echo $query->general->meta_description ?></textarea>
                </div>
            </div>
        </div>
        <?php
            endif;
        ?>
    </div>

    <div id="purple-fdb-blocks-preview" class="uk-width-2-3">
        <div class="uk-card uk-card-default">
            <div class="uk-card-header uk-padding-small" uk-sticky="offset: 70" style="background-color: #ffffff">
                <div class="uk-inline uk-align-left fdb-button-option" style="margin-bottom: 1.5px; margin-top: 1.5px">
                    <a id="button-toggle-blocks" class="" uk-tooltip="title: Show/Hide Blocks"><i class="mdi mdi-menu"></i></a>
                    <a id="button-toggle-fullscreen" class="uk-margin-small-left" uk-tooltip="title: Toggle Fullscreen"><i class="mdi mdi-fullscreen"></i></a>
                    <?php
                        if ($this->request->getParam('id') == 0 && $this->request->getParam('slug') == 'purple-home-page-builder'):
                    ?>
                    <a id="button-toggle-code" uk-tooltip="title: Edit HTML Code" data-purple-modal="#modal-fdb-code-editor" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaCodeEditor"]); ?>" data-purple-id="0" data-purple-actionurl="<?= $this->Url->build(['controller' => $this->request->getParam('controller'), 'action' => 'ajaxSave']); ?>" data-purple-redirect="<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action'), 'type' => 'general', 'slug' => $this->request->getParam('slug')]); ?>" class="uk-margin-small-left"><i class="mdi mdi-code-tags"></i></a>
                    <?php
                        else:
                    ?>
                    <a id="button-toggle-code" uk-tooltip="title: Edit HTML Code" data-purple-modal="#modal-fdb-code-editor" data-purple-url="<?= $this->Url->build(["controller" => "Pages", "action" => "ajaxFroalaCodeEditor"]); ?>" data-purple-id="<?php echo $pages->id ?>" data-purple-actionurl="<?= $this->Url->build(['controller' => $this->request->getParam('controller'), 'action' => 'ajaxSave']); ?>" data-purple-redirect="<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => $this->request->getParam('action'), 'type' => $pages->page_template->type, 'slug' => $pages->slug]); ?>" class="uk-margin-small-left"><i class="mdi mdi-code-tags"></i></a>
                <?php endif; ?>
                    <a id="button-toggle-tuning" uk-tooltip="title: Enable/Disable Tuning Mode" data-purple-active="yes" class="uk-margin-small-left active"><i class="mdi mdi-tune"></i>
                    <a id="button-toggle-editing" uk-tooltip="title: Enable/Disable Editing Mode" data-purple-active="no" class="uk-margin-small-left"><i class="mdi mdi-pencil"></i></a>

                    <span class="preview-screen-modifier" style="display: none">
                        <span class="fdb-button-screen-divider uk-margin-small-left">|</span>
                        <a id="button-toggle-desktop-screen" class="uk-margin-small-left" uk-tooltip="title: Desktop Screen"><i class="mdi mdi-desktop-mac"></i></a>
                        <a id="button-toggle-tablet-screen" class="uk-margin-small-left" uk-tooltip="title: Tablet Screen"><i class="mdi mdi-tablet-android"></i></a>
                        <a id="button-toggle-phone-screen" class="uk-margin-small-left" uk-tooltip="title: Mobile Screen"><i class="mdi mdi-cellphone-android"></i></a>
                    </span>

                    <span class="fdb-button-option-divider uk-margin-small-left">|</span>
                    <a id="button-save-page" uk-tooltip="title: Save Content" data-purple-modal="#modal-save-page" data-purple-page="general" data-purple-content="#bind-fdb-blocks" class="uk-margin-small-left"><i class="mdi mdi-content-save"></i> Save</a>
                </div>
                <span class="uk-align-right fdb-blocks-mode"><small>Tuning Mode</small></span>
            </div>
            <div id="bind-fdb-blocks" class="uk-card-body uk-padding-remove" uk-sortable="handle: .uk-sortable-handle">
                <?php
                    if ($this->request->getParam('id') == 0 && $this->request->getParam('slug') == 'purple-home-page-builder'):
                        if ($homePageContent == '') 
                            echo '<div class="fdb-blocks-empty text-center" uk-alert>Empty Content</div>'; 
                        else 
                            echo $homePageContent;
                    else:
                        if ($query == NULL):
                ?>
                <div class="fdb-blocks-empty text-center" uk-alert>Empty Content</div>
                <?php
                        else:
                            echo html_entity_decode($query->general->content);
                        endif;
                    endif;
                ?>
            </div>
            <div id="bottom-of-builder">&nbsp;</div>
        </div>
    </div>
</div>

<div id="modal-fdb-code-editor" class="uk-modal-container purple-modal" uk-modal="bg-close: false">
    <div class="uk-modal-dialog">
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Edit HTML Code</h2>
        </div>
        <?php
            echo $this->Form->create($pageSave, [
                'id'                    => 'form-save-html',
                'class'                 => '',
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-save']
            ]);

            echo $this->Form->hidden('id', ['value' => $this->request->getParam('id')]);
        ?>
        <div class="uk-modal-body uk-padding-remove">

        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Save', [
                    'id'    => 'button-save-html',
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

<div id="modal-show-information" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">For Your Information</h3>
        </div>
        <div class="uk-modal-body">
            <?php
                if ($this->request->getParam('id') == 0 && $this->request->getParam('slug') == 'purple-home-page-builder'):
            ?>
            <p>For some themes, They have their own home page template, so the result of this home page builder will not be used.</p>
            <?php endif; ?>
            <p>The display on the editor's screen might be slightly different from what is seen on the front-end page because the .css file from the used theme.</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="btn btn-gradient-primary uk-modal-close" type="button">I Understand</button>
        </div>
    </div>
</div>

<!-- Color Picker for Froala Blocks -->
<?= $this->element('Dashboard/Modal/change_bgcolor_modal') ?>

<!-- UIkit Slideshow Tuning -->
<?= $this->element('Dashboard/Modal/uikit_slider_tuning_modal') ?>

<!-- UIkit Animation -->
<?= $this->element('Dashboard/Modal/uikit_add_animation_modal') ?>

<!-- Font Awesome Icons for Froala Blocks -->
<?= $this->element('Dashboard/Modal/font_awesome_icons_modal') ?>

<!-- Buttons Customizing for Froala Blocks -->
<?= $this->element('Dashboard/Modal/buttons_customizing_modal') ?>

<script>
    $(document).ready(function() {
        UIkit.modal("#modal-show-information").show();

        var pageHtml = {
            form            : 'form-save-html',
            button          : 'button-save-html',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $this->Url->build(["controller" => $this->request->getParam('controller'), "action" => 'index']); ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+pageHtml.button);
        targetButton.one('click',function() {
            ajaxSubmit(pageHtml.form, pageHtml.action, pageHtml.redirectType, pageHtml.redirect, pageHtml.btnNormal, pageHtml.btnLoading);
        })
    })
</script>