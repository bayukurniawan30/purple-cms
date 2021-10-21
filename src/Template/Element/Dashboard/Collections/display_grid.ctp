<div class="col-md-12">
    <div class="uk-child-width-1-4@m uk-grid-match" uk-grid="masonry: false">
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
        <div>
            <div class="uk-card uk-card-default">
                <div class="uk-card-media-top uk-inline">
                    <img class="" src="<?php echo $this->request->getAttribute("webroot") . 'master-assets/img/default-collection-image.png' ?>" alt="" width="100%">

                    <?php
                        if ($status != 'deleted'):
                    ?>
                    <div class="uk-position-small uk-position-top-right">
                        <button type="button" uk-close uk-tooltip="Delete <?= $collection->name ?>" class="button-delete-purple" data-purple-name="<?= $collection->name ?>" data-purple-id="<?= $collection->id ?>" data-purple-modal="#modal-delete-collection"></button>
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
                    <div class="uk-child-width-1-3" uk-grid="">
                        <div class="uk-text-center">
                            <a href="<?= $addDataUrl ?>" class="uk-button uk-button-text">Add</a>
                        </div>

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