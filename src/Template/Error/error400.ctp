<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'purple_error';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?= $this->element('auto_table_warning') ?>
<?php
if (extension_loaded('xdebug')) :
    xdebug_print_function_stack();
endif;

$this->end();
endif;
?>

<?php
    if (Configure::read('debug')) :
?>
<h2><?= h($message) ?></h2>
<p class="error">
    <strong><?= __d('cake', 'Error') ?>: </strong>
    <?= __d('cake', 'The requested address {0} was not found on this server.', "<strong>'{$url}'</strong>") ?>
</p>
<?php
    else:
?>
<div class="col-lg-6 text-lg-right pr-lg-4">
    <h1 class="display-1 mb-0">404</h1>
</div>
<div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
    <h2>SORRY!</h2>
    <h3 class="font-weight-light"><?= __d('cake', 'The requested address {0} was not found on this server.', "<strong>'{$url}'</strong>") ?></h3>
</div>
<?php
    endif;
?>
