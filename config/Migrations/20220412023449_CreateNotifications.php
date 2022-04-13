<?php
use Migrations\AbstractMigration;

class CreateNotifications extends AbstractMigration
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
        $table = $this->table('notifications');
        $table->addColumn('type', 'string', [
            'default' => null,
            'limit' => 20,
            'null' => false,
        ]);
        $table->addColumn('content', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);
        $table->addColumn('is_read', 'integer', [
            'default' => null,
            'limit' => 1,
            'null' => true,
        ]);
        $table->addColumn('comment_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('message_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('blog_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addIndex(['comment_id']);
        $table->addForeignKey('comment_id', 'comments', 'id');
        $table->addIndex(['message_id']);
        $table->addForeignKey('message_id', 'messages', 'id');
        $table->addIndex(['blog_id']);
        $table->addForeignKey('blog_id', 'blogs', 'id');
        $table->create();
    }
}
