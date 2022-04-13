<?php
use Migrations\AbstractMigration;

class CreateVisitors extends AbstractMigration
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
        $table = $this->table('visitors');
        $table->addColumn('ip', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
        ]);
        $table->addColumn('browser', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('platform', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('device', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => false,
        ]);
        $table->addColumn('date_created', 'date', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('time_created', 'time', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}
