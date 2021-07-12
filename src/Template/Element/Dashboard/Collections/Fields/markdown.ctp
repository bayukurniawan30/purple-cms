<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'class' => 'form-control mdeditor-' . $uid,
            'required' => $required == "1" ? true : false
        ];

        $default  = collection($defaultOptions);
        $newArray = $default->append($options)->toArray();

        if (isset($value)) {
            $newArray['value'] = $value;
        }

        if ($info != '') {
            $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
        }
        echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
        echo $this->Form->label($uid . '.value', $label, ['escape' => false]);
        echo $this->Form->textarea($uid . '.value', $newArray);
    ?>
</div>

<script>
    var token                = $('#csrf-ajax-token').val(),
        froalaImageUploadUrl = $("#froala-image-upload-url").val();

    var md = new MdEditor('.mdeditor-<?= $uid ?>', {
        preview: true,
        uploader: froalaImageUploadUrl,
        headers: {
            'X-CSRF-Token': token
        },
        images: [<?php if ($mediasForMd->count() > 0): foreach ($mediasForMd as $mediaMd): ?>
            {id: '<?= $mediaMd->name ?>', alt: '<?= $mediaMd->name ?>', url: '<?= $this->cell('Medias::mediaPath', [$mediaMd->name, 'image', 'original']) ?>'},
            <?php endforeach; endif; ?>
        ],
        ctrls: false,
    });
</script>