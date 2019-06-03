<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="<?= $this->Url->build(['_name' => 'adminDashboard']); ?>">
            <?= $this->Html->image('/master-assets/img/logo.svg', ['alt' => 'Purple CMS']) ?>
        </a>
        <a class="navbar-brand brand-logo-mini" href="<?= $this->Url->build(['_name' => 'adminDashboard']); ?>">
            <?= $this->Html->image('/master-assets/img/logo-mini.svg', ['alt' => 'Purple CMS']) ?>

        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <div class="search-field d-none d-md-block">
            <?php
                echo $this->Form->create($dashboardSearch, [
                    'id'                    => 'form-search',
                    'class'                 => 'd-flex align-items-center h-100',
                    'data-parsley-validate' => '',
                    'url'                   => ['_name' => 'adminSearchPost'],
                ]);
            ?>
                <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                        <i class="input-group-text border-0 mdi mdi-magnify"></i>
                    </div>
                    <?php
                        echo $this->Form->text('search', [
                            'class'                  => 'form-control bg-transparent border-0',
                            'placeholder'            => 'Search posts',
                            'data-parsley-minlength' => '2',
                            'data-parsley-maxlength' => '100',
                            'value'                  => '',
                            'required'               => 'required'
                        ]);
                    ?>
                </div>
            <?php
                echo $this->Form->end();
            ?>
        </div>
        <ul class="navbar-nav navbar-nav-right purple-navbar-header">
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <div class="nav-profile-img">
                        <?php if ($adminPhoto === NULL): ?>
                        <img class="initial-photo" src="" alt="<?= $adminName ?>" data-name="<?= $adminName ?>" data-height="32" data-width="32" data-char-count="2" data-font-size="16">
                        <?php else: ?>
                        <img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $adminPhoto ?>" alt="<?= $adminName ?>">
                        <?php endif; ?>
                        <span class="availability-status online"></span>
                    </div>
                    <div class="nav-profile-text">
                        <p class="mb-1 text-black"><span class="greeting-to-user"><?php if(is_array($greeting)) echo $greeting['greeting'].', '; else echo $greeting ?></span><?= $adminName ?></p>
                    </div>
                </a>
                <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="<?= $this->Url->build(['_name' => 'home']); ?>" target="_blank">
                        <i class="mdi mdi-web mr-2 text-success"></i>
                        Go to Website
                    </a>
                    <a id="messages-with-counter" class="dropdown-item" href="<?= $this->Url->build(['_name' => 'adminMessages']); ?>">
                        <i class="mdi mdi-email-outline mr-2 text-success"></i>
                        Messages
                    </a>
                    <a class="dropdown-item" href="<?= $this->Url->build(['_name' => 'adminHistories']); ?>">
                        <i class="mdi mdi-cached mr-2 text-success"></i>
                        Activity Log
                    </a>
                    <a class="dropdown-item button-about-purple-cms" href="#" data-purple-modal="#modal-about-purple-cms">
                        <i class="mdi mdi-information-outline mr-2 text-success"></i>
                        About
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= $this->Url->build(['_name' => 'adminLogout']); ?>">
                        <i class="mdi mdi-logout mr-2 text-primary"></i>
                        Logout
                    </a>
                </div>
            </li>
            <li class="nav-item d-none d-lg-block full-screen-link">
                <a class="nav-link" title="Toggle Fullscreen">
                    <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown" aria-expanded="false" title="Notification">
                    <i class="mdi mdi-bell-outline"></i>
                    
                </a>
                <div id="dashboard-notifications" class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                    <h6 class="p-3 mb-0">Notifications</h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item"><i class="fa fa-circle-o-notch fa-spin uk-margin-small-right"></i> Loading...</a>
                </div>
            </li>
            <li class="nav-item nav-logout d-none d-lg-block">
                <a class="nav-link" title="Logout" href="<?= $this->Url->build(['_name' => 'adminLogout']); ?>">
                    <i class="mdi mdi-power"></i>
                </a>
            </li>
            <!-- <li class="nav-item nav-settings d-none d-lg-block">
                <a class="nav-link" href="#">
                    <i class="mdi mdi-format-line-spacing"></i>
                </a>
            </li> -->
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>

<!-- About Purple CMS -->
<?= $this->element('Dashboard/Modal/about_purple_cms_modal') ?>
