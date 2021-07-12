<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'empty'    => $label,
            'class'    => 'form-control',
            'required' => $required == "1" ? true: false
        ];

        $newOptions = [];
        foreach ($options['fields'] as $field) {
            array_push($newOptions, ['value' => $field['value'], 'text' => ' ' . $field['text'], 'class' => 'uk-radio']);
        }

        if ($info != '') {
            $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
        }
        echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
        echo $this->Form->label($uid . '.value', $label, ['escape' => false]);
        echo '<div class="uk-child-width-auto uk-margin-small-top" uk-grid>';
        echo $this->Form->radio($uid . '.value',
            $newOptions,
            [
                'value' => isset($value) ? $value : ''
            ]
        );
        echo '</div>';
    ?>
</div>