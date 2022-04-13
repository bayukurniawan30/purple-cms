<?php
use Migrations\AbstractSeed;

/**
 * PageTemplates seed.
 */
class PageTemplatesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name'            => 'General (Block Editor)',
                'type'            => 'general',
                'column_position' => '1'
            ],
            [
                'name'            => 'Blog',
                'type'            => 'blog',
                'column_position' => '2'
            ],
            [
                'name'            => 'Custom Page (Your Code)',
                'type'            => 'custom',
                'column_position' => '1'
            ],
        ];

        $table = $this->table('page_templates');
        $table->insert($data)->save();
    }
}
