<?php
    $loadTopCountriesUrl = $this->Url->build([
        '_name'  => 'adminDashboardAction',
        'action' => 'ajaxGetTopCountries',
    ]);

    $loadTopPostsUrl = $this->Url->build([
        '_name'  => 'adminDashboardAction',
        'action' => 'ajaxGetTopPosts',
    ]);
?>

<?php
    if ($headlessStatus == 'enable') {
        echo $this->element('Dashboard/Statistic/headless');
    }
    else {
        echo $this->element('Dashboard/Statistic/conventional');
    }
?>

<div class="row">
    <div class="col-md-12">
        <div class="uk-alert-primary uk-margin-medium-bottom" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p>
                <strong>Still confused how to use Purple?</strong><br>
                Read the full documentation <a href="https://bayukurniawan30.github.io/purple-cms/#/" target="_blank">here</a>. Are you a developer? We have a <a href="https://bayukurniawan30.github.io/purple-cms/#/generate-documentation" target="_blank">complete guide</a> to customize Purple CMS.
            </p>
        </div>
    </div>
</div>

<?php
    if ($headlessStatus == 'enable'):
?>
<div class="row">
    <div class="col-md-12">
        <div class="uk-alert-warning uk-margin-medium-bottom" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p>
                <strong>Headless CMS Is Enabled</strong><br>
                Visitors stats won't count. Please use third party service like <a href="https://analytics.google.com/analytics/web/" target="_blank">Google Analytics</a> in your Front-End.
            </p>
        </div>
    </div>
</div>
<?php
    endif;
?>

<div id="dashboard-main-panel" class="row">
    <div id="dashboard-statistic-container" class="col-md-7 grid-margin stretch-card">
        <?php
            if ($headlessStatus == 'enable'):
                $allCollectionUrl = $this->Url->build([
                    '_name'  => 'adminCollections',
                ]);

                $newCollectionUrl = $this->Url->build([
                    '_name'  => 'adminCollectionsAction',
                    'action' => 'add'
                ]);

                $allSingletonUrl = $this->Url->build([
                    '_name'  => 'adminSingletons',
                ]);

                $newSingletonUrl = $this->Url->build([
                    '_name'  => 'adminSingletonsAction',
                    'action' => 'add'
                ]);
        ?>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Components</h4>

                <ul class="components-tab" uk-tab="swiping: false">
                    <li><button class="btn btn-outline-primary">Collections</button></li>
                    <li><button class="btn btn-outline-primary ml-2">Singletons</button></li>
                </ul>
                
                <ul class="uk-switcher uk-margin" uk-switcher="swiping: false">
                    <li>
                        <?php
                            if ($collectionsTable->count() > 0):
                        ?>
                        <div class="table-responsive components-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="20"> # </th>
                                        <th width="150"> Name </th>
                                        <th> Fields and Datas </th>
                                        <th class="uk-visible@xl"> Created </th>
                                        <th class="text-center"> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 1;
                                        foreach ($collectionsTable as $collTable):
                                            $countDatas   = (int)$this->cell('Collections::countDatas', [$collTable->id])->render();
                                            $decodeFields = json_decode($collTable->fields, true);

                                            $viewDataUrl = $this->Url->build([
                                                '_name' => 'adminCollectionsViewData',
                                                'data'  => $collTable->slug
                                            ]);
                                    ?>
                                    <tr>
                                        <td> <?= $i ?> </td>
                                        <td class="uk-text-truncate"> <?= $collTable->name ?> </td>
                                        <td> Contain <?= $this->Purple->plural(count($decodeFields), ' field') ?> <?= $countDatas > 0 ? ' and ' . $this->Purple->plural($countDatas, ' data') : '' ?> </td>
                                        <td class="uk-visible@xl"> <?= $this->Time->format(
                                            $collTable->created,
                                            'MMMM dd, yyyy HH:mm',
                                            null,
                                            ); ?> </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-inverse-primary btn-rounded btn-icon" uk-tooltip="View" onclick="location.href='<?= $viewDataUrl ?>'">
                                                <i class="mdi mdi-checkbox-multiple-blank-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                            $i++;
                                        endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-outline-primary btn-sm mt-3" onclick="location.href='<?= $allCollectionUrl ?>'">All Collections</button>
                        <?php
                            else:
                        ?>
                        <button class="btn btn-outline-primary btn-sm mt-3" onclick="location.href='<?= $newCollectionUrl ?>'">Create New Collection</button>
                        <?php
                            endif;
                        ?>
                    </li>
                    <li>
                    <?php
                            if ($singletonsTable->count() > 0):
                        ?>
                        <div class="table-responsive components-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="20"> # </th>
                                        <th width="150"> Name </th>
                                        <th> Fields and Datas </th>
                                        <th class="uk-visible@xl"> Created </th>
                                        <th class="text-center"> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $j = 1;
                                        foreach ($singletonsTable as $singTable):
                                            $countDatas   = (int)$this->cell('Singletons::countDatas', [$singTable->id])->render();
                                            $decodeFields = json_decode($singTable->fields, true);

                                            $viewDataUrl = $this->Url->build([
                                                '_name' => 'adminSingletonsViewData',
                                                'data'  => $singTable->slug
                                            ]);
                                    ?>
                                    <tr>
                                        <td> <?= $j ?> </td>
                                        <td class="uk-text-truncate"> <?= $singTable->name ?> </td>
                                        <td> Contain <?= $this->Purple->plural(count($decodeFields), ' field') ?> <?= $countDatas > 0 ? ' and ' . $this->Purple->plural($countDatas, ' data') : '' ?> </td>
                                        <td class="uk-visible@xl"> <?= $this->Time->format(
                                            $singTable->created,
                                            'MMMM dd, yyyy HH:mm',
                                            null,
                                            ); ?> </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-inverse-primary btn-rounded btn-icon" uk-tooltip="View" onclick="location.href='<?= $viewDataUrl ?>'">
                                                <i class="mdi mdi-cube-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                            $j++;
                                        endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-outline-primary btn-sm mt-3" onclick="location.href='<?= $allSingletonUrl ?>'">All Singletons</button>
                        <?php
                            else:
                        ?>
                        <button class="btn btn-outline-primary btn-sm mt-3" onclick="location.href='<?= $newSingletonUrl ?>'">Create New Singleton</button>
                        <?php
                            endif;
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <?php
            else:
        ?>
        <div class="card">
            <div id="load-dashboard-statistic" class="card-body">
                <div class="clearfix">
                    <h4 class="card-title float-left">Visitors Statistics<br>
                        <!-- <small id="selected-title-date-range">(Last 6 Months)</small> -->
                    </h4>
                    <div id="visit-sale-chart-legend" class="rounded-legend legend-horizontal legend-top-right float-right"></div>                                     
                </div>
                <canvas id="visit-sale-chart" class="mt-4"></canvas>
                
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <h5 class="uk-margin-small-bottom">Visitors</h5>
                        <p class="uk-margin-small uk-margin-remove-bottom"><?= $this->Purple->shortenNumber($totalAllVisitors) ?></p>
                        <hr class="uk-hidden@l uk-hidden@xl">
                    </div>
                    <div class="col-md-4 text-center">
                        <h5 class="uk-margin-small-bottom">Mobile</h5>
                        <p class="uk-margin-small uk-margin-remove-bottom"><?= $this->Purple->shortenNumber($totalMobileVisitors) ?></p>
                        <hr class="uk-hidden@l uk-hidden@xl">
                    </div>
                    <div class="col-md-4 text-center">
                        <h5 class="uk-margin-small-bottom">Last 2 Weeks</h5>
                        <p class="uk-margin-small uk-margin-remove-bottom"><?= $this->Purple->shortenNumber(array_sum($twoWeeksVisitors)) ?> <?php if ($twoWeeksIcon == 'up') echo '<i class="fa fa-arrow-up text-primary uk-animation-slide-bottom"></i>'; elseif ($twoWeeksIcon == 'same') echo '<i class="fa fa-exchange text-success uk-animation-scale-up"></i>'; elseif ($twoWeeksIcon == 'down') echo '<i class="fa fa-arrow-down text-danger uk-animation-slide-top"></i>'; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
            endif;
        ?>
    </div>
    <div id="recent-activity-container" class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Recent Activity</h4>
                <?php
                    if ($histories->count() == 0): 
                ?>
                <div class="uk-alert-primary" uk-alert>
                    <p>No Activity found yet.</p>
                </div>
                <?php
                    else:
                ?>
                <table class="table">
                    <tbody>
                        <?php
                            foreach ($histories as $history):
                        ?>
                        <tr>
                            <td class="text-center uk-table-shrink">
                                <?php if ($history->admin->photo === NULL): ?>
                                <img class="initial-photo" src="" alt="<?= $history->admin->get('display_name') ?>" data-name="<?= $history->admin->get('display_name') ?>" data-height="28" data-width="28" data-char-count="2" data-font-size="14" style="max-width: none;">
                                <?php else: ?>
                                <img src="<?= $this->cell('Medias::mediaPath', [$history->admin->photo, 'image', 'original']) ?>" alt="<?= $history->admin->get('display_name') ?>" width="28" height="28" style="max-width: none;">
                                <?php endif; ?>    
                            </td>
                            <td uk-tooltip="title:<?= $history->admin->get('display_name').' '.$history->detail ?>; pos:bottom-left; offset: -15">
                                <?= $history->title ?><br>
                                <small><?= $history->admin->get('display_name') ?></small> - <small data-livestamp="<?= $history->created ?>"></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
    if ($headlessStatus != 'enable'):
?>
<div class="row">
    <div class="col-md-4 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Top 10 Countries</h4>
            </div>
            <div id="bind-top-countries" class="card-body uk-padding-small uk-inline" style="min-height: 300px; height: auto; padding: 15px 30px" data-purple-url="<?= $loadTopCountriesUrl ?>">
                <div class="uk-position-center"><div uk-spinner="ratio: 1.5"></div></div>
            </div>
        </div>
    </div>

    <div class="col-md-8 grid-margin">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Top 10 Posts</h4>
            </div>
            <div id="bind-top-posts" class="card-body uk-padding-small uk-inline" style="min-height: 300px; height: auto; padding: 15px 30px" data-purple-url="<?= $loadTopPostsUrl ?>">
                <div class="uk-position-center"><div uk-spinner="ratio: 1.5"></div></div>
            </div>
        </div>
    </div>
</div>
<?php
    endif;
?>

<div id="modal-select-date-range" class="uk-flex-top purple-modal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <?php
            echo $this->Form->create($dashboardMonthOfVisit, [
                'id'                    => 'form-month-of-visit', 
                'class'                 => 'pt-3', 
                'data-parsley-validate' => '',
                'url'                   => ['action' => 'ajax-update-statistic']
            ]);
        ?>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">Select Month of Visit</h3>
        </div>
        <div class="uk-modal-body">
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'month',
                        $monthLatinArray,
                        [
                            'empty'    => 'Select Month', 
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
            <div class="form-group">
                <?php
                    echo $this->Form->select(
                        'year',
                        $yearArray,
                        [
                            'empty'    => 'Select Year', 
                            'class'    => 'form-control',
                            'required' => 'required'
                        ]
                    );
                ?>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <?php
                echo $this->Form->button('Update Statistic', [
                    'id'    => 'button-month-of-visit',
                    'class' => 'btn btn-gradient-primary'
                ]);

                echo $this->Form->button('Cancel', [
                    'id'           => 'button-close-modal',
                    'class'        => 'btn btn-outline-primary uk-margin-left uk-modal-close',
                    'type'         => 'button',
                    'data-target'  => '.purple-modal'
                ]);
            ?>
        </div>
        <?php
            echo $this->Form->end();
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form-month-of-visit').find('select[name=month] option[value="<?= $selectedMonth ?>"]').attr("selected","selected");
        $('#form-month-of-visit').find('select[name=year] option[value="<?= $selectedYear ?>"]').attr("selected","selected");

        var statisticUpdate = {
            form            : 'form-month-of-visit', 
            button          : 'button-month-of-visit',
            action          : 'dashboard-statistic', 
            redirectType    : 'ajax', 
            redirect        : '#dashboard-main-panel', 
            btnNormal       : 'Update Statistic', 
            btnLoading      : '<i class="fa fa-circle-o-notch fa-spin"></i> Updating Statistic...' 
        };
        
        var targetButton = $("#"+statisticUpdate.button);
        targetButton.one('click',function() {
            ajaxSubmit(statisticUpdate.form, statisticUpdate.action, statisticUpdate.redirectType, statisticUpdate.redirect, statisticUpdate.btnNormal, statisticUpdate.btnLoading, true, true);
        });
    });
</script>