<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="row">
    <div class="col-md-12 uk-margin-small-bottom">
        <div class="uk-alert-success" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p><?= $message ?></p>
        </div>
    </div>
</div>