<?php

use Carbon\Carbon;
use Migrations\AbstractSeed;

/**
 * BlogCategories seed.
 */
class BlogCategoriesSeed extends AbstractSeed
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
                'name'     => 'Uncategorized',
                'slug'     => 'uncategorized',
                'created'  => Carbon::now(),
                'ordering' => 1,
                'admin_id' => 1,
            ]
        ];

        $table = $this->table('blog_categories');
        $table->insert($data)->save();
    }
}
