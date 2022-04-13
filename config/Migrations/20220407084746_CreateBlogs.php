<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateBlogs extends AbstractMigration
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
        $table = $this->table('blogs');
        $table->addColumn('title', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('content', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_MEDIUM,
            'null' => false,
        ]);
        $table->addColumn('slug', 'string', [
            'default' => null,
            'limit' => 191,
            'null' => false,
        ]);
        $table->addColumn('blog_type_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('blog_category_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('comment', 'string', [
            'default' => null,
            'limit' => 3,
            'null' => false,
        ]);
        $table->addColumn('featured', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('selected', 'string', [
            'default' => null,
            'limit' => 3,
            'null' => true,
        ]);
        $table->addColumn('meta_keywords', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('meta_description', 'text', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('status', 'string', [
            'default' => null,
            'limit' => 10,
            'null' => false,
        ]);
        $table->addColumn('social_share', 'string', [
            'default' => null,
            'limit' => 10,
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
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex([
            'slug',
        ], [
            'name' => 'SLUG_INDEX',
            'unique' => true,
        ]);
        $table->addIndex(['blog_type_id']);
        $table->addForeignKey('blog_type_id', 'blog_types', 'id');
        $table->addIndex(['blog_category_id']);
        $table->addForeignKey('blog_category_id', 'blog_categories', 'id');
        $table->addIndex(['admin_id']);
        $table->addForeignKey('admin_id', 'admins', 'id');
        $table->create();
    }
}
