<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Text;

class PurpleCommand extends Command
{
    public function initialize()
    {
        parent::initialize();
    }

    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser->addArguments([
            'type'  => ['help' => 'Purple command type', 'required' => true, 'choices'  => ['database', 'model', 'theme']],
            'value' => ['help' => 'Command value', 'required' => true]
        ])
        ->addOption('display', [
            'short'   => 'd',
            'help'    => 'Display model data',
            'choices' => ['query', 'table']
        ])
        ->addOption('migrate', [
            'short'   => 'm',
            'help'    => 'Migrate database',
        ])
        ->addOption('database_info', [
            'short'   => 'i',
            'help'    => 'Database information',
            'boolean' => true
        ])
        ->addOption('theme_name', [
            'short'   => 't',
            'help'    => 'Theme name (Must include "Theme after theme name, e.g. GoodTheme or BestTheme")',
        ]);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $type  = $args->getArgument('type');
        $value = $args->getArgument('value');
        if ($type == 'model') {
            if ($args->getOption('display')) {
                $availableModels = ['Admins', 'Blogs', 'BlogCategories', 'Histories'];

                if (in_array($value, $availableModels)) {
                    $this->loadModel($value);
                    $table  = $this->$value->find('all');
                    if ($table->count() > 0) {

                        if ($args->getOption('display') == 'query') {
                            $output = print_r($table, true);
                            $io->out($output);
                        }
                        elseif ($args->getOption('display') == 'table') {
                            $data = [];
                            if ($value == 'Admins') {
                                $data[] = [
                                    'Username', 
                                    'Email', 
                                    'Display Name', 
                                    'Level', 
                                    'Created', 
                                    'Last Login'
                                ];
                                foreach ($table as $newData) {
                                    $data[] = [
                                        $newData->username,
                                        $newData->email,
                                        $newData->display_name,
                                        $newData->level == 1 ? 'Administrator' : 'Editor',
                                        $newData->created,
                                        $newData->last_login
                                    ];
                                }
                            }
                            elseif ($value == 'Blogs') {
                                $table = $table->contain('BlogCategories')->contain('Admins');
                                $data[] = [
                                    'Title', 
                                    'Category', 
                                    'Created', 
                                    'Comment',
                                    'Status',
                                    'Author'
                                ];
                                foreach ($table as $newData) {
                                    $data[] = [
                                        $newData->title,
                                        $newData->blog_category->name,
                                        $newData->created,
                                        ucwords($newData->comment),
                                        $newData->status == 1 ? 'Publish' : 'Draft',
                                        $newData->admin->display_name
                                    ];
                                }
                            }
                            elseif ($value == 'BlogCategories') {
                                $table = $table->contain('Pages')->contain('Admins');
                                $data[] = [
                                    'Name',
                                    'Page',
                                    'Created',
                                    'Owner'
                                ];
                                foreach ($table as $newData) {
                                    $data[] = [
                                        $newData->name,
                                        $newData->page == NULL ? '' : $newData->page->title,
                                        $newData->created,
                                        $newData->admin->display_name
                                    ];
                                }
                            }
                            elseif ($value == 'Histories') {
                                $table = $table->contain('Admins');
                                $data[] = [
                                    'Title',
                                    'Detail',
                                    'Date',
                                    'User'
                                ];
                                foreach ($table as $newData) {
                                    $data[] = [
                                        $newData->title,
                                        trim($newData->detail),
                                        $newData->created,
                                        $newData->admin->display_name
                                    ];
                                }
                            }

                            $io->helper('Table')->output($data);
                        }
                    }
                    else {
                        $io->error('There is no data for ' . $value . ' model');
                    }
                }
                else {
                    $io->error('Available models are ' . implode(',', $availableModels));
                }
            }
        }
        elseif ($type == 'database') {
            if ($value == 'decrypt') {
                if ($args->getOption('database_info')) {
                    $file        = new File(__DIR__ . DS . '..' . DS . '..' . DS . 'config' . DS . 'database.php');
                    $resultcrypt = \Dcrypt\Aes256Gcm::decrypt($file->read(), CIPHER);
                    $io->out($resultcrypt);
                }
                else {
                    $io->error('Empty option for database type');
                }
            }
            elseif ($value == 'migrate') {
                if ($args->getOption('migrate')) {
                    $databaseInfo = trim($args->getOption('migrate'));
                    $file = new File(__DIR__ . DS . '..' . DS . '..' . DS . 'config' . DS . 'database.php');
                    $resultcrypt = \Dcrypt\Aes256Gcm::encrypt($databaseInfo, CIPHER);
	            	if ($file->write($resultcrypt)) {
                        $io->success('Database has been changed');
                    }
                    else {
                        $io->error("Can't change database information");
                    }
                }
                else {
                    $io->error('Empty option for database type and migrate value');
                }
            }
        }
        elseif ($type == 'theme') {
            if ($value == 'create') {
                $path = WWW_ROOT . 'uploads' . DS . 'themes' . DS;
                if ($args->getOption('theme_name')) {
                    $themeName = $args->getOption('theme_name');
                    if (!file_exists($path . $themeName . DS . 'detail.json')) {
                        if (strpos($themeName, 'Theme') !== false) {
                            $progress = $io->helper('Progress');
                            $progress->init([
                                'total' => 100,
                                'width' => 50,
                            ]);

                            // Folders
                            $themeFolder        = new Folder($path . $themeName, true, 0755);
                            $progress->increment(2)->draw();

                            $themeSrcFolder     = new Folder($path . $themeName . DS . 'src', true, 0755);
                            $progress->increment(2)->draw();

                            $themeSrcControllerFolder = new Folder($path . $themeName . DS . 'src' . DS . 'Controller', true, 0755);
                            $progress->increment(2)->draw();

                                $themeSrcFunctionsFolder  = new Folder($path . $themeName . DS . 'src' . DS . 'Functions', true, 0755);
                                $themeSrcTemplateFolder   = new Folder($path . $themeName . DS . 'src' . DS . 'Template', true, 0755);
                                $progress->increment(4)->draw();

                                    $themeSrcTemplateBlogsFolder   = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Blogs', true, 0755);
                                    $themeSrcTemplateCellFolder    = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Cell', true, 0755);
                                    $progress->increment(4)->draw();

                                        $themeSrcTemplateCellCategoriesFolder = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Cell' . DS . 'Categories', true, 0755);
                                        $themeSrcTemplateCellCommentsFolder   = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Cell' . DS . 'Comments', true, 0755);
                                        $themeSrcTemplateCellTagsFolder       = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Cell' . DS . 'Tags', true, 0755);
                                        $progress->increment(6)->draw();

                                    $themeSrcTemplateElementFolder = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element', true, 0755);
                                        $progress->increment(2)->draw();

                                        $themeSrcTemplateElementMetaFolder = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Meta', true, 0755);
                                        $themeSrcTemplateElementPostFolder = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post', true, 0755);
                                            $themeSrcTemplateElementPostCommentFolder = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Comment', true, 0755);
                                            $themeSrcTemplateElementPostSidebarFolder = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Sidebar', true, 0755);
                                        $progress->increment(4)->draw();
                                    $themeSrcTemplateHomeFolder    = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Home', true, 0755);
                                    $themeSrcTemplateLayoutFolder  = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Layout', true, 0755);
                                    $themeSrcTemplatePagesFolder   = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages', true, 0755);
                                    $themeSrcTemplateSearchFolder  = new Folder($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Search', true, 0755);
                                    $progress->increment(8)->draw();

                            $themeWebrootFolder = new Folder($path . $themeName . DS . 'webroot', true, 0755);
                            $progress->increment(2)->draw();

                                $themeWebrootBlocksFolder = new Folder($path . $themeName . DS . 'webroot' . DS . 'blocks', true, 0755);
                                $themeWebrootCssFolder    = new Folder($path . $themeName . DS . 'webroot' . DS . 'css', true, 0755);
                                $themeWebrootImgFolder    = new Folder($path . $themeName . DS . 'webroot' . DS . 'img', true, 0755);
                                $themeWebrootJsFolder     = new Folder($path . $themeName . DS . 'webroot' . DS . 'js', true, 0755);
                                $progress->increment(8)->draw();

                            // Files
                            $detailJsonData = [
                                'name'        => str_replace('Theme', '', $themeName),
                                'author'      => "Purple CMS",
                                'image'       => "",
                                'preview'     => "",
                                'homepage'    => [
                                    'use'      => 'default',
                                    'function' => ""
                                ],
                                'blocks'      => "",
                                'description' => "Custom theme for Purple CMS",
                                'version'     => '1.0.0'
                            ];
                            $detailJson     = json_encode($detailJsonData, JSON_PRETTY_PRINT);
                            $detailJsonFile = new File($path . $themeName . DS . 'detail.json', true, 0644);
                            $detailJsonFile->write($detailJson);
                            $progress->increment(2)->draw();

                            // src/Controller/AppController.php
                            $appControllerData  = "<?php\n\n";
                            $appControllerData .= "namespace " . $themeName . "\Controller;\n\n";
                            $appControllerData .= "use App\Controller\AppController as BaseController;\n\n";
                            $appControllerData .= "class AppController extends BaseController\n";
                            $appControllerData .= "{\n\n";
                            $appControllerData .= "}";
                            $appControllerFile  = new File($path . $themeName . DS . 'src' . DS . 'Controller' . DS . 'AppController.php', true, 0644);
                            $appControllerFile->write($appControllerData);
                            $progress->increment(2)->draw();

                            // src/Functions/ThemeFunction.php
                            $themeFunctionData  = "<?php\n\n";
                            $themeFunctionData .= "namespace " . $themeName . "\Functions;\n\n";
                            $themeFunctionData .= "use Cake\ORM\TableRegistry;\n\n";
                            $themeFunctionData .= "class ThemeFunction\n";
                            $themeFunctionData .= "{\n";
                            $themeFunctionData .= "\tprivate \$themeSlug = '" . Text::slug(strtolower($themeName)) . "';\n\n";
                            $themeFunctionData .= "\tpublic function __construct(\$webroot)\n";
                            $themeFunctionData .= "\t{\n";
                            $themeFunctionData .= "\t\t\$this->webroot = \$webroot;\n";
                            $themeFunctionData .= "\t}\n";
                            $themeFunctionData .= "\tpublic function example()\n";
                            $themeFunctionData .= "\t{\n";
                            $themeFunctionData .= "\t\treturn \"This is example of theme function.\";\n";
                            $themeFunctionData .= "\t}\n";
                            $themeFunctionData .= "}";
                            $themeFunctionFile  = new File($path . $themeName . DS . 'src' . DS . 'Functions' . DS . 'ThemeFunction.php', true, 0644);
                            $themeFunctionFile->write($themeFunctionData);
                            $progress->increment(2)->draw();

                            // src/Plugin.php
                            $pluginData  = "<?php\n\n";
                            $pluginData .= "namespace " . $themeName . ";\n\n";
                            $pluginData .= "use use Cake\Core\BasePlugin;\n\n";
                            $pluginData .= "class Plugin extends BasePlugin\n";
                            $pluginData .= "{\n";
                            $pluginData .= "}";
                            $pluginFile  = new File($path . $themeName . DS . 'src' . DS . 'Plugin.php', true, 0644);
                            $pluginFile->write($pluginData);
                            $progress->increment(2)->draw();

                            // src/Template/Blogs
                            $archivesBlogsFile        = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Blogs' . DS . 'archives.ctp', true, 0644);
                            $detailBlogsFile          = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Blogs' . DS . 'detail.ctp', true, 0644);
                            $postInCategoryBlogsFile  = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Blogs' . DS . 'posts_in_category.ctp', true, 0644);
                            $tagBlogsFile             = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Blogs' . DS . 'tag.ctp', true, 0644);
                            $ajaxSendCommentBlogsFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Blogs' . DS . 'ajax_send_comment.ctp', true, 0644);
                            $ajaxSendCommentBlogsFile->write("<?= \$json ?>");
                            $progress->increment(10)->draw();

                            // src/Template/Cell/Categories
                            $totalPostCategoriesFile  = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Cell' . DS . 'Categories' . DS . 'total_post.ctp', true, 0644);
                            $totalPostCategoriesFile->write("<?php if (\$postInCategory > 0) echo \"<span class='float-right'>(\" . \$postInCategory . \")</span>\" ?>");
                            $progress->increment(2)->draw();
                           
                            // src/Template/Cell/Categories
                            $fetchRepliesCommentsFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Cell' . DS . 'Comments' . DS . 'fetch_replies.ctp', true, 0644);
                            $progress->increment(2)->draw();

                            // src/Template/Cell/Tags
                            $totalPostsInTagTagsFile  = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Cell' . DS . 'Tags' . DS . 'total_posts_in_tag.ctp', true, 0644);
                            $totalPostsInTagTagsFile->write("<?= \$totalPosts ?>");
                            $progress->increment(2)->draw();

                            // src/Template/Element/Meta
                            $openGraphMetaFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Meta' . DS . 'open_graph.ctp', true, 0644);
                            $openGraphMetaFile->write("<?php\r\n    if (\$logo == '') {\r\n        if (\$metaImage != '') {\r\n            \$metaImage = \$this->request->host().\$this->request->getAttribute(\"webroot\").'uploads/images/original/' . \$metaImage;\r\n        }\r\n        else {\r\n            \$metaImage = '';\r\n        }\r\n    }\r\n    else {\r\n        \$metaImage = \$this->request->host().\$this->request->getAttribute(\"webroot\").'uploads/images/original/' . \$logo;\r\n    }\r\n\r\n    // Meta og:locale\r\n    echo \$this->Html->meta(\r\n        'og:locale',\r\n        'en_US'\r\n    );\r\n\r\n    // Meta og:title\r\n    echo \$this->Html->meta(\r\n        'og:title',\r\n        \$this->element('head_title')\r\n    );\r\n\r\n    // Meta og:type\r\n    echo \$this->Html->meta(\r\n        'og:type',\r\n        \$metaOgType\r\n    );\r\n\r\n    // Meta og:image\r\n    echo \$this->Html->meta(\r\n        'og:image',\r\n        \$metaImage\r\n    );\r\n\r\n    // Meta og:video\r\n    echo \$this->Html->meta(\r\n        'og:video',\r\n        ''\r\n    );\r\n\r\n    // Meta og:url\r\n    echo \$this->Html->meta(\r\n        'og:url',\r\n        \$this->Url->build(\$this->request->getRequestTarget(), true)\r\n    );\r\n\r\n    // Meta og:description\r\n    echo \$this->Html->meta(\r\n        'og:description',\r\n        \$metaDescription\r\n    );\r\n\r\n    // Meta og:site_name\r\n    echo \$this->Html->meta(\r\n        'og:site_name',\r\n        \$siteName\r\n    );\r\n?>");
                            $twitterMetaFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Meta' . DS . 'twitter.ctp', true, 0644);
                            $twitterMetaFile->write("<?php\r\n    if (\$logo == '') {\r\n        if (\$metaImage != '') {\r\n            \$metaImage = \$this->request->host().\$this->request->getAttribute(\"webroot\").'uploads/images/original/' . \$metaImage;\r\n        }\r\n        else {\r\n            \$metaImage = '';\r\n        }\r\n    }\r\n    else {\r\n        \$metaImage = \$this->request->host().\$this->request->getAttribute(\"webroot\").'uploads/images/original/' . \$logo;\r\n    }\r\n\r\n    // Meta twitter:card\r\n    echo \$this->Html->meta(\r\n        'twitter:card',\r\n        'summary'\r\n    );\r\n\r\n    // Meta twitter:url\r\n    echo \$this->Html->meta(\r\n        'twitter:url',\r\n        \$this->Url->build(\$this->request->getRequestTarget(), true)\r\n    );\r\n\r\n    // Meta twitter:title\r\n    echo \$this->Html->meta(\r\n        'twitter:title',\r\n        \$this->element('head_title')\r\n    );\r\n\r\n    // Meta twitter:description\r\n    echo \$this->Html->meta(\r\n        'twitter:description',\r\n        \$metaDescription\r\n    );\r\n\r\n    // Meta twitter:image\r\n    echo \$this->Html->meta(\r\n        'twitter:image',\r\n        \$metaImage\r\n    );\r\n?>");
                            $headTitleElementFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'head_title.ctp', true, 0644);
                            $headTitleElementFile->write("<?php if(isset(\$pageTitle)) { if (\$childPage) echo \$parentPageTitle.' - '; echo \$pageTitle; } ?> - <?= \$siteName ?><?php if(!empty(\$tagLine)) echo ' | '.\$tagLine ?>");
                            $scriptElementFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'script.ctp', true, 0644);
                            $scriptElementFile->write("<!-- UI Kit -->\r\n<?= \$this->Html->script('/master-assets/plugins/uikit/js/uikit.js'); ?>\r\n<?= \$this->Html->script('/master-assets/plugins/uikit/js/uikit-icons.js'); ?>\r\n<!-- Parsley -->\r\n<?= \$this->Html->script('/master-assets/plugins/parsley/dist/parsley.js'); ?>\r\n<!-- Initial -->\r\n<?= \$this->Html->script('/master-assets/plugins/initial/initial.min.js'); ?>\r\n<!-- Livestamp -->\r\n<?= \$this->Html->script('/master-assets/plugins/livestamp/livestamp.min.js'); ?>\r\n<!-- Purple -->\r\n<?= \$this->Html->script('/master-assets/js/ajax-front-end.js'); ?>\r\n<?= \$this->Html->script('/master-assets/js/purple-front-end.js'); ?>\r\n<script type=\"text/javascript\">\r\n\t\$(document).ready(function(){\r\n\t\tvar urlActionContactForm = '<?= \$this->Url->build(['_name' => 'ajaxSendContact']) ?>';\r\n\t\tvar urlActionCommentForm = '<?= \$this->Url->build(['_name' => 'ajaxSendComment']) ?>';\r\n\t\t \r\n\t\t\$('body').find('.form-send-contact').attr('action', urlActionContactForm);\r\n\t\t\$('body').find('.form-send-contact').prepend('<input type=\"hidden\" name=\"token\" value=<?= json_encode(\$this->request->getParam('_csrfToken')); ?>>');\r\n\t\t\$('body').find('.form-send-contact').prepend('<input type=\"hidden\" name=\"ds\" value=<?= \$this->Url->build(['_name' => 'adminMessages']); ?>>');\r\n\t\t\$('body').find('.form-send-comment').attr('action', urlActionCommentForm);\r\n\r\n\t\t<?php if (\$formSecurity == 'on'): ?>\r\n    \t\$('body').find('.form-send-contact').prepend('<input type=\"hidden\" name=\"status\" value=\"\">');\r\n    \t\$('body').find('.form-send-contact').prepend('<input type=\"hidden\" name=\"score\" value=\"\">');\r\n\t    if (\$('.form-send-contact').length > 0) {\r\n\t\t    grecaptcha.ready(function() {\r\n\t\t        grecaptcha.execute('<?= \$recaptchaSitekey ?>', {action: 'ajaxVerifyForm'}).then(function(token) {\r\n\t\t            fetch('<?= \$this->Url->build(['_name' => 'ajaxVerifyForm', 'action' => 'ajaxVerifyForm', 'token' => '']); ?>' + token).then(function(response) {\r\n\t\t                response.json().then(function(data) {\r\n\t\t                    var json    = \$.parseJSON(data),\r\n\t\t                        success = (json.success),\r\n\t\t                        score   = (json.score);\r\n\r\n\t\t                    if (success == true) {\r\n\t\t                       \$('.form-send-contact').find('input[name=status]').val('success'); \r\n\t\t                       \$('.form-send-contact').find('input[name=score]').val(score); \r\n\t\t                    }\r\n\t\t                    else {\r\n\t\t                       \$('.form-send-contact').find('input[name=status]').val('failed'); \r\n\t\t                       \$('.form-send-contact').find('input[name=score]').val('0'); \r\n\t\t                    }\r\n\t\t                });\r\n\t\t            });\r\n\t\t        });\r\n\t\t    });\r\n\t    }\r\n\t    <?php endif; ?>\r\n\r\n\t\t\$('.initial-photo').initial(); \r\n\t})\r\n</script>\r\n\r\n<!-- Google Analytics Code -->\r\n<?php echo \$googleAnalytics != '' ? \$googleAnalytics : ''; ?>");
                            $headElementFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'head.ctp', true, 0644);
                            $headElementFile->write("<head>\r\n    <?= \$this->Html->charset(); ?>\r\n    <title><?= \$this->element('head_title') ?></title>\r\n    <?php \r\n        // Meta Viewport\r\n        echo \$this->Html->meta(\r\n            'viewport',\r\n            'width=device-width, initial-scale=1'\r\n        );\r\n\r\n        // Meta Author\r\n        echo \$this->Html->meta(\r\n            'author',\r\n            \$siteName\r\n        );\r\n\r\n        // Meta Keywords\r\n        echo \$this->Html->meta(\r\n            'keywords',\r\n            \$metaKeywords\r\n        );\r\n\r\n        // Meta Description\r\n        echo \$this->Html->meta(\r\n            'description',\r\n            \$metaDescription\r\n        );\r\n        \r\n        // Meta Open Graph\r\n        echo \$this->element('Meta/open_graph');\r\n\r\n        // Meta Twitter\r\n        echo \$this->element('Meta/twitter') \r\n    ?>\r\n\r\n    <link rel=\"canonical\" href=\"<?= \$this->Url->build(\$this->request->getRequestTarget(), true) ?>\">\r\n    \r\n    <!-- Required Theme Assets -->\r\n\r\n    <!-- Froala Blocks -->\r\n    <?= \$this->Html->css('/master-assets/plugins/froala-blocks/css/froala_blocks.css') ?>\r\n    <!-- UI Kit -->\r\n    <?= \$this->Html->css('/master-assets/plugins/uikit/css/uikit.css') ?>\r\n    <!-- Parsley -->\r\n    <?= \$this->Html->css('/master-assets/plugins/parsley/src/parsley.css') ?>\r\n\r\n    <?= \$this->Html->css('/master-assets/css/bttn.css') ?>\r\n    <?= \$this->Html->css('custom.css') ?>\r\n\r\n    <?php if (\$favicon != ''): ?>\r\n    <!-- Favicon -->\r\n    <link rel=\"icon\" href=\"<?= \$this->request->getAttribute(\"webroot\").'uploads/images/original/' . \$favicon ?>\">\r\n    <?php else: ?>\r\n    <!-- Favicon -->\r\n    <link rel=\"icon\" href=\"<?= \$this->request->getAttribute(\"webroot\").'master-assets/img/favicon.png' ?>\">\r\n    <?php endif; ?>\r\n\r\n    <?php if (\$formSecurity == 'on'): ?>\r\n    <!-- Google reCaptcha -->\r\n    <?= \$this->Html->script('https://www.google.com/recaptcha/api.js?render='.\$recaptchaSitekey); ?>\r\n    <?php endif; ?>\r\n\r\n    <!-- Schema.org ld+json -->\r\n    <?php\r\n        if (\$this->request->getParam('action') == 'home') {\r\n            echo html_entity_decode(\$ldJsonWebsite);\r\n            echo html_entity_decode(\$ldJsonOrganization);\r\n        }\r\n\r\n        // WebPage\r\n        if (isset(\$webpageSchema)) {\r\n            echo html_entity_decode(\$webpageSchema);\r\n        }\r\n        \r\n        // BreadcrumbList\r\n        if (isset(\$breadcrumbSchema)) {\r\n            echo html_entity_decode(\$breadcrumbSchema);\r\n        }\r\n\r\n        // Article\r\n        if (isset(\$articleSchema)) {\r\n            echo html_entity_decode(\$articleSchema);\r\n        }\r\n    ?>\r\n\r\n    <!-- Purple Timezone -->\r\n    <?= \$this->Html->script('/master-assets/plugins/moment/moment.js'); ?>\r\n    <?= \$this->Html->script('/master-assets/plugins/moment-timezone/moment-timezone.js'); ?>\r\n    <?= \$this->Html->script('/master-assets/plugins/moment-timezone/moment-timezone-with-data.js'); ?>\r\n    <?= \$this->Html->script('/master-assets/js/purple-timezone.js'); ?>\r\n    <script type=\"text/javascript\">\r\n        \$(document).ready(function(){\r\n            clientTimezone('<?= \$timeZone ?>');\r\n        })\r\n    </script>\r\n\r\n    <script type=\"text/javascript\">\r\n        var cakeDebug    = \"<?= \$cakeDebug ?>\",\r\n            formSecurity = \"<?= \$formSecurity ?>\";\r\n        window.cakeDebug    = cakeDebug;\r\n        window.formSecurity = formSecurity;\r\n    </script>\r\n</head>");
                            
                            // src/Template/Element/Post
                            $tagsElementPostFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'tags.ctp', true, 0644);
                            
                            // src/Template/Element/Post/Comment
                            $commentFormElementPostCommentFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Comment' . DS . 'comment_form.ctp', true, 0644);
                            $commentsElementPostCommentFile    = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Comment' . DS . 'comments.ctp', true, 0644);
                            
                            // src/Template/Element/Post/Sidebar
                            $aboutFormElementPostSidebarFile    = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Sidebar' . DS . 'about.ctp', true, 0644);
                            $archivesFormElementPostSidebarFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Sidebar' . DS . 'archives.ctp', true, 0644);
                            $categoryFormElementPostSidebarFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Sidebar' . DS . 'category.ctp', true, 0644);
                            $searchFormElementPostSidebarFile   = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Sidebar' . DS . 'search.ctp', true, 0644);
                            $commentFormElementPostSidebarFile  = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'Post' . DS . 'Sidebar' . DS . 'tags.ctp', true, 0644);

                            $progress->increment(10)->draw();
                            
                            // src/Template/Home
                            $indexHomeFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Home' . DS . 'index.ctp', true, 0644);
                            $progress->increment(2)->draw();

                            // src/Template/Layout
                            $defaultLayoutFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Layout' . DS . 'default.ctp', true, 0644);
                            $progress->increment(2)->draw();

                            // src/Template/Pages
                            $ajaxSendContactPagesFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages' . DS . 'ajax_send_contact.ctp', true, 0644);
                            $ajaxSendContactPagesFile->write("<?= \$json ?>");
                            $ajaxVerifyFormPagesFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages' . DS . 'ajax_verify_form.ctp', true, 0644);
                            $ajaxVerifyFormPagesFile->write("<?= \$json ?>");
                            $blogPagesFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages' . DS . 'blog.ctp', true, 0644);
                            $codePagesFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages' . DS . 'code.ctp', true, 0644);
                            $generalPagesFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages' . DS . 'general.ctp', true, 0644);
                            $homePagesFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages' . DS . 'home.ctp', true, 0644);
                            $indexPagesFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Pages' . DS . 'index.ctp', true, 0644);

                            // src/Template/Pages
                            $indexSearchFile = new File($path . $themeName . DS . 'src' . DS . 'Template' . DS . 'Search' . DS . 'index.ctp', true, 0644);

                            $progress->increment(16)->draw();
                            $progress->increment(2)->draw();

                            $io->success(' Theme files has been created');
                        }
                        else {
                            $io->error('Theme name doesn\'t include "Theme"');
                        }
                    }
                    else {
                        $io->error('Theme folder is already exist');
                    }
                }
                else {
                    $io->error('Empty option for theme type and migrate value');
                }
            }
            else {
                $io->error('Empty value for theme type. Available value is create');
            }
        }
    }
}