<?php
use Migrations\AbstractSeed;

/**
 * BlogTypes seed.
 */
class BlogTypesSeed extends AbstractSeed
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
                'name' => 'standard'
            ],
            [
                'name' => 'image'
            ],
            [
                'name' => 'video'
            ],
        ];

        $table = $this->table('blog_types');
        $table->insert($data)->save();
    }
}
