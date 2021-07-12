<?php
    $status = $this->request->getQuery('status', 'all');

    $newSingletonUrl = $this->Url->build([
		'_name'  => 'adminSingletonsAction',
		'action' => 'add'
    ]);

    $submitRedirect = $this->Url->build([
        '_name' => 'adminSingletons'
    ]);

    $publishedUrl = $this->Url->build([
        '_name' => 'adminSingletons',
        '?'     => [
            'status' => 'publish'
        ]
    ]);

    $draftUrl = $this->Url->build([
        '_name' => 'adminSingletons',
        '?'     => [
            'status' => 'draft'
        ]
    ]);

    $deletedUrl = $this->Url->build([
        '_name' => 'adminSingletons',
        '?'     => [
            'status' => 'deleted'
        ]
    ]);

    $timezone = $this->getRequest()->getSession()->read('Purple.timezone');
?>

<?= $this->Flash->render('flash', [
    'element' => 'Flash/Purple/success'
]); ?>

<div class="row">
    <div class="col-md-12 uk-margin-bottom">
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
        </div>
    </div>

    <?php
        if ($singletons->count() > 0):
    ?>
    <div class="col-md-12">
        <div class="uk-child-width-1-4@m uk-grid-match" uk-grid="masonry: true">
            <?php
                foreach ($singletons as $collection):
                    $countDatas = (int)$this->cell('Singletons::countDatas', [$collection->id])->render();

                    $decodeFields = json_decode($collection->fields, true);

                    $addDataUrl = $this->Url->build([
                        '_name' => 'adminSingletonsData',
                        'data'  => $collection->slug
                    ]);

                    $viewDataUrl = $this->Url->build([
                        '_name' => 'adminSingletonsViewData',
                        'data'  => $collection->slug
                    ]);
                    
                    $editUrl = $this->Url->build([
                        '_name' => 'adminSingletonsEdit',
                        'id'    => $collection->id
                    ]);
            ?>
            <div>
                <div class="uk-card uk-card-default">
                    <div class="uk-card-media-top uk-inline">
                        <img class="" src="<?php echo $this->request->getAttribute("webroot") . 'master-assets/img/default-singleton-image.png' ?>" alt="" width="100%">

                        <?php
                            if ($status != 'deleted'):
                        ?>
                        <div class="uk-position-small uk-position-top-right">
                            <button type="button" uk-close uk-tooltip="Delete <?= $collection->name ?>" class="button-delete-purple" data-purple-name="<?= $collection->name ?>" data-purple-id="<?= $collection->id ?>" data-purple-modal="#modal-delete-singleton"></button>
                        </div>
                        <?php
                            endif;
                        ?>
                    </div>
                    <div class="uk-card-body">
                        <h4 class="uk-card-title uk-margin-remove"><?= $collection->name ?></h4>
                        <ul class="list-arrow">
                            <li><?= $collection->text_status ?></li>
                            <li>Contain <?= $this->Purple->plural(count($decodeFields), ' field') ?> <?= $countDatas > 0 ? ' and ' . $this->Purple->plural($countDatas, ' data') : '' ?></li>
                            <li><?= $collection->admin->display_name ?></li>
                            <li>Created at <?= $this->Time->format(
                                $collection->created,
                                'MMMM dd, yyyy HH:mm',
                                null,
                                ); ?>
                            </li>
                        </ul>
                    </div>
                    <?php
                        if ($status != 'deleted'):
                    ?>
                    <div class="uk-card-footer text-center uk-padding-small">
                        <div class="uk-child-width-1-<?= $countDatas < 1 ? '3' : '2' ?>" uk-grid="">
                            <?php
                                if ($countDatas < 1):
                            ?>
                            <div class="uk-text-center">
                                <a href="<?= $addDataUrl ?>" class="uk-button uk-button-text">Add</a>
                            </div>
                            <?php
                                endif;
                            ?>

                            <div class="uk-text-center">
                                <a href="<?= $viewDataUrl ?>" class="uk-button uk-button-text">View</a>
                            </div>

                            <div class="uk-text-center">
                                <a href="<?= $editUrl ?>" class="uk-button uk-button-text">Edit</a>
                            </div>
                        </div>
                    </div>
                    <?php
                        endif;
                    ?>
                </div>
            </div>
            <?php
                endforeach;
            ?>
        </div>
    </div>
    <?php
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