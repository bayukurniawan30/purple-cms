<div class="row">
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder dashboard-statistic-card">
            <div class="card-body">
                <?= $this->Html->image('/master-assets/img/circle.svg', ['class' => 'card-img-absolute', 'alt' => 'circle-image']) ?>
                <h4 class="font-weight-normal mb-3">Monthly Visitors
                    <i class="mdi mdi-chart-line mdi-24px float-right"></i>
                </h4>
                <h2 class="mb-5"><?= $statisticVisitors ?></h2>
                <h6 class="card-text"><?= $visitorsCard ?></h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder dashboard-statistic-card">
            <div class="card-body">
                <?= $this->Html->image('/master-assets/img/circle.svg', ['class' => 'card-img-absolute', 'alt' => 'circle-image']) ?>
                <h4 class="font-weight-normal mb-3">Post Comments
                    <i class="mdi mdi-comment-multiple-outline mdi-24px float-right"></i>
                </h4>
                <h2 class="mb-5"><?= $this->Purple->shortenNumber($allComments) ?></h2>
                <h6 class="card-text"><?= $this->Purple->plural($unreadComments, ' unread comment') ?></h6>
            </div>
        </div>
    </div>
    <div class="col-md-4 stretch-card grid-margin">
        <div id="weather-card" class="card bg-gradient-success card-img-holder dashboard-statistic-card">
            <div class="card-body">
                <?= $this->Html->image('/master-assets/img/circle.svg', ['class' => 'card-img-absolute weather-image', 'alt' => 'circle-image']) ?>
                <h4 class="font-weight-normal weather-currently mb-3">
                    <i class="mdi mdi-library-books mdi-24px float-right"></i>
                    Blog Posts
                </h4>
                <h2 class="weather-temp mb-5"><?= $this->Purple->shortenNumber($allPosts) ?></h2>
                <h6 class="card-text weather-location">
                    <?php
                        if ($draftPosts > 0):
                            echo $this->Purple->plural($draftPosts, ' post').' needs update';
                        else:
                            if ($oneMonthPosts <= 2): 
                                echo 'Visitors wants to read posts from you';
                            else:
                                echo 'Keep writing a good post';
                            endif;
                        endif;
                    ?>
                </h6>
            </div>
        </div>
    </div>
</div>

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

<div id="dashboard-main-panel" class="row">
    <div id="dashboard-statistic-container" class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div id="load-dashboard-statistic" class="card-body">
                <div class="clearfix">
                    <h4 class="card-title float-left">Visitors Statistics<br><small id="selected-title-date-range">(Last 6 Months)</small></h4>
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
                            <td class="text-center">
                                <?php if ($history->admin->photo === NULL): ?>
                                <img class="initial-photo" src="" alt="<?= $history->admin->get('display_name') ?>" data-name="<?= $history->admin->get('display_name') ?>" data-height="28" data-width="28" data-char-count="2" data-font-size="14" style="max-width: none;">
                                <?php else: ?>
                                <img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $history->admin->photo ?>" alt="<?= $history->admin->get('display_name') ?>" width="28" height="28" style="max-width: none;">
                                <?php endif; ?>    
                            </td>
                            <td uk-tooltip="title:<?= $history->admin->get('display_name').' '.$history->detail ?>; pos:bottom">
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