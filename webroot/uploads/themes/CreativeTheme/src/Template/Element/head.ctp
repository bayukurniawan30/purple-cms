<head>
    <?= $this->Html->charset(); ?>
    <title><?= $this->element('head_title') ?></title>
    <?php 
        // Meta Viewport
        echo $this->Html->meta(
            'viewport',
            'width=device-width, initial-scale=1'
        );

        // Meta Author
        echo $this->Html->meta(
            'author',
            $siteName
        );

        // Meta Keywords
        echo $this->Html->meta(
            'keywords',
            $metaKeywords
        );

        // Meta Description
        echo $this->Html->meta(
            'description',
            $metaDescription
        );
        
        // Meta Open Graph
        echo $this->element('Meta/open_graph');

        // Meta Twitter
        echo $this->element('Meta/twitter') 
    ?>

    <link rel="canonical" href="<?= $this->Url->build($this->request->getRequestTarget(), true) ?>">
    
    <!-- Froala Blocks -->
    <?= $this->Html->css('/master-assets/plugins/froala-blocks/css/froala_blocks.css') ?>
    <!-- UI Kit -->
    <?= $this->Html->css('/master-assets/plugins/uikit/css/uikit.css') ?>
    <!-- Parsley -->
    <?= $this->Html->css('/master-assets/plugins/parsley/src/parsley.css') ?>
    <!-- Bttn -->
    <?= $this->Html->css('/master-assets/css/bttn.css') ?>

    <!-- Bootstrap CSS-->
    <?= $this->Html->css('/vendor/bootstrap/css/bootstrap.min.css') ?>
    <!-- Font Awesome CSS-->
    <?= $this->Html->css('/vendor/font-awesome/css/font-awesome.min.css') ?>
    <!-- Google fonts - Roboto-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,300,700,400italic">
    <!-- owl carousel-->
    <?= $this->Html->css('/vendor/owl.carousel/assets/owl.carousel.css') ?>
    <?= $this->Html->css('/vendor/owl.carousel/assets/owl.theme.default.css') ?>
    <!-- theme stylesheet-->
    <?= $this->Html->css('style.default.css') ?>
    <!-- Custom stylesheet - for your changes-->
    <?= $this->Html->css('custom.css') ?>
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->

    <?php if ($favicon != ''): ?>
    <!-- Favicon -->
    <link rel="icon" href="<?= $this->request->getAttribute("webroot").'uploads/images/original/' . $favicon ?>">
    <?php else: ?>
    <!-- Favicon -->
    <link rel="icon" href="<?= $this->request->getAttribute("webroot").'master-assets/img/favicon.png' ?>">
    <?php endif; ?>

    <!-- jQuery -->
    <?= $this->Html->script('/vendor/jquery/jquery.min.js'); ?>

    <?php if ($formSecurity == 'on'): ?>
    <!-- Google reCaptcha -->
    <?= $this->Html->script('https://www.google.com/recaptcha/api.js?render='.$recaptchaSitekey); ?>
    <?php endif; ?>

    <!-- Schema.org ld+json -->
    <?php
        if ($this->request->getParam('action') == 'home') {
            echo html_entity_decode($ldJsonWebsite);
            echo html_entity_decode($ldJsonOrganization);
        }

        // WebPage
        if (isset($webpageSchema)) {
            echo html_entity_decode($webpageSchema);
        }
        
        // BreadcrumbList
        if (isset($breadcrumbSchema)) {
            echo html_entity_decode($breadcrumbSchema);
        }

        // Article
        if (isset($articleSchema)) {
            echo html_entity_decode($articleSchema);
        }
    ?>

    <!-- Purple Timezone -->
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
        var cakeDebug    = "<?= $cakeDebug ?>",
            formSecurity = "<?= $formSecurity ?>";
        window.cakeDebug    = cakeDebug;
        window.formSecurity = formSecurity;
    </script>
</head>