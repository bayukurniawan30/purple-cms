<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'type' => 'number',
            'class' => 'form-control',
            'required' => $required == "1" ? true : false
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
        echo $this->Form->text($uid . '.value', $filterArray);
    ?>
</div>