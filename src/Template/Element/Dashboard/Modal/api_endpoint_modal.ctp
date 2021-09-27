<div id="modal-api-endpoint" class="uk-flex-top uk-modal-container purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">API Endpoint</h3>
        </div>
        <div class="uk-modal-body">
            <div class="uk-alert-primary uk-margin-bottom" uk-alert>
                <a class="uk-alert-close" uk-close></a>
                <p>
                    Read the API documentation <a href="https://bayukurniawan30.github.io/purple-cms/#/" target="_blank">here</a> to know how to use this api endpoint.
                </p>
            </div>
            <div class="form-group">
                <div class="uk-inline" style="width: 100%">
                    <a title="Copy Permalink" class="uk-form-icon uk-form-icon-flip icon-copy-permalink" href="#" uk-icon="icon: copy" data-clipboard-target="#purple-permalink" uk-tooltip="title: Copy Permalink; pos: bottom"></a>
                    <input id="purple-permalink" class="uk-input" type="text" value="<?= $url ?>" readonly>
                </div>
            </div>

            <!--<pre><?= $apiResponse ?></pre>-->
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="btn btn-outline-primary uk-margin-left uk-modal-close" type="button">Close</button>
        </div>
    </div>
</div>