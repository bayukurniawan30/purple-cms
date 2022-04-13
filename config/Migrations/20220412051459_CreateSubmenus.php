<?php
use Migrations\AbstractMigration;

class CreateSubmenus extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('submenus');
        $table->addColumn('title', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('status', 'string', [
            'default' => null,
            'limit' => 1,
            'null' => false,
        ]);
        $table->addColumn('ordering', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => null,
        ]);
        $table->addColumn('target', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('menu_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('page_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('admin_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addIndex(['menu_id']);
        $table->addForeignKey('menu_id', 'menus', 'id');
        $table->addIndex(['page_id']);
        $table->addForeignKey('page_id', 'pages', 'id');
        $table->addIndex(['admin_id']);
        $table->addForeignKey('admin_id', 'admins', 'id');
        $table->create();
    }
}
