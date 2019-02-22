<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <!-- plugins:css -->
    <?= $this->Html->css('/master-assets/plugins/iconfonts/mdi/css/materialdesignicons.min.css') ?>
    <?= $this->Html->css('/master-assets/css/vendor.bundle.base.css') ?>
    <!-- endinject -->
    <!-- inject:css -->
    <?= $this->Html->css('/master-assets/css/style.css') ?>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center text-center error-page bg-primary">
                <div class="row flex-grow">
                    <div class="col-lg-7 mx-auto text-white">
                        <div class="row align-items-center d-flex flex-row">
                            <?= $this->Flash->render() ?>

                            <?= $this->fetch('content') ?>
                        </div>
                        <div class="row mt-5">
                            <div class="col-12 text-center mt-xl-2">
                                <?= $this->Html->link(
                                    __('Back'),
                                    'javascript:history.back()',
                                    ['class' => 'text-white font-weight-medium']) 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <?= $this->Html->script('/master-assets/js/vendor.bundle.base.js'); ?>
    <?= $this->Html->script('/master-assets/js/vendor.bundle.addons.js'); ?>
    <!-- endinject -->
    <!-- inject:js -->
    <?= $this->Html->script('/master-assets/js/off-canvas.js'); ?>
    <?= $this->Html->script('/master-assets/js/misc.js'); ?>
    <!-- endinject -->
</body>
</body>
</html>
