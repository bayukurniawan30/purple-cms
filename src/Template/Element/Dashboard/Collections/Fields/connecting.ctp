<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'empty'    => $label,
            'class'    => 'form-control',
            'required' => $required == "1" ? true: false,
            'value'    => isset($value) ? $value : '' 
        ];

        $connectingCollectionDatas = $this->cell('Collections::getConnectingCollectionDatasForSelectbox', [$id])->render();
        $decodeConnectingCollectionDatas = json_decode($connectingCollectionDatas, true);

        $newOptions = [];
        if (count($decodeConnectingCollectionDatas) > 0) {
            foreach ($decodeConnectingCollectionDatas as $key => $value) {
                $decodeContent = json_decode($value, true);
                $newOptions[$key] = $decodeContent[$options['showFieldUid']]['value'];
            }
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