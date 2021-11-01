<?php
    $queryString   = $this->request->getQueryParams();
    $status        = $this->request->getQuery('status', 'all');
    $displayString = $this->request->getQuery('display', 'grid');

    $displayGridArray = ['display' => 'grid'];
    $displayListArray = ['display' => 'list'];
    $publishedArray   = ['status'  => 'publish'];
    $draftArray       = ['status'  => 'draft'];
    $deletedArray     = ['status'  => 'deleted'];
    if (count($queryString) > 0) {
        $displayGridArray = $displayGridArray + $queryString;
        $displayListArray = $displayListArray + $queryString;
        $publishedArray   = $publishedArray + $queryString;
        $draftArray       = $draftArray + $queryString;
        $deletedArray     = $deletedArray + $queryString;
    }

    $displayGridUrl = $this->Url->build([
        '_name'  => 'adminSingletons',
        '?'      => $displayGridArray
    ]);

    $displayListUrl = $this->Url->build([
        '_name'  => 'adminSingletons',
        '?'      => $displayListArray
    ]);

    $newSingletonUrl = $this->Url->build([
		'_name'  => 'adminSingletonsAction',
		'action' => 'add'
    ]);

    $submitRedirect = $this->Url->build([
        '_name' => 'adminSingletons'
    ]);

    $publishedUrl = $this->Url->build([
        '_name' => 'adminSingletons',
        '?'     => $publishedArray
    ]);

    $draftUrl = $this->Url->build([
        '_name' => 'adminSingletons',
        '?'     => $draftArray
    ]);

    $deletedUrl = $this->Url->build([
        '_name' => 'adminSingletons',
        '?'     => $deletedArray
    ]);

    $timezone = $this->getRequest()->getSession()->read('Purple.timezone');
?>

<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

<div class="row">
    <div class="col-md-12 uk-margin-bottom outside-btn-toolbar">
        <button type="button" class="btn btn-gradient-primary btn-toolbar-card btn-sm btn-icon-text" onclick="location.href='<?= $newSingletonUrl ?>'">
        <i class="mdi mdi-pencil btn-icon-prepend"></i>
            Add Singleton
        </button>

        <div class="uk-inline uk-align-right" style="margin-bottom: 0">
            <button type="button" class="btn btn-gradient-success btn-toolbar-card btn-sm btn-icon-text button-add-purple uk-margin-small-left">
                <i class="mdi mdi-filter btn-icon-prepend"></i>
                    Status : <?= $setStatus ?>
            </button>
            <div uk-dropdown="pos: bottom-right; mode: click">
                <ul class="uk-nav uk-dropdown-nav text-right">
                    <li class="<?= $status == 'all' ? 'uk-active' : '' ?>"><a href="<?= $submitRedirect ?>">All</a></li>
                    <li class="uk-nav-divider"></li>
                    <li class="<?= $status == 'publish' ? 'uk-active' : '' ?>"><a href="<?= $publishedUrl ?>">Publish</a></li>
                    <li class="<?= $status == 'draft' ? 'uk-active' : '' ?>"><a href="<?= $draftUrl ?>">Draft</a></li>
                    <li class="<?= $status == 'deleted' ? 'uk-active' : '' ?>"><a href="<?= $deletedUrl ?>">Deleted</a></li>
                </ul>
            </div>

            <button type="button" class="btn btn-dark btn-sm <?= $displayString == 'grid' ? 'active' : '' ?>" uk-tooltip="Grid View" onclick="location.href='<?= $displayGridUrl ?>'">
                <i class="mdi mdi-grid"></i>
            </button>

            <button type="button" class="btn btn-dark btn-sm <?= $displayString == 'list' ? 'active' : '' ?>" uk-tooltip="List View" onclick="location.href='<?= $displayListUrl ?>'">
                <i class="mdi mdi-view-list"></i>
            </button>
        </div>
    </div>

    <?php
        if ($singletons->count() > 0):
            if ($displayString == 'grid'):
                echo $this->element('Dashboard/Singletons/display_grid', [
                    'singletons' => $singletons,
                    'status'     => $status,
                ]);
            elseif ($displayString == 'list'):
                echo $this->element('Dashboard/Singletons/display_list', [
                    'singletons' => $singletons,
                    'status'     => $status,
                ]);
            endif;
        else:
    ?>
    <div class="col-md-12">
        <div class="uk-alert-danger <?php if ($singletons->count() == 0) echo 'uk-margin-remove-bottom'; ?>" uk-alert>
            <p>Can't find <?= $status != 'all' ? strtolower($setStatus) : '' ?> singleton. You can add a new singleton <a href="<?= $newSingletonUrl ?>" class="">here</a>.</p>
        </div>
    </div>    
    <?php
        endif;
    ?>
</div>

<?php
    if ($singletons->count() > 0):
        echo $this->element('Dashboard/Modal/delete_modal', [
            'action'     => 'singleton',
            'form'       => $singletonDelete,
            'formAction' => 'ajax-delete'
        ]);
    endif;
?>

<script type="text/javascript">
    $(document).ready(function() {
        var singletonDelete = {
            form            : 'form-delete-singleton',
            button          : 'button-delete-singleton',
            action          : 'delete',
            redirectType    : 'redirect',
            redirect        : '<?= $submitRedirect ?>',
            btnNormal       : false,
            btnLoading      : false
        };

        var targetButton = $("#"+singletonDelete.button);
        targetButton.one('click',function() {
            ajaxSubmit(singletonDelete.form, singletonDelete.action, singletonDelete.redirectType, singletonDelete.redirect, singletonDelete.btnNormal, singletonDelete.btnLoading);
        })
    })
</script>