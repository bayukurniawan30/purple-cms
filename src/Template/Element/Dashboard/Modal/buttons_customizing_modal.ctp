<div id="modal-bttns-customizing" class="uk-flex-top purple-modal" uk-modal style="z-index: 99999999">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Button Customizing</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="uk-alert-primary" uk-alert>
                <p><strong>Note</strong> : Use editing mode to edit target url(href attribute).</p>
            </div>

            <div class="form-group">
                <label>Text</label>
                <input class="form-control" placeholder="Text" type="text" name="text" autofocus>
            </div>
            <div class="form-group">
                <label>Style</label>
                <select class="form-control" name="style">
                    <option value="default">Default</option>
                    <option value="bttn-simple">Simple</option>
                    <option value="bttn-material-flat">Material Flat</option>
                    <option value="bttn-gradient">Gradient</option>
                    <option value="bttn-stretch">Stretch</option>
                    <option value="bttn-minimal">Minimal</option>
                    <option value="bttn-bordered">Bordered</option>
                </select>
            </div>
            <div class="form-group">
                <label>Size</label>
                <select class="form-control" name="size">
                    <option value="default">Default</option>
                    <option value="bttn-sm">Small</option>
                    <option value="bttn-md">Medium</option>
                    <option value="bttn-lg">Large</option>
                </select>
            </div>
            <div class="form-group">
                <label>Base</label>
                <select class="form-control" name="base">
                    <option value="default">Default</option>
                    <option value="bttn-primary">Primary</option>
                    <option value="bttn-warning">Warning</option>
                    <option value="bttn-danger">Danger</option>
                    <option value="bttn-success">Success</option>
                    <option value="bttn-royal">Royal</option>
                </select>
            </div>
            <div class="form-group">
                <label>Icon</label>
                <div class="input-group">
                    <input id="attach-icon-for-button" type="text" class="form-control" name="icon" placeholder="Browse Icon" aria-label="Browse Icon" aria-describedby="basic-addon2" readonly="readonly">
                    <div class="input-group-append">
                        <button id="browse-icon-for-button" class="btn btn-sm btn-gradient-primary" type="button" data-purple-source="button-customizing">Browse</button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Icon Position</label>
                <select class="form-control" name="icon-position">
                    <option value="front">Front</option>
                    <option value="back">Back</option>
                </select>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button id="button-bttns-customizing" class="btn btn-gradient-primary button-bttns-customizing" type="button">Save</button>
            <button class="btn btn-outline-primary uk-margin-left uk-modal-close" type="button" data-target=".purple-modal">Cancel</button>
        </div>
    </div>
</div>