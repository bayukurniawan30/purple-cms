<nav class="navbar navbar-expand-lg navbar-light main-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= $this->Url->build(['_name' => 'home']); ?>">
            <?php
                if ($logo == ''):
                    echo $this->Html->image('logo.svg', ['alt' => '', 'class' => 'main-logo']);
                else:
                    echo '<img src="'.$this->request->getAttribute("webroot").'uploads/images/original/' . $logo.'" alt="'.$siteName.'" height="40" style="height: 40px">';
                endif; 
            ?>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?php if ($this->request->getParam('action') == 'home') echo 'active' ?>">
                    <a class="nav-link" href="<?= $this->Url->build(['_name' => 'home']); ?>">Home</a>
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
                <li class="nav-item <?php if ($this->request->getParam('page') == $menu['target']) echo 'active' ?>">
                    <a class="nav-link" href="<?= $pageUrl; ?>"><?= $menu['title'] ?></a>
                </li>
                <?php 
                            else:
                ?>  
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $menu['title'] ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="">
                        <?php
                            foreach ($menu['child'] as $submenu):
                                if ($submenu['page'] == NULL) {                                     
                                    $pageUrl = $submenu['target'];
                                }
                                else {
                                    $pageUrl = $this->Url->build([
                                        '_name' => 'specificPage',
                                        'page'  => $submenu['target']
                                    ]);
                                }
                        ?>        
                        <a class="dropdown-item" href="<?= $pageUrl ?>"><?= $submenu['title'] ?></a>
                        <?php
                            endforeach;
                        ?>
                    </div>
                </li>
                <?php
                            endif;
                        endforeach;
                    endif;
                ?>  
            </ul>
        </div>
    </div>
</nav>
