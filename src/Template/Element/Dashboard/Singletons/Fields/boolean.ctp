<?php
    $defaultOptions = [
        'class' => 'js-switch',
        'value' => '1',
        'data-parsley-errors-container' => '#error-' . $uid
        // 'required' => $required == "1" ? true : false,
    ];

    $default  = collection($defaultOptions);
    $newArray = $default->append($options);
    $filterArray = array_filter($newArray->toArray(), function($var) {
        return ($var !== NULL && $var !== FALSE && $var !== "");
    });

    if (isset($value)) {
        $filterArray['checked'] = $value == '1' ? true : false;
    }

    if ($info != '') {
        $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
    }
?>
<ul class="uk-list uk-list-divider uk-margin-remove">
    <li class="uk-padding uk-margin-remove-top">
        <?php
            echo $this->Form->label($uid . '.value', $label, ['escape' => false]);
        ?>
        <div class="uk-inline uk-align-right" style="margin-bottom: 0">
            <?php
                echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
                echo $this->Form->checkbox($uid . '.value', $filterArray);
            ?>
        </div>
    </li>
</ul>
<div id="error-<?= $uid ?>" class="uk-padding-small"></div>

<?= $this->Html->script('/master-assets/plugins/switchery/switchery.min.js'); ?>
<script type="text/javascript">
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function(html) {
        var switchery = new Switchery(html, { color: '#9a55ff', jackColor: '#ffffff', size: 'small' });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#data-<?= $uid ?>').find('.card-body').addClass('uk-padding-remove');
    })
</script>