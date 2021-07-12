<div class="form-group uk-margin-remove-bottom">
    <?php
        $defaultOptions = [
            'class' => 'form-control tags-' . $uid,
            'required' => $required == "1" ? true : false
        ];

        $default  = collection($defaultOptions);
        $newArray = $default->append($options);
        $filterArray = array_filter($newArray->toArray(), function($var) {
            return ($var !== NULL && $var !== FALSE && $var !== "");
        });
        unset($filterArray['max']);

        if ($info != '') {
            $label .= ' <span class="uk-text-small uk-text-italic uk-text-muted">' . $info . '</span>';
        }
        echo $this->Form->hidden($uid . '.field_type', ['value' => $field_type]);
        echo $this->Form->label($uid . '.value', $label, ['escape' => false]);
        echo $this->Form->text($uid . '.value', $filterArray);
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var targetName = '.tags-<?= $uid ?>';
        var maxTags    = <?= array_key_exists('max', $options) ? $options['max'] : 10 ?>;

        $(targetName).tagEditor({ 
            <?php
                if (isset($value)):
                    if (strpos($value, ',') !== false) {
                        $explodeTags = explode(',', $value);
                    }
                    else {
                        $explodeTags = [$value];
                    }
            ?>
                initialTags: [<?php
                    $i = 1;
                    foreach ($explodeTags as $tag):
                        if ($i < count($explodeTags)) {
                            echo '"' . $tag . '",';
                        }
                        else {
                            echo '"' . $tag . '"';
                        }

                        $i++;
                    endforeach;
                ?>],
            <?php
                endif;
            ?>
            autocomplete: {
                delay: 0, 
                position: { collision: 'flip' },
            },
            maxTags: maxTags,
            placeholder: "Tags (Max 5 tags)",
            
        });
    })
</script>