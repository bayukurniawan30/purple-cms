<?php
    $headlessWebsite = str_replace('https://', '', $headlessWeb);
    $headlessWebsite = str_replace('http://', '', $headlessWebsite);
?>

<div class="row">
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder dashboard-statistic-card">
            <div class="card-body">
                <?= $this->Html->image('/master-assets/img/circle.svg', ['class' => 'card-img-absolute', 'alt' => 'circle-image']) ?>
                <h4 class="font-weight-normal mb-3">Headless CMS
                    <i class="mdi mdi-layers mdi-24px float-right"></i>
                </h4>
                <h2 class="mb-5"><?= $headlessStatus == 'enable' ? 'Enabled' : 'Disabled' ?></h2>
                <h6 class="card-text">Front-End site: <?= $headlessWebsite ?></h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder dashboard-statistic-card">
            <div class="card-body">
                <?= $this->Html->image('/master-assets/img/circle.svg', ['class' => 'card-img-absolute', 'alt' => 'circle-image']) ?>
                <h4 class="font-weight-normal mb-3">Collections
                    <i class="mdi mdi-checkbox-multiple-blank-outline mdi-24px float-right"></i>
                </h4>
                <h2 class="mb-5"><?= $totalCollections ?></h2>
                <h6 class="card-text">Contain <?= $this->Purple->plural($totalCollectionDatas, ' data') ?></h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div id="weather-card" class="card bg-gradient-success card-img-holder dashboard-statistic-card">
            <div class="card-body">
                <?= $this->Html->image('/master-assets/img/circle.svg', ['class' => 'card-img-absolute weather-image', 'alt' => 'circle-image']) ?>
                <h4 class="font-weight-normal weather-currently mb-3">Singletons
                    <i class="mdi mdi-cube-outline mdi-24px float-right"></i>
                </h4>
                <h2 class="weather-temp mb-5"><?= $totalSingletons ?></h2>
                <h6 class="card-text weather-location">Contain <?= $this->Purple->plural($totalSingletonDatas, ' data') ?></h6>
            </div>
        </div>
    </div>
</div>