<?= $this->Html->css('/master-assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css') ?>
<?= $this->Html->script('/master-assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js') ?>

<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'class'                         => 'form-control timepicker-' . $uid,
            'data-provide'                  => 'timepicker',
            'data-parsley-errors-container' => '#error-' . $uid,
            'required'                      => $required == "1" ? true: false
        ];

        $default  = collection($defaultOptions);
        $newArray = $default->append($options);
        $filterArray = array_filter($newArray->toArray(), function($var) {
            return ($var !== NULL && $var !== FALSE && $var !== "");
        });

        if (isset($value)) {
            $filterArray['value'] = $value;
        }

        if ($info != '') {
            $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
        }
        
        echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
        echo $this->Form->label($uid . '.value', $label, ['escape' => false]);
        echo '<div class="input-group bootstrap-timepicker timepicker">';
        echo $this->Form->text($uid . '.value', $filterArray);
        echo '<div class="input-group-append">';
        echo '<span class="input-group-text"><i class="fa fa-clock-o"></i></span>';
        echo '</div></div>';
    ?>
    <div id="error-<?= $uid ?>"></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.timepicker-<?= $uid ?>').timepicker();
    })
</script>