<?php
    if ($topPosts != false):
?>
<div class="uk-overflow-auto">
    <table class="uk-table uk-table-divider uk-table-justify">
        <thead>
            <tr>
                <th class="" width="20">No</th>
                <th class="">Post Title</th>
                <th class="uk-table-shrink">Category</th>
                <th class="uk-table-shrink uk-text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $i = 1;
                foreach ($topPosts as $post):
                    $postUrl = $this->Url->build([
                        '_name' => 'specificPost',
                        'year'  => date('Y', strtotime($post['created'])),
                        'month' => date('m', strtotime($post['created'])),
                        'date'  => date('d', strtotime($post['created'])),
                        'post'  => $post['slug'],
                    ], true);
            ?>
            <tr>
                <td><?= $i ?>.</td>
                <td class="uk-text-truncate"><a href="<?= $postUrl ?>" target="_blank" title="<?= $post['title'] ?>"><?= $post['title'] ?></a></td>
                <td class="uk-table-shrink"><?= $post['category'] ?></td>
                <td class="uk-table-shrink uk-text-right"><?= $this->Purple->shortenNumber($post['total']) ?></td>
            </tr>
            <?php
                    $i++;
                endforeach;
            ?>
        </tbody>
    </table>
</div>
<?php
    else:
?>
<div class="uk-alert-primary uk-margin-remove-bottom" uk-alert>
    <p>
        <strong>Empty reader</strong>. Share your post to your friends and social media to increase readers.
    </p>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#bind-top-posts').addClass('uk-padding-remove');
        $('#bind-top-posts').css('min-height', '0');
    })
</script>
<?php
    endif;
?>