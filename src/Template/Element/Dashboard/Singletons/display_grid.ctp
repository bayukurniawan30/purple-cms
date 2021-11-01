<div class="col-md-12">
    <div class="uk-child-width-1-3@m uk-child-width-1-4@xl uk-grid-match" uk-grid="masonry: false">
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
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top uk-inline">
                    <img class="" src="<?php echo $this->request->getAttribute("webroot") . 'master-assets/img/default-singleton-image.png' ?>" alt="" width="100%">

                    <?php
                        if ($status != 'deleted'):
                    ?>
                    <div class="uk-position-small uk-position-top-right">
                        <button type="button" uk-close uk-tooltip="Delete <?= $singleton->name ?>" class="button-delete-purple" data-purple-name="<?= $singleton->name ?>" data-purple-id="<?= $singleton->id ?>" data-purple-modal="#modal-delete-singleton"></button>
                    </div>
                    <?php
                        endif;
                    ?>
                </div>
                <div class="uk-card-body">
                    <h4 class="uk-card-title uk-margin-remove"><?= $singleton->name ?></h4>
                    <ul class="list-arrow">
                        <li><?= $singleton->text_status ?></li>
                        <li>Contain <?= $this->Purple->plural(count($decodeFields), ' field') ?> <?= $countDatas > 0 ? ' and ' . $this->Purple->plural($countDatas, ' data') : '' ?></li>
                        <li><?= $singleton->admin->display_name ?></li>
                        <li>Created at <?= $this->Time->format(
                            $singleton->created,
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