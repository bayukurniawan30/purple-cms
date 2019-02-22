<div class="uk-child-width-expand@s" uk-grid>
    <div class="uk-width-1-1">
        <div class="uk-card uk-card-default uk-card-small uk-margin-top">
            <div class="uk-card-header uk-padding-small">
                <h4 class="card-title"><small>Page Title</small></h4>
            </div>
            <div class="uk-card-body">
                <div class="form-group">
                    <input id="form-input-title" type="text" class="form-control" name="title" data-parsley-maxlength="100" placeholder="Page Title (Max 100 charecters)" value="<?= $pages->title ?>" required>
                </div>
            </div>
        </div>
    </div>

    <div id="purple-fdb-blocks-preview" class="uk-width-2-3">
        <div class="uk-card uk-card-default">
            <div class="uk-card-header uk-padding-small">
                <div class="uk-inline uk-align-left" style="margin-bottom: 1.5px; margin-top: 1.5px">
                    <h4 class="card-title" style="margin-bottom: 0;"><small>Code Editor</small></h4>
                </div>
                <div class="uk-inline uk-align-right fdb-button-option" style="margin-bottom: 1.5px; margin-top: 1.5px">
                    <a id="button-toggle-code-fullscreen" class="uk-margin-small-left" uk-tooltip="title: Toggle Fullscreen"><i class="mdi mdi-fullscreen"></i></a>
                    <span class="fdb-button-option-divider uk-margin-small-left">|</span>
                    <a id="button-save-page" uk-tooltip="title: Save Content" data-purple-modal="#modal-save-page" data-purple-page="custom" data-purple-content="#fdb-code-editor-ace" class="uk-margin-small-left"><i class="mdi mdi-content-save"></i> Save</a>
                </div>
            </div>
            <div class="uk-card-body" style="padding: 0">
                <textarea id="fdb-code-editor-ace" class="form-control uk-height-large" rows="4" style="width: 100%" name="content"><?= $code ?></textarea>
            </div>
        </div>
    </div>

    <div id="purple-fdb-blocks" class="uk-width-1-3">
        <div class="uk-card uk-card-default">
            <div class="uk-card-header uk-padding-small">
                <h4 class="card-title"><small>SEO (Search Engine Optimization)</small></h4>
            </div>
            <div class="uk-card-body">
                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input id="form-input-metakeywords" type="text" class="form-control" data-parsley-maxlength="100" placeholder="Meta Keywords (Best practice is 10 keyword phrases)" value="<?php if ($query != NULL) echo $query->meta_keywords ?>">
                </div>
                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea id="form-input-metadescription" class="form-control" rows="4" data-parsley-maxlength="150" placeholder="Meta Description (Max 150 chars)"><?php if ($query != NULL) echo $query->meta_description ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-show-information" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">For Your Information</h3>
        </div>
        <div class="uk-modal-body">
            Your code will be saved in .php format. Don't use a custom page template if you don't understand PHP, HTML, CSS, and Javascript.
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="btn btn-gradient-primary uk-modal-close" type="button">I Understand</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        <?php
            if ($code == '' || empty($code)):
        ?>
        UIkit.modal("#modal-show-information").show();
        <?php
            endif;
        ?>
        $('#fdb-code-editor-ace').ace({ theme: 'chrome', lang: 'html' })
    })
</script>