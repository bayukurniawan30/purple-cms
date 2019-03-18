<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Under Construction</title>
        <?= $this->Html->css('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') ?>
        <!-- plugins:css -->
        <?= $this->Html->css('/master-assets/plugins/iconfonts/mdi/css/materialdesignicons.min.css') ?>
        <?= $this->Html->css('/master-assets/css/vendor.bundle.base.css') ?>
        <!-- endinject -->
        <!-- plugin css for this page -->
        <?= $this->Html->css('/master-assets/plugins/parsley/src/parsley.css') ?>
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <?= $this->Html->css('/master-assets/css/style.css') ?>
        <?= $this->Html->css('/master-assets/plugins/uikit/css/uikit.css') ?>
        <!-- endinject -->
        <?= $this->Html->script('/master-assets/js/vendor.bundle.base.js'); ?>
        <style type="text/css">
            #modal-setup-purple p {
                font-size: 1.1rem;
            }
            .auth .brand-logo img {
              width: 100%;
            }
            .auth-form-light {
                margin-top: 25px;
            }
            .setup-loader {
                color: #ffffff;
            }
            .setup-loader p {
                font-size: 1rem;
            }
            .setup-information {
                position: relative;
            }
            .setup-information .uk-overlay {
                width: 100%;
            }
            .under-construction-title {
                font-weight: 700!important;
            }
            .stay-in-touch {
                font-size: 1rem;
            }
            .uk-iconnav > li {
                margin-right: 12px;
            }
            .uk-iconnav > li > a {
                font-size: 1rem;
            }
        </style>
        <script type="text/javascript">
            var cakeDebug    = "<?= $cakeDebug ?>",
                formSecurity = "<?= $formSecurity ?>";
            window.cakeDebug    = cakeDebug;
            window.formSecurity = formSecurity;
        </script>
    </head>

    <body>
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper full-page-wrapper">
                <div class="content-wrapper d-flex align-items-center auth bg-primary text-center">
                    <div class="uk-overlay uk-position-center setup-loader">
                        <p>Loading...</p><br>
                        <div uk-spinner="ratio: 2"></div>
                    </div>
                </div>
              <!-- content-wrapper ends -->
            </div>
            <!-- page-body-wrapper ends -->
        </div>
        <!-- container-scroller -->

        <div id="modal-setup-purple" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog">
                <div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
                    <div class="bg-primary uk-background-contain" style="background-image: url('<?php if ($settingBgComingSoon->value == '') echo $this->request->getAttribute("webroot") . 'master-assets/img/signin-background.svg'; else echo $this->request->getAttribute("webroot") . 'uploads/images/original/'. $settingBgComingSoon->value ?>');" uk-height-viewport></div>
                    <div class="uk-padding-large">
                        <!-- <div class="brand-logo">
                            <?= $this->Html->image('/master-assets/img/logo.svg', ['alt' => 'Setup Page', 'data-id' => 'login-cover-image', 'width' => '150']) ?>
                            <p>&nbsp;</p>
                        </div> -->
                        <h1 class="under-construction-title">UNDER<br>CONSTRUCTION</h1>
                        <p>Our website is currently undergoing scheduled maintenance. <br>We Should be back shortly. Thank you for your patience.</p>

                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- plugins:js -->
        <?= $this->Html->script('/master-assets/plugins/parsley/dist/parsley.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/moment/moment.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/moment-timezone/moment-timezone-with-data.js'); ?>
        <!-- UI Kit -->
        <?= $this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>
        <!-- End UI Kit -->
        <!-- endinject -->
        <!-- inject:js -->
        <?= $this->Html->script('/master-assets/js/off-canvas.js'); ?>
        <?= $this->Html->script('/master-assets/js/misc.js'); ?>
        <!-- endinject -->
        <!-- Purple -->
        <?= $this->Html->script('/master-assets/js/ajax-front-end.js'); ?>
        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    UIkit.modal('#modal-setup-purple').show();
                    $(".setup-loader").hide();
                }, 2000);
            })
        </script>
    </body>
</html>