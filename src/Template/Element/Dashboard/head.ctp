<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?></title>
    <!-- plugins:css -->
    <?= $this->Html->css('/master-assets/plugins/iconfonts/mdi/css/materialdesignicons.min.css') ?>
    <?= $this->Html->css('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css') ?>
    <?= $this->Html->css('/master-assets/css/vendor.bundle.base.css') ?>
    <?= $this->Html->css('/master-assets/plugins/toast/jquery.toast.min.css') ?>
    <?= $this->Html->css('/master-assets/plugins/parsley/src/parsley.css') ?>
    <?php if ($this->request->getParam('controller') == 'Medias' || $this->request->getParam('controller') == 'Appearance'): ?>
        <?= $this->Html->css('/master-assets/plugins/dmuploader/css/jquery.dm-uploader.css') ?>
        <?= $this->Html->css('/master-assets/plugins/datatables/datatables.min.css') ?>
        <?= $this->Html->css('/master-assets/plugins/datatables/Responsive-2.2.3/css/responsive.dataTables.min.css') ?>
    <?php endif; ?>
    <?php if ($this->request->getParam('controller') == 'Appearance' || ($this->request->getParam('controller') == 'Admins' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit'))): ?>
        <?= $this->Html->css('/master-assets/plugins/croppie/croppie.css') ?>
    <?php endif; ?>
    <?php if (($this->request->getParam('controller') == 'Admins' && $this->request->getParam('action') == 'index') || $this->request->getParam('controller') == 'Pages' || $this->request->getParam('controller') == 'Blogs' || $this->request->getParam('controller') == 'Subscribers'): ?>
        <?= $this->Html->css('/master-assets/plugins/datatables/datatables.min.css') ?>
        <?= $this->Html->css('/master-assets/plugins/datatables/Responsive-2.2.3/css/responsive.dataTables.min.css') ?>
    <?php endif; ?>
    <?php if (($this->request->getParam('controller') == 'Admins' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit')) || ($this->request->getParam('controller') == 'Blogs' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit')) || $this->request->getParam('controller') == 'Themes'): ?>
        <?= $this->Html->css('/master-assets/plugins/dmuploader/css/jquery.dm-uploader.css') ?>
    <?php endif; ?>
    <?php if ($this->request->getParam('controller') == 'Pages'): ?>
        <?= $this->Html->css('/master-assets/plugins/leaflet/leaflet.css') ?>
        <?php if ($this->request->getParam('type') == 'general'): ?>
            <?= $this->Html->css('/master-assets/plugins/jstree/themes/default/style.css', ['id' => 'tree-css-path']) ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($this->request->getParam('controller') == 'Pages' || ($this->request->getParam('controller') == 'Blogs' && ($this->request->getParam('action') == 'add' || $this->request->getParam('action') == 'edit')) || ($this->request->getParam('controller') == 'Settings' && $this->request->getParam('action') == 'seo')): ?>
        <?= $this->Html->css('/master-assets/plugins/froala-editor/css/froala_editor.pkgd.min.css') ?>
        <?= $this->Html->css('/master-assets/plugins/froala-editor/css/froala_style.min.css') ?>
        <?= $this->Html->css('/master-assets/plugins/froala-editor/css/themes/royal.min.css') ?>
        <?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css') ?>
        <?= $this->Html->css('/master-assets/plugins/froala-editor/css/plugins/code_view.min.css') ?>
        <?= $this->Html->css('/master-assets/plugins/froala-editor/css/third_party/font_awesome.min.css') ?>
        <?= $this->Html->css('https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.css') ?>
        <?= $this->Html->css('/master-assets/plugins/froala-editor/css/third_party/image_tui.min.css') ?>
        <?= $this->Html->css('/master-assets/plugins/tag-editor/jquery.tag-editor.css') ?>
    <?php endif; ?>
    <?php if ($this->request->getParam('controller') == 'Pages' && $this->request->getParam('action') == 'detail'): ?>
        <?= $this->Html->css('/master-assets/plugins/froala-blocks/css/froala_blocks.css') ?>
        <?= $this->Html->css('https://fonts.googleapis.com/css?family=Quicksand:300,400,700') ?>
        <?= $this->Html->css('/master-assets/css/bttn.css') ?>
    <?php endif; ?>
    <?php if ($this->request->getParam('controller') == 'Socials'): ?>
        <?= $this->Html->css('/master-assets/plugins/jssocials/css/jssocials.css') ?>
    <?php endif; ?>
    <?php if ($this->request->getParam('controller') == 'Settings' && $this->request->getParam('action') == 'seo'): ?>
        <?= $this->Html->css('/master-assets/plugins/prism/prism.css') ?>
    <?php endif; ?>
    <!-- endinject -->

    <!-- Purple CMS Plugins Script -->
    <?php
        foreach ($plugins as $plugin):
            if ($plugin['yes'] == true) {
                $pluginNamespace = $plugin['namespace'];
                $pluginAssets    = $plugin['dashboard_assets'];
                
                foreach ($pluginAssets as $asset):
                    $assetType  = $asset['type'];
                    $assetValue = $asset['value'];

                    if ($assetType == 'head' && $assetValue == 'yes') {
                        $assetElement = $asset['name'];
                        echo $this->element($pluginNamespace . '.' . $assetElement);
                    }
                endforeach;
            }
        endforeach;
    ?>

    <!-- inject:css -->
    <?= $this->Html->css('/master-assets/css/style.css') ?>
    <?= $this->Html->css('/master-assets/plugins/uikit/css/uikit.css') ?>
    <?= $this->Html->css('/master-assets/css/custom.css') ?>
    <?= $this->Html->css('/master-assets/css/blocks.css') ?>
    <!-- endinject -->

    <!-- Favicon -->
    <link rel="icon" href="<?= $this->request->getAttribute("webroot").'master-assets/img/favicon.png' ?>">

    <?= $this->Html->script('/master-assets/js/vendor.bundle.base.js'); ?>
    <?= $this->Html->script('/master-assets/js/notification.js'); ?>
    <?= $this->Html->script('/master-assets/js/button.js'); ?>
    <?= $this->Html->script('/master-assets/js/ajax.js'); ?>
    <?php if ($this->request->getParam('controller') == 'Pages' && $this->request->getParam('type') == 'general'): ?>
    <?= $this->Html->script('/master-assets/plugins/jstree/jstree.min.js', ['id' => 'tree-js-path']); ?>
    <?= $this->Html->script('/master-assets/plugins/html2json/htmlparser.js'); ?>
    <?= $this->Html->script('/master-assets/plugins/html2json/html2json.js'); ?>
    <?php endif; ?>

    <!-- Purple Timezone Check -->
    <?= $this->Html->script('/master-assets/plugins/moment/moment.js'); ?>
    <?= $this->Html->script('/master-assets/plugins/moment-timezone/moment-timezone.js'); ?>
    <?= $this->Html->script('/master-assets/plugins/moment-timezone/moment-timezone-with-data.js'); ?>
    <?= $this->Html->script('/master-assets/js/purple-timezone.js'); ?>
    <script type="text/javascript">
        $(document).ready(function(){
            clientTimezone('<?= $timeZone ?>');
        })
    </script>

    <script type="text/javascript">
        var cakeDebug = "<?= $cakeDebug ?>";
        window.cakeDebug = cakeDebug;
    </script>

    <?php if ($appearanceFavicon->value != ''): ?>
    <link rel="shortcut icon" href="<?= $this->request->getAttribute("webroot") . 'uploads/images/original/' . $appearanceFavicon->value ?>" />
    <?php endif; ?>
</head>