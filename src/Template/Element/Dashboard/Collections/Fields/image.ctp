<?php
    if (isset($value)) {
        $explodeFullPath = explode('/', $value['full_path']);
        $image = end($explodeFullPath);
    }
    else {
        $image = NULL;
    }

    $defaultOptions = [
        'selected'      => $image,
        'widgetTitle'   => $label,
        'inputTarget'   => $uid . '\\\[value\\\]',
        'browseMedias'  => $browseMedias,
        'multiple'      => false,
        'modalParams'   => [
            'browseContent' => 'collections::0',
            'browseAction'  => 'send-to-input',
            'browseTarget'  => $uid . '\\[value\\]'
        ]
    ];

    $default  = collection($defaultOptions);
    $newArray = $default->append($options);
?>
<div class="form-group">
    <?= $this->Form->hidden($uid . '.field_type', ['value' => $field_type]) ?>
    <?= $this->Form->hidden($uid . '.value', ['value' => isset($value) ? $image : '' ]) ?>
    <?= $this->element('Dashboard/upload_or_browse_image', $newArray->toArray()) ?>
</div>