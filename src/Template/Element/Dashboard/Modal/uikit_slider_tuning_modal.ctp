<div id="modal-uikit-slider-tuning" class="uk-flex-top purple-modal" uk-modal style="z-index: 99999999">
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Change Slider Options</h3>
        </div>
        <div class=" uk-modal-body">
            <div class="form-group">
                <label>Autoplay</label>
                <select class="form-control" name="autoplay">
                    <option value="true">On</option>
                    <option value="false">Off</option>
                </select>
            </div>
            <div class="form-group">
                <label>Autoplay Interval</label>
                <select class="form-control" name="autoplay-interval">
                    <option value="3000">3 seconds</option>
                    <option value="5000">5 seconds</option>
                    <option value="7000">7 seconds</option>
                    <option value="10000">10 seconds</option>
                </select>
            </div>
            <div class="form-group">
                <label>Infinite Scrolling</label>
                <select class="form-control" name="finite">
                    <option value="false">On</option>
                    <option value="true">Off</option>
                </select>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button id="button-change-slidertuning" class="btn btn-gradient-primary" type="button">Update Block</button>
            <button class="btn btn-outline-primary uk-margin-left uk-modal-close" type="button" data-target=".purple-modal">Cancel</button>
        </div>
    </div>
</div>