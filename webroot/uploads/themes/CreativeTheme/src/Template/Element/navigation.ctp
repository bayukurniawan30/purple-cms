<ul class="sidebar-menu">
    <!-- Link-->
    <li class="sidebar-item">
        <a href="<?= $this->Url->build(['_name' => 'home']); ?>" class="sidebar-link <?php if ($this->request->getParam('action') == 'home') echo 'active' ?>">Home</a>
    </li>
    <?php
        if ($menus):
            foreach ($menus as $menu):
                if ($menu['has_sub'] == 0):
                    if ($menu['page'] == NULL) {                                        
                        $pageUrl = $menu['target'];
                    }
                    else {
                        $pageUrl = $this->Url->build([
                            '_name' => 'specificPage',
                            'page'  => $menu['target']
                        ]);
                    }
    ?>
    <!-- Link-->
    <li class="sidebar-item">
        <a href="<?= $pageUrl; ?>" class="sidebar-link <?php if ($this->request->getParam('page') == $menu['target']) echo 'active' ?>"><?= $menu['title'] ?></a>
    </li>
    <?php
                else:
                    // Theme does not support nested navigation
                endif;
            endforeach;
        endif;
    ?>
</ul>