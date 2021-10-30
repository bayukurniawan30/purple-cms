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
                <h4 class="font-weight-normal weather-currently mb-3">Blog Posts
                    <i class="mdi mdi-library-books mdi-24px float-right"></i>
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