<div class="form-group">
    <?php
        $defaultOptions = [
            'class' => 'form-control',
            'data-parsley-type' => 'url',
            'required' => $required == "1" ? true : false
        ];

        $default  = collection($defaultOptions);
        $newArray = $default->append($options);
        $filterArray = array_filter($newArray->toArray(), function($var) {
            return ($var !== NULL && $var !== FALSE && $var !== "");
        });

        if (isset($value)) {
            $filterArray['value'] = $value['url'];
        }

        if ($info != '') {
            $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
        }
        echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
        echo $this->Form->label($uid . '.value.url', $label, ['escape' => false]);
        echo $this->Form->text($uid . '.value.url', $filterArray);
    ?>
</div>

<div class="form-group uk-margin-remove-bottom">
    <?php 
        if (strpos($options['open'], ',') !== false) {
            $getOpenArray = explode(',', $options['open']);
        }
        else {
            $getOpenArray = [$options['open']];
        }
        $openArray = array_combine($getOpenArray, $getOpenArray);

        echo $this->Form->label($uid . '.value.target', 'Target', ['escape' => false]);
        echo $this->Form->select($uid . '.value.target', $openArray,
            [
                'class'    => 'form-control',
                'required' => true,
                'value'    => isset($value) ? $value['target'] : ''
            ])
    ?>
</div>
