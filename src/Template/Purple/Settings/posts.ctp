<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Posts Settings</h4>
            </div>
            <div class="card-body">
                <div class="uk-overflow-auto">
                    <table class="uk-table uk-table-justify uk-table-middle uk-table-divider">
                        <thead>
                            <?php
                                echo $this->Html->tableHeaders([
                                    'Setting Name',
                                    'Value',
                                    ['Action' => ['class' => 'uk-width-small text-center']]
                                ]);
                            ?>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Posts Limit per Page</td>
                                <td><?= $settingPostLimit->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Posts Limit per Page" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingPostLimit->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="posts" uk-tooltip="Change Posts Limit"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <!-- <tr>
                                <td>Permalink</td>
                                <td>
                                    <?php
                                        if ($settingPostPermalink->value == 'day-name') {
                                            $permalinkUrl = $this->Url->build([
                                                '_name' => 'specificPost',
                                                'year'  => date('Y'),
                                                'month' => date('m'),
                                                'date'  => date('d'),
                                                'post'  => 'sample-post',
                                            ], true);
                                        }
                                        elseif ($settingPostPermalink->value == 'month-name') {
                                            $permalinkUrl = $this->Url->build([
                                                '_name' => 'specificPostMonth',
                                                'year'  => date('Y'),
                                                'month' => date('m'),
                                                'post'  => 'sample-post',
                                            ], true);
                                        }
                                        elseif ($settingPostPermalink->value == 'post-name') {
                                            $permalinkUrl = $this->Url->build([
                                                '_name' => 'specificPostName',
                                                'post'  => 'sample-post',
                                            ], true);
                                        }
                                        
                                        echo $permalinkUrl;
                                    ?>        
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Permalink" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingPostPermalink->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="posts" uk-tooltip="Change Permalink"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-edit-settings" class="uk-flex-top purple-modal" uk-modal>
    <div id="load-edit-settings" class="uk-modal-dialog uk-margin-auto-vertical">
    </div>
</div>