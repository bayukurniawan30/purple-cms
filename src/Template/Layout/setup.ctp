<?php
    $checkStep = $this->request->getParam('action');
    if ($checkStep == 'index'):
        $currentStep = 'Database Information';
        $currentLoad = 'Preparing setup for the first time...';
        $currentDesc = "Provide your database information that you created earlier on your server. If you don't have permission to do that, please contact your hosting provider.";
    elseif ($checkStep == 'administrative'):
        $currentStep = 'Administrative Setup';
        $currentLoad = 'Saving database and preparing administrative setup...';
        $currentDesc = 'Create your account and site name for your website.';
    elseif ($checkStep == 'finish'):
        $currentStep = 'Finishing Setup';
        $currentDesc = 'You are ready to go.';
    endif;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Purple CMS | Setup Page</title>
        <?= $this->Html->css('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') ?>
        <!-- plugins:css -->
        <?= $this->Html->css('/master-assets/plugins/iconfonts/mdi/css/materialdesignicons.min.css') ?>
        <?= $this->Html->css('/master-assets/css/vendor.bundle.base.css') ?>
        <?php if ($checkStep == 'administrative'): ?>
        <?= $this->Html->css('/master-assets/plugins/password/password.min.css') ?>
        <?php endif; ?>
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
            select.form-control {
                color: #495057!important;
            }
        </style>
    </head>

    <body>
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper full-page-wrapper">
                <div class="content-wrapper d-flex align-items-center auth bg-primary text-center">
                    <?php
                        if ($checkStep == 'index' || $checkStep == 'administrative'):
                    ?>
                    <div class="uk-overlay uk-position-center setup-loader">
                        <p><?= $currentLoad ?></p><br>
                        <div uk-spinner="ratio: 2"></div>
                    </div>
                <?php endif; ?>
                </div>
              <!-- content-wrapper ends -->
            </div>
            <!-- page-body-wrapper ends -->
        </div>
        <!-- container-scroller -->

        <div id="modal-setup-purple" class="uk-modal-full" uk-modal>
            <div class="uk-modal-dialog">
                <div class="uk-grid-collapse uk-flex-middle" uk-grid>
                    <div class="uk-width-2-3@m uk-hidden@xl  bg-primary uk-background-contain setup-information" style="background-image: url('<?= $this->request->getAttribute("webroot") . 'master-assets/img/signin-background.svg' ?>');" uk-height-viewport>
                        <div class="uk-overlay uk-overlay-default uk-position-bottom">
                            <h3 class="text-primary"><strong><?= $currentStep ?></strong></h3>
                            <p>
                                <?= $currentDesc ?>
                                <br><a href="http://doc.purple-cms.com/" target="_blank"><span class="mdi mdi-open-in-new"></span> Read full documentation</a>
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
                                       <!-- <h4 class="uk-margin-small">Get Started with Purple CMS</h4> -->
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
        <?php if ($checkStep == 'administrative'): ?>
        <?= $this->Html->script('/master-assets/plugins/password/password.min.js'); ?>
        <?php endif; ?>
        <!-- UI Kit -->
        <?= $this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>
        <?= $this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>
        <!-- End UI Kit -->
        <!-- endinject -->
        <!-- inject:js -->
        <?= $this->Html->script('/master-assets/js/off-canvas.js'); ?>
        <?= $this->Html->script('/master-assets/js/misc.js'); ?>
        <!-- endinject -->
        <?php
            if ($checkStep == 'index' || $checkStep == 'administrative'):
        ?>
        <script>
            $(document).ready(function() {
                <?php if ($checkStep == 'administrative'): ?>
                var userTimezone = moment.tz.guess();
                $('select[name=timezone] option:contains('+userTimezone+')').attr('selected', 'selected');
                <?php endif; ?>

                setTimeout(function() {
                    UIkit.modal('#modal-setup-purple').show();
                    $(".setup-loader").hide();
                }, 2000);
            })
        </script>
        <?php
            else:
        ?>
        <script>
            $(document).ready(function() {
                UIkit.modal('#modal-setup-purple').show();
            })
        </script>
        <?php
            endif;
        ?>
    </body>
</html>
