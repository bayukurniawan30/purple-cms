<?php
    $result = json_decode($json, true); 
?>
<input id="form-hidden-mk" type="hidden" name="meta_keywords" value="">
<input id="form-hidden-md" type="hidden" name="meta_description" value="">
<input id="form-hidden-tl" type="hidden" name="title" value="">
<textarea id="fdb-code-editor-ace" class="form-control uk-height-large" rows="4" style="width: 100%" name="content"><?= h($result['html']) ?></textarea>

<script type="text/javascript">
    $(document).ready(function() {
        $('#fdb-code-editor-ace').ace({ theme: 'chrome', lang: 'html' })
        
        var meta_key  = $("#form-input-metakeywords").val(),
            meta_desc = $("#form-input-metadescription").val(),
            title     = $("#form-input-title").val();
        
        $('#form-hidden-mk').val(meta_key);
        $('#form-hidden-md').val(meta_desc);
        $('#form-hidden-tl').val(title);
    })
</script>