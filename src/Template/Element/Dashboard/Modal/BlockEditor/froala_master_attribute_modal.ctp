<div id="modal-element-properties" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
    	<div class="uk-modal-header">
            <h3 class="uk-modal-title">Element Properties</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <label>ID</label>
                <input type="text" name="element-id" class="form-control" placeholder="ID" data-parsley-type="alphanumeric" autofocus>
            </div>
            <div class="form-group">
                <label>Class (No space, press tab after typing)</label>
                <input type="text" name="element-class" class="form-control" placeholder="Class">
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
        	<button id="button-element-properties" type="button" class="btn btn-gradient-primary" data-purple-target="">Save</button>
        	<button id="button-close-modal" class="btn btn-outline-primary uk-margin-left uk-modal-close" type="button" data-target=".purple-modal">Cancel</button>
        </div>
    </div>
</div>