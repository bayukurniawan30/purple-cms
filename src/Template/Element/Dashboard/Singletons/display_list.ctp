<div class="col-md-12">
    <ul class="display-list" uk-grid>
        <?php
            foreach ($singletons as $singleton):
                $countDatas = (int)$this->cell('Singletons::countDatas', [$singleton->id])->render();

                $decodeFields = json_decode($singleton->fields, true);

                $addDataUrl = $this->Url->build([
                    '_name' => 'adminSingletonsData',
                    'data'  => $singleton->slug
                ]);

                $viewDataUrl = $this->Url->build([
                    '_name' => 'adminSingletonsViewData',
                    'data'  => $singleton->slug
                ]);
                
                $editUrl = $this->Url->build([
                    '_name' => 'adminSingletonsEdit',
                    'id'    => $singleton->id
                ]);
        ?>
        <li class="uk-width-1-1 uk-margin-remove-top" style="position: relative">
            <div class="uk-card uk-card-default uk-card-small uk-card-body">
                <?= $singleton->name ?> <span class="uk-text-muted uk-text-italic uk-text-small uk-margin-left"><?= $this->Purple->plural(count($decodeFields), ' field') ?> <?= $countDatas > 0 ? ' and ' . $this->Purple->plural($countDatas, ' data') : '' ?></span>

                <div class="uk-inline uk-align-right">
                    <?php
                        if ($status != 'deleted'):
                    ?>
                    <?php
                        if ($countDatas < 1):
                    ?>
                    <a href="<?= $addDataUrl ?>" class="uk-margin-small-right" uk-icon="icon: plus" uk-tooltip="Add"></a>
                    <?php
                        endif;
                    ?>
                    <a href="<?= $viewDataUrl ?>" class="uk-margin-small-right" uk-icon="icon: list" uk-tooltip="View"></a>
                    <a href="<?= $editUrl ?>" class="uk-margin-small-right" uk-icon="icon: pencil" uk-tooltip="Edit"></a>
                    <a href="#" class="uk-margin-small-right uk-text-danger button-delete-purple" data-purple-name="<?= $singleton->name ?>" data-purple-id="<?= $singleton->id ?>" data-purple-modal="#modal-delete-singleton" uk-icon="icon: close" uk-tooltip="Delete"></a>
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