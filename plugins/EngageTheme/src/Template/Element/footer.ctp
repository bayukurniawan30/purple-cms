<footer class="fdb-block footer-small">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-12 col-lg-6 text-lg-left">
                <?php if ($leftFooter == 'NULL') echo ''; else echo $leftFooter ?>
            </div>

            <div class="col-12 col-lg-6 text-lg-right mt-4 mt-lg-0">
                <?php if ($rightFooter == 'NULL') echo ''; else echo $rightFooter ?>
            </div>
        </div>
    </div>
</footer>