<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'class'                         => 'form-control',
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
        echo '<div class="input-group date" data-provide="datepicker" data-date-format="' . $options['data-date-format'] . '">';
        echo $this->Form->text($uid . '.value', $filterArray);
        echo '<div class="input-group-append">';
        echo '<span class="input-group-text"><i class="fa fa-calendar-o"></i></span>';
        echo '</div></div>';
    ?>
    <div id="error-<?= $uid ?>"></div>
</div>