<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'empty'    => $label,
            'class'    => 'form-control',
            'required' => $required == "1" ? true: false,
            'value'    => isset($value) ? $value : '' 
        ];

        $newOptions = [];
        foreach ($options['fields'] as $field) {
            $newOptions[$field['value']] = $field['label'];
        }

        if ($info != '') {
            $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
        }
        echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
        echo $this->Form->label($uid . '.value', $label, ['escape' => false]);
        echo $this->Form->select($uid . '.value',
            $newOptions,
            $defaultOptions
        );
    ?>
</div>