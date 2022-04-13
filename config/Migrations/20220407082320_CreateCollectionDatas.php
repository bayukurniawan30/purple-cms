<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateCollectionDatas extends AbstractMigration
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
        $table = $this->table('collection_datas');
        $table->addColumn('content', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => false,
        ]);
        $table->addColumn('slug', 'string', [
            'default' => null,
            'limit' => 191,
            'null' => true,
        ]);
        $table->addColumn('slug_target', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => true,
        ]);
        $table->addColumn('collection_id', 'integer', [
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
        $table->addIndex([
            'slug',
        ], [
            'name' => 'SLUG_INDEX',
            'unique' => true,
        ]);
        $table->addIndex(['collection_id']);
        $table->addForeignKey('collection_id', 'collections', 'id');
        $table->addIndex(['admin_id']);
        $table->addForeignKey('admin_id', 'admins', 'id');
        $table->create();
    }
}
