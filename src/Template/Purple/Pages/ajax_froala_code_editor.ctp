<?php
    $result = json_decode($json, true); 
?>
<ul class="uk-margin-remove-bottom" uk-tab>
    <li class="uk-active"><a href="#">HTML</a></li>
    <li class=""><a href="#">CSS</a></li>
</ul>
<input id="form-hidden-mk" type="hidden" name="meta_keywords" value="">
<input id="form-hidden-md" type="hidden" name="meta_description" value="">
<input id="form-hidden-tl" type="hidden" name="title" value="">
<ul class="uk-switcher">
    <li><textarea id="fdb-code-editor-ace" class="form-control uk-height-large" rows="4" style="width: 100%" name="content"><?= h($result['html']) ?></textarea></li>
    <li><textarea id="fdb-css-editor-ace" class="form-control uk-height-large" rows="4" style="width: 100%" name="css-content"><?= h($result['css']) ?></textarea></li>
</ul>

<script type="text/javascript">
    $(document).ready(function() {
        $('#fdb-code-editor-ace').ace({ theme: 'chrome', lang: 'html' })
        $('#fdb-css-editor-ace').ace({ theme: 'chrome', lang: 'css' })
        
        var meta_key  = $("#form-input-metakeywords").val(),
            meta_desc = $("#form-input-metadescription").val(),
            title     = $("#form-input-title").val();
        
        $('#form-hidden-mk').val(meta_key);
        $('#form-hidden-md').val(meta_desc);
        $('#form-hidden-tl').val(title);
    })
</script>