<?php
    $checkStep = $this->request->getParam('action');

    if ($this->request->getParam('controller') == 'Setup'):
        $purpleTag = 'Get Started with Purple CMS';

        if ($checkStep == 'index'):
            $currentStep = 'Database Information';
            $currentLoad = 'Preparing setup for the first time...';
            $currentDesc = "Provide your database information that you created earlier on your server. If you don't have permission to do that, please contact your hosting provider.";
            $vectorImg   = 'setup-database.svg';
        elseif ($checkStep == 'administrative'):
            $currentStep = 'Administrative Setup';
            $currentLoad = 'Saving database and preparing administrative setup...';
            $currentDesc = 'Create your account and site name for your website.';
            $vectorImg   = 'setup-administrative.svg';
        elseif ($checkStep == 'finish'):
            $currentStep = 'Finishing Setup';
            $currentDesc = 'You are ready to go.';
            $vectorImg   = 'setup-finish.svg';
        elseif ($checkStep == 'databaseMigration'):
            $currentStep = 'Database Migration';
            $currentLoad = 'Preparing database form...';
            $currentDesc = "Provide your production database information that you created earlier on your server. If you don't have permission to do that, please contact your hosting provider.";
        endif;
    elseif ($this->request->getParam('controller') == 'Production'):
        $purpleTag = 'Get Ready to Live Your Website';

        if ($checkStep == 'userVerification'):
            $currentStep = 'User Verification';
            $currentLoad = 'Preparing User Verification...';
            $currentDesc = "Provide your email and Purple CMS will sent a verification code to you.";
            $vectorImg   = 'user-verification.svg';
        elseif ($checkStep == 'codeVerification'):
            $currentStep = 'Verification Code';
            $currentLoad = 'Preparing Code Verification...';
            $currentDesc = "Insert the verification code. Please check your inbox or spam folder.";
            $vectorImg   = 'code-verification.svg';
        elseif ($checkStep == 'databaseMigration'):
            $currentStep = 'Database Migration';
            $currentLoad = 'Preparing Migration Form...';
            $currentDesc = "Provide your production database information that you created earlier on your server. If you don't have permission to do that, please contact your hosting provider.";
            $vectorImg   = 'setup-database.svg';
        endif;
    endif;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Purple CMS | <?php if ($this->request->getParam('controller') == 'Production' && ($checkStep == 'userVerification' || $checkStep == 'codeVerification' || $checkStep == 'databaseMigration')) echo 'Database Migration'; else echo 'Setup Page' ?></title>
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
            .purple-check-req-step, .purple-check-mbstring, .purple-check-intl {
                display: none;
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
                <div class="content-wrapper d-flex align-items-center auth text-center" style="background-image: linear-gradient(120deg, #63cfb3 0%, #9f5eff 100%)">
                    <?php
                        if ($checkStep == 'index' || $checkStep == 'administrative' || ($this->request->getParam('controller') == 'Production' && ($checkStep == 'userVerification' || $checkStep == 'codeVerification' || $checkStep == 'databaseMigration'))):
                    ?>
                    <div class="uk-overlay uk-position-center setup-loader">
                        <p><?= $currentLoad ?></p><br>
                        
                        <div class="setup-loader-spinner" uk-spinner="ratio: 2"></div>
                        
                        <ul class="uk-iconnav uk-iconnav-vertical purple-check-req-step">
                            <li class="uk-animation-slide-bottom-medium purple-check-php-version"><span class="purple-check-php-version-icon" uk-spinner></span> <span class="purple-check-php-version-text">Checking PHP version...</span></li>

                            <li class="uk-animation-slide-bottom-medium purple-check-mbstring"><span class="purple-check-mbstring-icon" uk-spinner></span> <span class="purple-check-mbstring-text">Checking mbstring PHP extension...</span></li>

                            <li class="uk-animation-slide-bottom-medium purple-check-intl"><span class="purple-check-intl-icon" uk-spinner></span> <span class="purple-check-intl-text">Checking intl PHP extension...</span></li>
                        </ul>
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
                    <div class="uk-width-1-2@m uk-hidden@xl uk-background-contain setup-information" style="background-image: linear-gradient(120deg, #63cfb3 0%, #9f5eff 100%)" uk-height-viewport>
                        <div class="uk-overlay uk-overlay-default" uk-height-viewport>
                            <?= $this->Html->image('/master-assets/img/'.$vectorImg, ['alt' => $currentStep]) ?>
                            <div class="uk-position-bottom uk-padding">
                                <h3 class="text-primary"><strong><?= $currentStep ?></strong></h3>
                                <p class="uk-margin-remove-bottom">
                                    <?= $currentDesc ?>
                                    <br><a href="https://bayukurniawan30.github.io/purple-cms/#/" target="_blank"><span class="mdi mdi-open-in-new"></span> Read full documentation</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-2@m uk-width-1-1@s uk-padding">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="auth-form-light text-left">
                                        <div class="brand-logo">
                                            <?= $this->Html->image('/master-assets/img/logo.svg', ['alt' => 'Setup Page', 'data-id' => 'login-cover-image', 'width' => '250']) ?>
                                        </div>
                                        <h4 class="uk-margin-small"><?= $purpleTag ?></h4>
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
        <?= $this->Html->script('/master-assets/plugins/password/password-generator.js'); ?>
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
            if ($checkStep == 'index'):
        ?>
        <script>
            $(document).ready(function() {
                $('.purple-check-req-step').hide();
                setTimeout(function() {
                    $('.setup-loader-spinner').hide();
                }, 2000);

                setTimeout(function() {
                    $('.purple-check-req-step').show();
                }, 2000);

                setTimeout(function() {
                    // Icon
                    $('.purple-check-php-version').find('.purple-check-php-version-icon').removeClass('uk-spinner');
                    $('.purple-check-php-version').find('.purple-check-php-version-icon').removeAttr('uk-spinner');
                <?php
                    if (version_compare(PHP_VERSION, '7.1.3') >= 0):
                ?>  
                    // Icon
                    $('.purple-check-php-version').find('.purple-check-php-version-icon').attr('uk-icon', 'icon: check');
                    $('.purple-check-php-version').find('.purple-check-php-version-icon').attr('uk-icon', 'icon: check');

                    // Text
                    $('.purple-check-php-version').find('.purple-check-php-version-text').text('PHP 7.1.3 or greater');
                <?php
                    else:
                ?>
                    // List
                    $('.purple-check-php-version').addClass('uk-text-danger');
                    
                    // Icon
                    $('.purple-check-php-version').find('.purple-check-php-version-icon').attr('uk-icon', 'icon: warning');

                    // Text
                    $('.purple-check-php-version').find('.purple-check-php-version-text').text('You need PHP 7.1.3 or greater');
                <?php
                    endif;
                ?>
                }, 3000);

                setTimeout(function() {
                    $('.purple-check-mbstring').show();
                }, 3500);

                setTimeout(function() {
                    // Icon
                    $('.purple-check-mbstring').find('.purple-check-mbstring-icon').removeClass('uk-spinner');
                    $('.purple-check-mbstring').find('.purple-check-mbstring-icon').removeAttr('uk-spinner');
                <?php
                    if (extension_loaded('intl')):
                ?>  
                    // Icon
                    $('.purple-check-mbstring').find('.purple-check-mbstring-icon').attr('uk-icon', 'icon: check');
                    $('.purple-check-mbstring').find('.purple-check-mbstring-icon').attr('uk-icon', 'icon: check');

                    // Text
                    $('.purple-check-mbstring').find('.purple-check-mbstring-text').text('mbstring PHP extension enabled');
                <?php
                    else:
                ?>
                    // List
                    $('.purple-check-mbstring').addClass('uk-text-danger');
                    
                    // Icon
                    $('.purple-check-mbstring').find('.purple-check-mbstring-icon').attr('uk-icon', 'icon: warning');

                    // Text
                    $('.purple-check-mbstring').find('.purple-check-mbstring-text').text('Please enable mbstring PHP extension');
                <?php
                    endif;
                ?>
                }, 4500);

                setTimeout(function() {
                    $('.purple-check-intl').show();
                }, 5000);

                setTimeout(function() {
                    // Icon
                    $('.purple-check-intl').find('.purple-check-intl-icon').removeClass('uk-spinner');
                    $('.purple-check-intl').find('.purple-check-intl-icon').removeAttr('uk-spinner');
                <?php
                    if (extension_loaded('intl')):
                ?>  
                    // Icon
                    $('.purple-check-intl').find('.purple-check-intl-icon').attr('uk-icon', 'icon: check');
                    $('.purple-check-intl').find('.purple-check-intl-icon').attr('uk-icon', 'icon: check');

                    // Text
                    $('.purple-check-intl').find('.purple-check-intl-text').text('intl PHP extension enabled');
                <?php
                    else:
                ?>
                    // List
                    $('.purple-check-intl').addClass('uk-text-danger');
                    
                    // Icon
                    $('.purple-check-intl').find('.purple-check-intl-icon').attr('uk-icon', 'icon: warning');

                    // Text
                    $('.purple-check-intl').find('.purple-check-intl-text').text('Please enable intl PHP extension');
                <?php
                    endif;
                ?>
                }, 6000);

                setTimeout(function() {
                <?php
                    if (version_compare(PHP_VERSION, '7.1.3') >= 0 && extension_loaded('intl') && extension_loaded('mbstring')):
                ?>
                    $('.purple-check-req-step').append('<li class="uk-animation-slide-bottom-medium">You are ready to go! </li>')
                <?php
                    else:
                ?>
                    $('.purple-check-req-step').append('<li class="uk-animation-slide-bottom-medium">Your machine does not meet the minimum requirements.</li>')
                <?php
                    endif;
                ?>
                }, 6500);
            })
        </script>
        <?php
            endif;

            if ($checkStep == 'index' || $checkStep == 'administrative' || ($this->request->getParam('controller') == 'Production' && ($checkStep == 'userVerification' || $checkStep == 'codeVerification' || $checkStep == 'databaseMigration'))):
        ?>
        <script>
            $(document).ready(function() {
                <?php if ($checkStep == 'administrative'): ?>
                var userTimezone = moment.tz.guess();
                $('select[name=timezone] option:contains('+userTimezone+')').attr('selected', 'selected');
                <?php endif; ?>

                <?php
                    if (version_compare(PHP_VERSION, '7.1.3') >= 0 && extension_loaded('intl') && extension_loaded('mbstring')):
                ?>
                    setTimeout(function() {
                        UIkit.modal('#modal-setup-purple').show();
                        $(".setup-loader").hide();
                    <?php if ($checkStep == 'index') echo '}, 8000);'; else echo '}, 2000);'; ?>
                <?php
                    endif;
                ?>
            })
        </script>
        <?php
            else:
        ?>
        <script>
            $(document).ready(function() {
                <?php
                    if (version_compare(PHP_VERSION, '7.1.3') >= 0 && extension_loaded('intl') && extension_loaded('mbstring')):
                ?>
                    UIkit.modal('#modal-setup-purple').show();
                <?php
                    endif;
                ?>
            })
        </script>
        <?php
            endif;
        ?>
    </body>
</html>
