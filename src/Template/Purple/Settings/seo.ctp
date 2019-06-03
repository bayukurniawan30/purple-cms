<!--CSRF Token-->
<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Meta Tags</h4>
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
                                <td>Meta Keywords</td>
                                <td><?= $settingMetaKeywords->value == '' ? 'None' : $settingMetaKeywords->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Meta Keywords" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingMetaKeywords->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="seo" uk-tooltip="Change Meta Keywords"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Meta Description</td>
                                <td><?= $settingMetaDescription->value == '' ? 'None' : $settingMetaDescription->value ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Meta Keywords" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingMetaDescription->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="seo" uk-tooltip="Change Meta Keywords"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Google Analytics Code</h4>
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
                                <td>Google Analytics Code</td>
                                <td><?= $settingGoogleAnalytics->value == '' ? 'None' : 'Click Change to view code' ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Google Analytics Code" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingGoogleAnalytics->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="seo" uk-tooltip="Change Google Analytics Code"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Schema.org ld+json</h4>
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
                                <td>Schema.org ld+json</td>
                                <td><?= $settingLdJson->value == '' ? 'None' : ucwords($settingLdJson->value) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw button-link-to-modal-setting" data-purple-title="Schema.org ld+json" data-purple-target="#modal-edit-settings" data-purple-id="<?= $settingLdJson->id ?>" data-purple-url="<?= $this->Url->build(["controller" => "Settings", "action" => "ajaxFormStandardSetting"]); ?>" data-purple-redirect="seo" uk-tooltip="Change Schema.org ld+json"><i class="mdi mdi-pencil"></i> Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php
                    if ($settingLdJson->value == 'enable'):
                ?>
                <hr>

                <ul class="jsonld-accordion" uk-accordion="multiple: true">
                    <li class="uk-open">
                        <a class="uk-accordion-title" href="#">WebSite</a>
                        <div class="uk-accordion-content">
                            <pre class="language-html"><code><?= $ldJsonWebsite ?></code></pre>
                        </div>
                    </li>
                    <li>
                        <a class="uk-accordion-title" href="#">Organization</a>
                        <div class="uk-accordion-content">
                            <div class="uk-accordion-content">
                                <pre class="language-html"><code><?= $ldJsonOrganization ?></code></pre>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="uk-accordion-title" href="#">BreadcrumbList</a>
                        <div class="uk-accordion-content">
                            <p>This is a dynamic generated ld+json and different each page.</p>
                        </div>
                    </li>
                    <li>
                        <a class="uk-accordion-title" href="#">Article</a>
                        <div class="uk-accordion-content">
                            <p>This is a dynamic generated ld+json and different each blog post.</p>
                        </div>
                    </li>
                </ul>
                <?php
                    endif;
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title uk-margin-remove-bottom">Sitemap.xml and Robots.txt</h4>
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
                                <td>Sitemap.xml</td>
                                <td>Dynamically Generated</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw" onclick="window.open('<?= $this->Url->build(["_name" => "websiteSitemap", "_ext" => 'xml']); ?>')"" uk-tooltip="Open sitemap.xml"><i class="mdi mdi-open-in-new"></i> Open</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Robots.txt</td>
                                <td>Dynamically Generated</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm btn-fw" onclick="window.open('<?= $this->Url->build(["_name" => "websiteRobots", "_ext" => 'txt']); ?>')"" uk-tooltip="Open robots.txt"><i class="mdi mdi-open-in-new"></i> Open</button>
                                </td>
                            </tr>
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