<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div id="page-detail-card" class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Page Detail</h4>
            </div>
            <div class="card-body">
                <?php
                    if ($this->request->getParam('type') == 'general'):
                        $formUrl = 'ajax-save';
                        echo $this->element('Dashboard/Pages/general');
                    elseif ($this->request->getParam('type') == 'blog'):
                        if (empty($this->request->getParam('category'))):
                            echo $this->element('Dashboard/Pages/blog');
                        else:
                            echo $this->element('Dashboard/Pages/blog_filter_category');
                        endif;
                    elseif ($this->request->getParam('type') == 'custom'):
                        $formUrl = 'ajax-save-custom-page';
                        echo $this->element('Dashboard/Pages/custom');
                    endif;
                ?>
            </div>
        </div>
    </div>
</div>

<?= $this->element('Dashboard/Modal/browse_images_modal', [
        'selected'     => '',
        'browseMedias' => $browseMedias
]) ?>

<?php
    if ($this->request->getParam('type') != 'blog'):
?>
<div id="modal-save-page" class="uk-flex-top purple-modal" uk-modal="bg-close: false">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($pageSave, [
                'id'                    => 'form-save-page',
                'class'                 => 'pt-3',
                'data-parsley-validate' => '',
                'url'                   => ['action' => $formUrl]
            ]);

            echo $this->Form->hidden('id', ['value' => $pageId]);
            echo $this->Form->hidden('title', ['required', 'required']);
            echo $this->Form->hidden('content');
            echo $this->Form->hidden('css-content');
            echo $this->Form->hidden('meta_keywords');
            echo $this->Form->hidden('meta_description');
        ?>
        <div class=" uk-modal-body">
            <p>Are you sure want to save <span class="bind-title"><?= $pageTitle ?></span>?</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Save', [
                    'id'    => 'button-save-page',
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
        var pageSave = {
            form            : 'form-save-page',
            button          : 'button-save-page',
            action          : 'edit',
            redirectType    : 'redirect',
            redirect        : '<?= $_SERVER['REQUEST_URI'] ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+pageSave.button);
        targetButton.one('click',function() {
            window.onbeforeunload = null;
            ajaxSubmit(pageSave.form, pageSave.action, pageSave.redirectType, pageSave.redirect, pageSave.btnNormal, pageSave.btnLoading, false, true);
        })
    })
</script>
<?php
    endif;
?>