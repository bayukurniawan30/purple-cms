<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white mr-2">
            <i class="mdi <?= $pageTitleIcon ?>"></i>                 
        </span>
        <?= $pageTitle ?>
    </h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <?php 
                if ($this->request->getParam('controller') != 'Dashboard'):
            ?>
            <li class="breadcrumb-item active" aria-current="page">
                <a href="<?= $this->Url->build(['_name' => 'adminDashboard']); ?>">Dashboard</a>
            </li>
            <?php
                endif;

                if (strpos($pageBreadcrumb, '::') !== false): 
                    $explodeBreadcrumb = explode('::', $pageBreadcrumb);
                    foreach ($explodeBreadcrumb as $data):
                        if ($data === end($explodeBreadcrumb)):
            ?>
            <li class="breadcrumb-item active" aria-current="page">
                <span></span><?= $data ?>
            </li>
            <?php
                        else:
            ?>
            <li class="breadcrumb-item"><?= $data ?></li>
            <?php
                        endif;
                    endforeach;
                else: 
            ?>
            <li class="breadcrumb-item active" aria-current="page">
                <span></span><?= $pageBreadcrumb ?>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>