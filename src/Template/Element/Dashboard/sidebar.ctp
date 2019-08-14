<?php
    $newPostUrl = $this->Url->build([
        '_name'  => 'adminBlogsAction',
        'action' => 'add'
    ]);
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav non-uikit">
        <li class="nav-item nav-profile">
            <a href="<?= $this->Url->build(['_name' => 'adminUsersEdit', 'id' => $sessionID]); ?>" class="nav-link">
                <div class="nav-profile-image">
                    <?php if ($adminPhoto === NULL): ?>
                    <img class="initial-photo" src="" alt="<?= $adminName ?>" data-name="<?= $adminName ?>" data-height="44" data-width="44" data-char-count="2" data-font-size="22">
                    <?php else: ?>
                    <img src="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $adminPhoto ?>" alt="<?= $adminName ?>">
                    <?php endif; ?>
                    <span class="login-status online"></span>
                    <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2"><?= $adminName ?></span>
                    <span class="text-secondary text-small">
                        <?php if ($adminLevel == 1): ?>
                            Administrator
                        <?php endif; ?>
                    </span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminDashboard']); ?>">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <?php
            if ($adminLevel == 1):
        ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#purple-appearance" aria-expanded="false" aria-controls="purple-appearance">
                <span class="menu-title">Appearance</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-image menu-icon"></i>
                </a>
            <div class="collapse" id="purple-appearance">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminAppearanceAction', 'action' => 'favicon']); ?>"> Favicon </a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminAppearanceAction', 'action' => 'logo']); ?>"> Logo </a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminAppearanceAction', 'action' => 'footer']); ?>"> Footer </a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminNavigation']); ?>">
                <span class="menu-title">Navigation</span>
                <i class="mdi mdi-menu menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminThemes']); ?>">
                <span class="menu-title">Themes</span>
                <i class="mdi mdi-image menu-icon"></i>
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminBlogs']); ?>">
                <span class="menu-title">Posts</span>
                <i class="mdi mdi-library-books menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminPages']); ?>">
                <span class="menu-title">Pages</span>
                <i class="mdi mdi-file-multiple menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#purple-media" aria-expanded="false" aria-controls="purple-media">
                <span class="menu-title">Medias</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-folder-image menu-icon"></i>
            </a>
            <div class="collapse" id="purple-media">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminMediasAction', 'action' => 'documents']); ?>">Documents</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminMediasAction', 'action' => 'images']); ?>">Images</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminMediasAction', 'action' => 'videos']); ?>">Videos</a></li>
                </ul>
            </div>
        </li>
        <?php
            if ($adminLevel == 1):

                foreach ($plugins as $plugin):
                    if ($plugin['yes'] == true) {
                        $pluginNamespace = $plugin['namespace'];
                        $pluginSidebar   = $plugin['dashboard_sidebar']['name'];
                        echo $this->element($pluginNamespace . '.' . $pluginSidebar);
                    }
                endforeach;
        ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#purple-socials" aria-expanded="false" aria-controls="purple-socials">
                <span class="menu-title">Socials</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-share-variant menu-icon"></i>
            </a>
            <div class="collapse" id="purple-socials">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSocials']); ?>">Account and Sharing</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSubscribers']); ?>">Subscribers</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminUsers']); ?>">
                <span class="menu-title">Users</span>
                <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#purple-settings" aria-expanded="false" aria-controls="purple-settings">
                <span class="menu-title">Settings</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-settings menu-icon"></i>
            </a>
            <div class="collapse" id="purple-settings">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'general']); ?>">General</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'posts']); ?>">Posts</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'email']); ?>">Email</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'seo']); ?>">SEO</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'maintenance']); ?>">Maintenance</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'personalize']); ?>">Personalize</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'security']); ?>">Security</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= $this->Url->build(['_name' => 'adminSettingsAction', 'action' => 'api']); ?>">API</a></li>
                </ul>
            </div>
        </li>
    <?php endif; ?>
        <li class="nav-item sidebar-actions">
            <span class="nav-link">
                <div class="border-bottom">
                </div>
                <button class="btn btn-block btn-lg btn-gradient-primary btn-icon-text mt-4" onclick="location.href='<?= $newPostUrl ?>'"><i class="mdi mdi-lead-pencil btn-icon-prepend"></i> New Post</button>
            </span>
        </li>
    </ul>
</nav>
