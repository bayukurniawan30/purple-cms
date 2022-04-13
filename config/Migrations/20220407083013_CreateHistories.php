<?php
use Migrations\AbstractMigration;

class CreateHistories extends AbstractMigration
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
        $table = $this->table('histories');
        $table->addColumn('title', 'string', [
            'default' => null,
            'limit' => 200,
            'null' => false,
        ]);
        $table->addColumn('detail', 'text', [
            'default' => null,
            'null' => false,
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
        $table->addIndex(['admin_id']);
        $table->addForeignKey('admin_id', 'admins', 'id');
        $table->create();
    }
}
