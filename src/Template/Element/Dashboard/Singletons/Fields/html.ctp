<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'class' => 'form-control froala-' . $uid,
            'required' => $required == "1" ? true : false
        ];

        $default  = collection($defaultOptions);
        $newArray = $default->append($options)->toArray();

        if (isset($value)) {
            $newArray['value'] = html_entity_decode($value);
        }

        if ($info != '') {
            $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
        }
        echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
        echo $this->Form->label($uid . '.value', $label, ['escape' => false]);
        echo $this->Form->textarea($uid . '.value', $newArray);
    ?>
</div>

<!-- Include PDF export JS lib. -->
<?= $this->Html->script('/master-assets/plugins/html2pdf/html2pdf.bundle.js'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        var token                  = $("input[name=_csrfToken]").val(),
            targetName             = '<?= '.froala-' . $uid ?>',
            froalaManagerLoadUrl   = $("#froala-load-url").val(),
            froalaImageUploadUrl   = $("#froala-image-upload-url").val(),
            froalaFileUploadUrl    = $("#froala-file-upload-url").val(),
            froalaVideoUploadUrl   = $("#froala-video-upload-url").val();

        $(targetName).froalaEditor({
            theme: 'royal',
            height: 400,
            toolbarStickyOffset: 70,
            charCounterCount: false,
            placeholderText: 'Post content here...',
            enter: $.FroalaEditor.ENTER_DIV,
            imageManagerLoadURL: froalaManagerLoadUrl,
            imageUploadURL: froalaImageUploadUrl,
            fileUploadURL: froalaFileUploadUrl,
            videoUploadURL: froalaVideoUploadUrl,
            imageMaxSize: 3 * 1024 * 1024,
            imageAllowedTypes: ['jpeg', 'jpg', 'png'],
            fileMaxSize: 5 * 1024 * 1024,
            fileAllowedTypes: ['*'],
            videoMaxSize: 20 * 1024 * 1024,
            videoAllowedTypes: ['mp4', 'm4v', 'ogg', 'webm'],
            requestHeaders: {
                'X-CSRF-Token': token
            }
        })
    })
</script>