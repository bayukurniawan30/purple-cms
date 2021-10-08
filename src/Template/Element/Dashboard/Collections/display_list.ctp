<div class="col-md-12">
    <ul class="display-list" uk-grid>
        <?php
            foreach ($collections as $collection):
                $countDatas = (int)$this->cell('Collections::countDatas', [$collection->id])->render();

                $decodeFields = json_decode($collection->fields, true);

                $addDataUrl = $this->Url->build([
                    '_name' => 'adminCollectionsData',
                    'data'  => $collection->slug
                ]);

                $viewDataUrl = $this->Url->build([
                    '_name' => 'adminCollectionsViewData',
                    'data'  => $collection->slug
                ]);
                
                $editUrl = $this->Url->build([
                    '_name' => 'adminCollectionsEdit',
                    'id'    => $collection->id
                ]);
        ?>
        <li class="uk-width-1-1 uk-margin-remove-top" style="position: relative">
            <div class="uk-card uk-card-default uk-card-small uk-card-body">
                <?= $collection->name ?> <span class="uk-text-muted uk-text-italic uk-text-small uk-margin-left"><?= $this->Purple->plural(count($decodeFields), ' field') ?> <?= $countDatas > 0 ? ' and ' . $this->Purple->plural($countDatas, ' data') : '' ?></span>

                <div class="uk-inline uk-align-right">
                    <?php
                        if ($status != 'deleted'):
                    ?>
                    <a href="<?= $addDataUrl ?>" class="uk-margin-small-right" uk-icon="icon: plus" uk-tooltip="Add"></a>
                    <a href="<?= $viewDataUrl ?>" class="uk-margin-small-right" uk-icon="icon: list" uk-tooltip="View"></a>
                    <a href="<?= $editUrl ?>" class="uk-margin-small-right" uk-icon="icon: pencil" uk-tooltip="Edit"></a>
                    <a href="<?= $editUrl ?>" class="uk-margin-small-right uk-text-danger button-delete-purple" data-purple-name="<?= $collection->name ?>" data-purple-id="<?= $collection->id ?>" data-purple-modal="#modal-delete-collection" uk-icon="icon: close" uk-tooltip="Delete"></a>
                    <?php
                        endif;
                    ?>
                </div>
            </div>
        </li>
        <?php
            endforeach;
        ?>
    </ul>
</div>