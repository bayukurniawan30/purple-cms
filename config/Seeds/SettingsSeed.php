<?php

use Cake\Auth\DefaultPasswordHasher;
use Migrations\AbstractSeed;

/**
 * Settings seed.
 */
class SettingsSeed extends AbstractSeed
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
        $faker = Faker\Factory::create();

        $data = [
            [
                'name'  => 'sitename',
                'value' => 'Purple CMS'
            ],
            [
                'name'  => 'siteurl',
                'value' => 'http://'
            ],
            [
                'name'  => 'foldername',
                'value' => ''
            ],
            [
                'name'  => 'tagline',
                'value' => 'Best Website Ever'
            ],
            [
                'name'  => 'template',
                'value' => 'EngageTheme'
            ],
            [
                'name'  => 'email',
                'value' => $faker->email
            ],
            [
                'name'  => 'phone',
                'value' => ''
            ],
            [
                'name'  => 'secondaryfooter',
                'value' => 'NULL::Created with &#60;a href=http://purple-cms.com&#62;Purple&#60;/a&#62;'
            ],
            [
                'name'  => 'metakeywords',
                'value' => ''
            ],
            [
                'name'  => 'metadescription',
                'value' => ''
            ],
            [
                'name'  => 'ldjson',
                'value' => 'enable'
            ],
            [
                'name'  => 'contactheader',
                'value' => ''
            ],
            [
                'name'  => 'address',
                'value' => ''
            ],
            [
                'name'  => 'websitelogo',
                'value' => ''
            ],
            [
                'name'  => 'colorscheme',
                'value' => ''
            ],
            [
                'name'  => 'homepagestyle',
                'value' => '&lt;section id=&quot;fdb-71781&quot; class=&quot;fdb-block uk-flex uk-flex-middle&quot; data-fdb-id=&quot;71781&quot; style=&quot;background-image: linear-gradient(120deg, rgb(213, 126, 235) 0%, rgb(252, 203, 144) 100%); box-sizing: border-box; min-height: calc(100vh); height: 626px;&quot; uk-height-viewport=&quot;&quot;&gt;    &lt;div class=&quot;container&quot;&gt;&lt;div class=&quot;row uk-flex uk-flex-middle&quot;&gt;&lt;div class=&quot;col-12 col-md-6 col-lg-6 text-left fdb-editor royal-theme&quot;&gt;&lt;h1 class=&quot;fdb-heading&quot;&gt;&lt;span style=&quot;font-size: 42px;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;color: rgb(255, 255, 255);&quot;&gt;Welcome to Purple CMS&lt;/span&gt;&lt;/strong&gt;&lt;/span&gt;&lt;/h1&gt;&lt;p class=&quot;text-h3&quot;&gt;&lt;span style=&quot;font-size: 24px; color: rgb(255, 255, 255);&quot;&gt;A Content Management System build with CakePHP 3. Aiming to make website developer easier and faster to make a website, whether simple or complex.&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;div class=&quot;col-12 col-md-6 col-lg-6&quot;&gt;&lt;img src=&quot;/master-assets/img/purple-dashboard.png&quot; class=&quot;img-fluid fdb-image fdb-editor uk-border-rounded&quot;&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/section&gt;'
            ],
            [
                'name'  => 'favicon',
                'value' => ''
            ],
            [
                'name'  => 'timezone',
                'value' => '(UTC+08:00) Asia/Makassar'
            ],
            [
                'name'  => 'recaptchasitekey',
                'value' => ''
            ],
            [
                'name'  => 'recaptchasecret',
                'value' => ''
            ],
            [
                'name'  => 'customprimary',
                'value' => ''
            ],
            [
                'name'  => 'customsecondary',
                'value' => ''
            ],
            [
                'name'  => 'googlemapapi',
                'value' => ''
            ],
            [
                'name'  => 'googleanalyticscode',
                'value' => ''
            ],
            [
                'name'  => 'dateformat',
                'value' => 'F d, Y'
            ],
            [
                'name'  => 'timeformat',
                'value' => 'g:i a'
            ],
            [
                'name'  => 'comingsoon',
                'value' => 'disable'
            ],
            [
                'name'  => 'datetimemaintenance',
                'value' => ''
            ],
            [
                'name'  => 'homepagelink',
                'value' => 'show'
            ],
            [
                'name'  => 'defaultbackgroundlogin',
                'value' => 'yes'
            ],
            [
                'name'  => 'backgroundlogin',
                'value' => ''
            ],
            [
                'name'  => 'backgroundmaintenance',
                'value' => ''
            ],
            [
                'name'  => 'instafeeduserid',
                'value' => ''
            ],
            [
                'name'  => 'developermode',
                'value' => 'off'
            ],
            [
                'name'  => 'logoff',
                'value' => '0'
            ],
            [
                'name'  => 'postlimitperpage',
                'value' => '5'
            ],
            [
                'name'  => 'postpermalink',
                'value' => 'day-name'
            ],
            [
                'name'  => 'socialshare',
                'value' => '\"email\",\"twitter\",\"facebook\",\"googleplus\",\"linkedin\",\"pinterest\",\"messenger\",\"line\",\"whatsapp\"'
            ],
            [
                'name'  => 'socialtheme',
                'value' => 'flat'
            ],
            [
                'name'  => 'socialfontsize',
                'value' => '14'
            ],
            [
                'name'  => 'sociallabel',
                'value' => 'true'
            ],
            [
                'name'  => 'socialcount',
                'value' => 'false'
            ],
            [
                'name'  => 'smtphost',
                'value' => ''
            ],
            [
                'name'  => 'smtpauth',
                'value' => 'true'
            ],
            [
                'name'  => 'smtpusername',
                'value' => ''
            ],
            [
                'name'  => 'smtppassword',
                'value' => ''
            ],
            [
                'name'  => 'smtpsecure',
                'value' => ''
            ],
            [
                'name'  => 'smtpport',
                'value' => ''
            ],
            [
                'name'  => 'senderemail',
                'value' => ''
            ],
            [
                'name'  => 'sendername',
                'value' => ''
            ],
            [
                'name'  => 'purpleapipublic',
                'value' => $this->hashPassword('public-purple is awesome')
            ],
            [
                'name'  => 'apiaccesskey',
                'value' => $this->apiKeyGenerator()
            ],
            [
                'name'  => 'productionkey',
                'value' => ''
            ],
            [
                'name'  => 'twiliosid',
                'value' => ''
            ],
            [
                'name'  => 'twiliotoken',
                'value' => ''
            ],
            [
                'name'  => 'mailchimpapikey',
                'value' => ''
            ],
            [
                'name'  => 'mailchimplistid',
                'value' => ''
            ],
            [
                'name'  => 'mediastorage',
                'value' => 'server'
            ],
            [
                'name'  => 'awss3accesskey',
                'value' => ''
            ],
            [
                'name'  => 'awss3secretkey',
                'value' => ''
            ],
            [
                'name'  => 'awss3region',
                'value' => ''
            ],
            [
                'name'  => 'awss3bucket',
                'value' => ''
            ],
            [
                'name'  => '2fa',
                'value' => 'disable'
            ],
            [
                'name'  => 'headlessfront',
                'value' => 'disable'
            ],
            [
                'name'  => 'headlessweb',
                'value' => ''
            ],
        ];

        $table = $this->table('settings');
        $table->insert($data)->save();
    }

    private function hashPassword($password) {
    	$hasher = new DefaultPasswordHasher();
    	$hashPassword = $hasher->hash($password);
    	return $hashPassword;
    }

    private function apiKeyGenerator($length = 32)
	{
		$key = '';
		list($usec, $sec) = explode(' ', microtime());
		mt_srand((float) $sec + ((float) $usec * 100000));
		
		$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

		for ($i = 0; $i < $length; $i++)
		{
			$key .= $inputs[mt_rand(0,61)];
		}
		return $key;
	}
}
