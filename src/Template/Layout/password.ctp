<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Purple CMS | Reset Password</title>
        <?= $this->Html->css('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') ?>
        <!-- plugins:css -->
        <?= $this->Html->css('/master-assets/plugins/iconfonts/mdi/css/materialdesignicons.min.css') ?>
        <?= $this->Html->css('/master-assets/css/vendor.bundle.base.css') ?>
        <?= $this->Html->css('/master-assets/plugins/password/password.min.css') ?>
        <!-- endinject -->
        <!-- plugin css for this page -->
        <?= $this->Html->css('/master-assets/plugins/parsley/src/parsley.css') ?>
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <?= $this->Html->css('/master-assets/css/style.css') ?>
        <?= $this->Html->css('/master-assets/plugins/uikit/css/uikit.css') ?>
        <!-- endinject -->
        <?= $this->Html->script('/master-assets/js/vendor.bundle.base.js'); ?>
        <!-- Favicon -->
        <link rel="icon" href="<?= $this->request->getAttribute("webroot").'master-assets/img/favicon.png' ?>">
        <style type="text/css">
            .auth .brand-logo img {
              width: 100%;
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
        </style>
    </head>

    <body>
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper full-page-wrapper">
                <div class="content-wrapper d-flex align-items-center auth bg-primary text-center">
                    <div class="uk-overlay uk-position-center setup-loader">
                        <p>Loading form...</p><br>
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
                <div class="uk-grid-collapse uk-flex-middle" uk-grid>
                    <div class="uk-width-2-3@m uk-hidden@xl uk-background-contain setup-information" style="background-image: linear-gradient(120deg, #63cfb3 0%, #9f5eff 100%)" uk-height-viewport>
                        <div class="uk-overlay uk-overlay-default uk-position-bottom">
                            <h3 class="text-primary"><strong>Reset Your Password</strong></h3>
                            <p>
                                Please keep your password save and do not use old password.
                            </p>
                        </div>
                    </div>
                    <div class="uk-width-1-3@m uk-width-1-1@s uk-padding">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="auth-form-light text-left">
                                        <div class="brand-logo">
                                            <?= $this->Html->image('/master-assets/img/logo.svg', ['alt' => 'Setup Page', 'data-id' => 'login-cover-image', 'width' => '300']) ?>
                                        </div>
                                       <!-- <div class="uk-margin-small">&nbsp;</div> -->
                                        <?= $this->fetch('content') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- plugins:js -->
        <?= $this->Html->script('/master-assets/plugins/parsley/dist/parsley.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/moment/moment.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/moment-timezone/moment-timezone-with-data.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/password/password.min.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/password/password-generator.js'); ?>

        <!-- UI Kit -->
        <?= $this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>
        <!-- End UI Kit -->
        <!-- endinject -->
        <!-- inject:js -->
        <?= $this->Html->script('/master-assets/js/off-canvas.js'); ?>
        <?= $this->Html->script('/master-assets/js/misc.js'); ?>
        <!-- endinject -->
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