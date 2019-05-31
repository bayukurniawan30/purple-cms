<?php
    foreach ($childPages as $child):
        $editUrl = $this->Url->build([
            'controller' => $this->request->getParam('controller'),
            'action'     => 'detail',
            'type'       => $child->page_template->type,
            'id'         => $child->id,
            'slug'       => $child->slug
        ]);
        $openUrl = $this->Url->build([
            '_name' => 'specificPageChild',
            'page'  => $page->slug,
            'child' => $child->slug
        ]);
        
        $this->Url->build('/'.$page->slug.'/'.$child->slug, true);
        if ($child->page_template->type == 'general') {
            $icon = 'move';
        }
        elseif ($child->page_template->type == 'blog') {
            $icon = 'pencil';
        }
        elseif ($child->page_template->type == 'custom') {
            $icon = 'code';
        }
?>
<li class="uk-width-1-1 <?php if ($this->request->getQuery('u') == $child->slug) echo 'uk-animation-shake' ?>">
    <div class="uk-card uk-card-default uk-card-small uk-card-body child-page-list">
        <span class="uk-margin-small-right" uk-icon="icon: <?= $icon ?>"></span><?= $child->title ?>
        <div class="uk-inline uk-align-right">
            <a href="#" class="uk-margin-small-right" uk-icon="icon: cog" uk-tooltip="<?= $child->page_template->name ?>"></a>
            <a href="#" class="uk-margin-small-right <?php if ($child->status == '0') echo 'text-secondary' ?>" uk-icon="icon: world" uk-tooltip="<?= $child->text_status ?>"></a>
            <a href="#" class="uk-margin-small-right" uk-icon="icon: user" uk-tooltip="<?= $child->admin->display_name ?>"></a>
            <button class="uk-button uk-button-link"><span uk-icon="more-vertical"></span></button>
            <div uk-dropdown="mode: click; pos: bottom-right">
                <ul class="uk-nav uk-dropdown-nav non-uikit">
                    <li><a href="<?= $openUrl ?>" target="_blank">Open</a></li>
                    <li><a href="<?= $editUrl ?>">Edit</a></li>
                    <li><a href="#" class="button-change-page-status" data-purple-id="<?= $child->id ?>" data-purple-status="<?= $child->status ?>" data-purple-name="<?= $child->title ?>" data-purple-modal="#modal-change-page-status">Change Status</a></li>
                    <li><a class="button-get-permalink" href="#" data-purple-link="<?= $this->Url->build(['_name' => 'specificPageChild', 'page'  => $page->slug, 'child' => $child->slug], true); ?>" data-purple-modal="#modal-show-permalink">Get Permalink</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a class="button-delete-purple text-danger" href="#" data-purple-id="<?= $child->id ?>" data-purple-name="<?= $child->title ?>" data-purple-type="<?= $child->page_template->type ?>" data-purple-modal="#modal-delete-page">Delete</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
<?php
    endforeach;
?>