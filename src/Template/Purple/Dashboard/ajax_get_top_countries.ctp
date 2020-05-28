<?php
    if ($topCountries != false):
?>
<div class="uk-overflow-auto">
    <table class="uk-table uk-table-divider uk-table-justify">
        <thead>
            <tr>
                <th class="" width="20">No</th>
                <th class="">Country</th>
                <th class="uk-text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $i = 1;
                foreach ($topCountries as $key => $value):
                    $explodeKey = explode('::', $key);
                    $country    = $explodeKey[0];
                    $flag       = $explodeKey[1];
            ?>
            <tr>
                <td><?= $i ?>.</td>
                <td><img src="<?= $flag ?>" width="15"> <?= $country ?></td>
                <td class="uk-text-right"><?= $this->Purple->shortenNumber($value) ?></td>
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
        <strong>Empty visitor</strong>. Share your website to your friends and social media to increase visitors.
    </p>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#bind-top-countries').addClass('uk-padding-remove');
        $('#bind-top-countries').css('min-height', '0');
    })
</script>
<?php
    endif;
?>