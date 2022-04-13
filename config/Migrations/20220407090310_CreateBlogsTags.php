<?php
use Migrations\AbstractMigration;

class CreateBlogsTags extends AbstractMigration
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
        $table = $this->table('blogs_tags', ['id' => false, 'primary_key' => ['blog_id', 'tag_id']]);
        $table->addColumn('blog_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('tag_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addIndex(['blog_id']);
        $table->addForeignKey('blog_id', 'blogs', 'id');
        $table->addIndex(['tag_id']);
        $table->addForeignKey('tag_id', 'tags', 'id');
        $table->create();
    }
}
