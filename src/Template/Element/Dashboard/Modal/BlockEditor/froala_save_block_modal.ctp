<div id="modal-save-block" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
    	<div class="uk-modal-header">
            <h3 class="uk-modal-title">Save Block</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <label>Block Name</label>
                <input type="hidden" name="html">
                <input type="text" name="name" class="form-control" placeholder="Block Name" data-parsley-maxlength="50" autofocus required>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        	<button id="button-save-block" type="button" class="btn btn-gradient-primary">Save</button>
        	<button id="button-close-modal" class="btn btn-outline-primary uk-margin-left uk-modal-close" type="button" data-target=".purple-modal">Cancel</button>
        </div>
    </div>
</div>