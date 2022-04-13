<?php

use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Security;
use Carbon\Carbon;
use Migrations\AbstractSeed;

/**
 * Admins seed.
 */
class AdminsSeed extends AbstractSeed
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
		$apiKeyPlain = Security::hash(Security::randomBytes(32), 'sha256', false);

		$hasher = new DefaultPasswordHasher();
		$apiKey = $hasher->hash($apiKeyPlain);

        $faker = Faker\Factory::create();

		$hasher = new DefaultPasswordHasher();
    	$hashPassword = $hasher->hash('secret');

        $data = [
            'username'      => 'purplecore',
			'password'      => $hashPassword,
			'api_key_plain' => $apiKeyPlain,
			'api_key'       => $apiKey,
			'email'         => $faker->email,
			'photo'         => NULL,
			'display_name'  => 'Core',
			'level'         => '1',
			'first_login'   => 'yes',
			'created'       => Carbon::now()
        ];

        $table = $this->table('admins');
        $table->insert($data)->save();
    }
}
