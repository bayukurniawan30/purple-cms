<?php

namespace App\Purple;

use Cake\Datasource\ConnectionManager;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Text;
use Cake\Utility\Security;
use Cake\Log\Log;
use Carbon\Carbon;
use DateTimeZone;
use DateTime;

class PurpleProjectSetup
{
	public function __construct() {
		$conn = ConnectionManager::get('default');
        $this->conn = $conn;
    }
    public function hashPassword($password) {
    	$hasher = new DefaultPasswordHasher();
    	$hashPassword = $hasher->hash($password);
    	return $hashPassword;
    }
    public function generateTimezoneList() {
        static $regions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        );
        $timezones = array();
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }
        $timezone_offsets = array();
        foreach ($timezones as $timezone){
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }
        //Sort timezone by offset
        asort($timezone_offsets);
        $timezone_list = array();
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );
            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }
        return $timezone_list;
	}
	public function apiKeyGenerator($length = 32)
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
	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	private function mysqlForeignKey($key, $column, $referenceTable, $referenceColumn, $next = false, $first = false)
	{
		if (getenv("PURPLE_DATABASE_NAME") !== false && getenv("PURPLE_DATABASE_USER") !== false && file_exists(CONFIG . '.env')) {
			if (getenv("PURPLE_DEPLOY_PLATFORM") == 'heroku') {
				if (getenv("PURPLE_DATABASE_DRIVER") == 'mysql') {
					$generatedForeignKey = '';
					if ($first) {
						$generatedForeignKey .= ',';
					}
					$generatedForeignKey .= 'FOREIGN KEY ' . $key . ' (' . $column . ') REFERENCES ' . $referenceTable . '(' . $referenceColumn . ')';
					if ($next) {
						$generatedForeignKey .= ',';
					}
				}
				else if (getenv("PURPLE_DATABASE_DRIVER") == 'pgsql') {
					$generatedForeignKey = '';
				}
			}
			else {
				$generatedForeignKey = '';
				if ($first) {
					$generatedForeignKey .= ',';
				}
				$generatedForeignKey .= 'FOREIGN KEY ' . $key . ' (' . $column . ') REFERENCES ' . $referenceTable . '(' . $referenceColumn . ')';
				if ($next) {
					$generatedForeignKey .= ',';
				}
			}
		}
		else {
			$generatedForeignKey = '';
			if ($first) {
				$generatedForeignKey .= ',';
			}
			$generatedForeignKey .= 'FOREIGN KEY ' . $key . ' (' . $column . ') REFERENCES ' . $referenceTable . '(' . $referenceColumn . ')';
			if ($next) {
				$generatedForeignKey .= ',';
			}
		}

		return $generatedForeignKey;
	}
	private function pgsqlForeignKey($referenceTable, $referenceColumn)
	{
		if (getenv("PURPLE_DATABASE_NAME") !== false && getenv("PURPLE_DATABASE_USER") !== false && file_exists(CONFIG . '.env')) {
			if (getenv("PURPLE_DEPLOY_PLATFORM") == 'heroku') {
				if (getenv("PURPLE_DATABASE_DRIVER") == 'mysql') {
					$generatedForeignKey = '';
				}
				else if (getenv("PURPLE_DATABASE_DRIVER") == 'pgsql') {
					$generatedForeignKey = ' REFERENCES ' . $referenceTable . '(' . $referenceColumn . ')';
				}
			}
			else {
				$generatedForeignKey = '';
			}
		}
		else {
			$generatedForeignKey = '';
		}

		return $generatedForeignKey;
	}
	public function createTable($email, $timezone)
	{
		if (getenv("PURPLE_DATABASE_NAME") !== false && getenv("PURPLE_DATABASE_USER") !== false && file_exists(CONFIG . '.env')) {
			if (getenv("PURPLE_DEPLOY_PLATFORM") == 'heroku') {
				if (getenv("PURPLE_DATABASE_DRIVER") == 'mysql') {
					$autoIncrement = 'INT AUTO_INCREMENT';
					$storageEngine = " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
					$typeInteger   = 'INT';
					$typeDatetime  = 'DATETIME';
					$mysqlTableUniqueSlug  = 'UNIQUE KEY (slug),';
					$mysqlTableUniqueName  = 'UNIQUE KEY (name),';
					$mysqlTableUniqueTitle = ',UNIQUE KEY (title)';
					$pgsqlTableUnique      = '';
				}
				else if (getenv("PURPLE_DATABASE_DRIVER") == 'pgsql') {
					$autoIncrement = 'serial';
					$storageEngine = '';
					$typeInteger   = 'DECIMAL';
					$typeDatetime  = 'TIMESTAMP';
					$mysqlTableUniqueSlug  = '';
					$mysqlTableUniqueName  = '';
					$mysqlTableUniqueTitle = '';
					$pgsqlTableUnique      = ' UNIQUE ';
				}
			}
			else {
				$autoIncrement = 'INT AUTO_INCREMENT';
				$storageEngine = " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
				$typeInteger   = 'INT';
				$typeDatetime  = 'DATETIME';
				$mysqlTableUniqueSlug  = 'UNIQUE KEY (slug),';
				$mysqlTableUniqueName  = 'UNIQUE KEY (name),';
				$mysqlTableUniqueTitle = ',UNIQUE KEY (title)';
				$pgsqlTableUnique = '';
			}
		}
		else {
			$autoIncrement = 'INT AUTO_INCREMENT';
			$storageEngine = " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
			$typeInteger   = 'INT';
			$typeDatetime  = 'DATETIME';
			$mysqlTableUniqueSlug  = 'UNIQUE KEY (slug),';
			$mysqlTableUniqueName  = 'UNIQUE KEY (name),';
			$mysqlTableUniqueTitle = ',UNIQUE KEY (title)';
			$pgsqlTableUnique = '';
		}
				
		$this->conn->execute('CREATE table admins(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    username VARCHAR( 50 ) NOT NULL,
			    password VARCHAR( 255 ) NOT NULL,
			    api_key_plain VARCHAR( 255 ) NOT NULL,
			    api_key VARCHAR( 255 ) NOT NULL,
			    email VARCHAR( 100 ) NOT NULL,
			    photo VARCHAR( 200 ) NULL,
			    phone VARCHAR( 25 ) NULL,
				phone_verified VARCHAR( 1 ) NULL,
				authy_id VARCHAR( 255 ) NULL,
				authy_remember_token VARCHAR( 100 ) NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    display_name VARCHAR( 100 ) NOT NULL,
			    level ' . $typeInteger. '( 1 ) NOT NULL,
			    about VARCHAR ( 255 ) NULL,
			    last_login ' . $typeDatetime . ' NULL,
			    facebook VARCHAR ( 200 ) NULL,
			    googleplus VARCHAR ( 200 ) NULL,
			    twitter VARCHAR ( 200 ) NULL,
			    token VARCHAR ( 200 ) NULL,
			    first_login VARCHAR ( 50 ) NULL,
			    login_device VARCHAR ( 50 ) NULL,
			    login_os VARCHAR ( 50 ) NULL,
			    login_browser VARCHAR ( 50 ) NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table settings(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 100 ) NOT NULL,
			    value TEXT NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table blog_types(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 100 ) NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table page_templates(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 100 ) NOT NULL,
			    type VARCHAR( 100 ) NOT NULL,
			    column_position ' . $typeInteger. '( 1 ) NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table pages(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    title VARCHAR( 100 ) NOT NULL,
			    slug VARCHAR( 100 ) NOT NULL ' . $pgsqlTableUnique . ',
			    status CHAR( 1 ) NOT NULL,
			    page_template_id ' . $typeInteger. '( 11 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . ',
			    ' . $mysqlTableUniqueSlug . '
			    page_option VARCHAR( 100 ) NULL,
			    parent ' . $typeInteger. '( 11 ) NULL
			    ' . $this->mysqlForeignKey('admin_page', 'admin_id', 'admins', 'id', true, true) . '
			    ' . $this->mysqlForeignKey('page_template_page', 'page_template_id', 'page_templates', 'id') . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table blog_categories(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 100 ) NOT NULL,
				slug VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
				' . $mysqlTableUniqueSlug . '
			    page_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('pages', 'id') . ',
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    ordering ' . $typeInteger. '( 11 ) NULL,
                admin_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_blog_category', 'admin_id', 'admins', 'id', true, true) . '
                ' . $this->mysqlForeignKey('page_blog_category', 'page_id', 'pages', 'id') . ')' . $storageEngine . ';');

        $this->conn->execute('CREATE table chats (
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    subject VARCHAR( 200 ) NOT NULL,
			    content TEXT NOT NULL,
			    sender ' . $typeInteger. '( 11 ) NOT NULL,
			    receiver ' . $typeInteger. '( 11 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    is_read ' . $typeInteger. '( 1 ) NOT NULL,
			    sender_folder VARCHAR( 20 ) NOT NULL,
			    receiver_folder VARCHAR( 20 ) NOT NULL,
			    type VARCHAR( 10 ) NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table collections (
			    id ' . $autoIncrement . ' PRIMARY KEY,
				name VARCHAR( 100 ) NOT NULL,
				slug VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    fields TEXT NOT NULL,
			    status CHAR( 1 ) NOT NULL,
			    connecting CHAR( 1 ) NOT NULL,
			    sorting VARCHAR( 50 ) NOT NULL,
			    sorting_order VARCHAR( 10 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
                admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_collection', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table collection_datas (
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    content MEDIUMTEXT NOT NULL,
				slug VARCHAR( 191 ) NULL ' . $pgsqlTableUnique . ',
				slug_target VARCHAR( 100 ) NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    collection_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('collections', 'id') . ',
                admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
				' . $this->mysqlForeignKey('admin_collection_data', 'admin_id', 'admins', 'id', true, true) . '
                ' . $this->mysqlForeignKey('collection_collection_data', 'collection_id', 'collections', 'id') . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table fonts(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 100 ) NOT NULL,
			    link VARCHAR( 255 ) NOT NULL,
			    family VARCHAR( 100 ) NOT NULL,
			    applied VARCHAR( 255 ) NOT NULL,
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_font', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table histories(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    title VARCHAR( 200 ) NOT NULL,
			    detail TEXT NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_history', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table medias(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    title VARCHAR( 255 ) NOT NULL,
			    description TEXT NULL,
			    size ' . $typeInteger. '( 11 ) NOT NULL,
			    ' . $mysqlTableUniqueName . '
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_media', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table media_docs(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    title VARCHAR( 255 ) NOT NULL,
			    description TEXT NULL,
			    size ' . $typeInteger. '( 11 ) NOT NULL,
			    ' . $mysqlTableUniqueName . '
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_media_doc', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table media_galleries(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    image VARCHAR( 255 ) NOT NULL,
			    sc TEXT NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    ordering VARCHAR( 255 ) NULL,
				type VARCHAR( 50 ) NULL,
				' . $mysqlTableUniqueName . '
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_media_gallery', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table media_videos(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    title VARCHAR( 255 ) NOT NULL,
			    description TEXT NULL,
			    size ' . $typeInteger. '( 11 ) NOT NULL,
			    ' . $mysqlTableUniqueName . '
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_media_video', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table menus(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    title VARCHAR( 100 ) NOT NULL,
			    ordering ' . $typeInteger. '( 11 ) NULL,
			    has_sub ' . $typeInteger. '( 11 ) NULL,
			    status CHAR( 1 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
                target VARCHAR( 255 ) NOT NULL,
			    page_id INT NULL ' . $this->pgsqlForeignKey('pages', 'id') . ',
                admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_menu', 'admin_id', 'admins', 'id', true, true) . '
                ' . $this->mysqlForeignKey('page_menu', 'page_id', 'pages', 'id') . ')' . $storageEngine . ';');

        $this->conn->execute('CREATE table blogs(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    title VARCHAR( 255 ) NOT NULL,
			    content TEXT NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    slug VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    blog_type_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('blog_types', 'id') . ',
			    blog_category_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('blog_categories', 'id') . ',
			    comment VARCHAR( 3 ) NOT NULL,
			    featured VARCHAR( 500 ) NULL,
			    selected CHAR( 3 ) NULL,
			    meta_keywords TEXT NULL,
			    meta_description TEXT NULL,
			    status VARCHAR( 10 ) NOT NULL,
			    social_share VARCHAR( 10 ) NOT NULL,
			    ' . $mysqlTableUniqueSlug . '
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_blog', 'admin_id', 'admins', 'id', true, true) . '
			    ' . $this->mysqlForeignKey('blogcategory_blog', 'blog_category_id', 'blog_categories', 'id', true) . '
			    ' . $this->mysqlForeignKey('blogtype_blog', 'blog_type_id', 'blog_types', 'id') . ')' . $storageEngine . ';');

		// $this->conn->execute('ALTER TABLE blogs ADD FULLTEXT (title);');
		// $this->conn->execute('ALTER TABLE blogs ADD FULLTEXT (content);');

		$this->conn->execute('CREATE table comments(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 50 ) NOT NULL,
			    email VARCHAR( 100 ) NOT NULL,
			    content VARCHAR( 1000 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    status ' . $typeInteger. '( 1 ) NOT NULL,
			    reply ' . $typeInteger. '( 11 ) NOT NULL,
			    is_read ' . $typeInteger. '( 1 ) NULL,
			    admin_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('admins', 'id') . ',
			    blog_id ' . $typeInteger. '( 1 ) NOT NULL ' . $this->pgsqlForeignKey('blogs', 'id') . '
			    ' . $this->mysqlForeignKey('admin_comment', 'admin_id', 'admins', 'id', true, true) . '
			    ' . $this->mysqlForeignKey('blog_comment', 'blog_id', 'blogs', 'id') . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table blog_sidebar(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    page VARCHAR( 255 ) NOT NULL,
			    content TEXT NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table blog_visitors(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    ip VARCHAR( 50 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    blog_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('blogs', 'id') . '
			    ' . $this->mysqlForeignKey('blog_blogvisitor', 'blog_id', 'blogs', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table tags(
				id ' . $autoIncrement . ' PRIMARY KEY,
				title VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    slug VARCHAR( 191 ) NOT NULL,
				created ' . $typeDatetime . ' NOT NULL,
				modified ' . $typeDatetime . '
				' . $mysqlTableUniqueTitle . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table blogs_tags (
				blog_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('blogs', 'id') . ',
				tag_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('tags', 'id') . ',
				PRIMARY KEY (blog_id, tag_id)
				' . $this->mysqlForeignKey('tag_key', 'tag_id', 'tags', 'id', true, true) . '
				' . $this->mysqlForeignKey('blog_key', 'blog_id', 'blogs', 'id') . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table messages(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    subject VARCHAR( 100 ) NOT NULL,
			    content TEXT NOT NULL,
			    name VARCHAR( 50 ) NOT NULL,
			    email VARCHAR( 200 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    is_read ' . $typeInteger. '( 1 ) NOT NULL,
			    folder VARCHAR( 20 ) NOT NULL,
			    replied ' . $typeInteger. '( 1 ) NOT NULL,
			    type VARCHAR( 20 ) NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table notifications(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    type VARCHAR( 20 ) NOT NULL,
			    content VARCHAR( 255 ) NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    is_read ' . $typeInteger. '( 1 ) NULL,
			    comment_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('comments', 'id') . ',
			    message_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('messages', 'id') . ',
			    blog_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('blogs', 'id') . '
			    ' . $this->mysqlForeignKey('comment_notification', 'comment_id', 'comments', 'id', true, true) . '
			    ' . $this->mysqlForeignKey('message_notification', 'message_id', 'messages', 'id', true) . '
				' . $this->mysqlForeignKey('blog_notification', 'blog_id', 'blogs', 'id') . ')' . $storageEngine . ';');

        $this->conn->execute('CREATE table generals(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    content TEXT NOT NULL,
			    meta_keywords TEXT NULL,
			    meta_description TEXT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
                page_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('pages', 'id') . ',
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_general', 'admin_id', 'admins', 'id', true, true) . '
                ' . $this->mysqlForeignKey('page_general', 'page_id', 'pages', 'id') . ')' . $storageEngine . ';');

        $this->conn->execute('CREATE table custom_pages(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    file_name VARCHAR( 100 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    meta_keywords TEXT NULL,
			    meta_description TEXT NULL,
			    page_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('pages', 'id') . ',
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_custom_page', 'admin_id', 'admins', 'id', true, true) . '
                ' . $this->mysqlForeignKey('page_custom_page', 'page_id', 'pages', 'id') . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table singletons (
			    id ' . $autoIncrement . ' PRIMARY KEY,
				name VARCHAR( 100 ) NOT NULL,
				slug VARCHAR( 191 ) NOT NULL ' . $pgsqlTableUnique . ',
			    content TEXT NULL,
			    fields TEXT NOT NULL,
			    status CHAR( 1 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
                admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
			    ' . $this->mysqlForeignKey('admin_singleton', 'admin_id', 'admins', 'id', false, true) . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table singleton_datas (
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    content MEDIUMTEXT NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
			    singleton_id ' . $typeInteger. '( 11 ) NULL ' . $this->pgsqlForeignKey('singletons', 'id') . ',
                admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
				' . $this->mysqlForeignKey('admin_singleton_data', 'admin_id', 'admins', 'id', true, true) . '
                ' . $this->mysqlForeignKey('singleton_singleton_data', 'singleton_id', 'singletons', 'id') . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table socials(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    name VARCHAR( 100 ) NOT NULL,
			    link VARCHAR( 255 ) NOT NULL,
			    ordering ' . $typeInteger. '( 11 ) NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table subscribers(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    email VARCHAR( 100 ) NOT NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    unsubscribe_date ' . $typeDatetime . ' NULL,
			    status VARCHAR( 20 ) NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table submenus(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    title VARCHAR( 100 ) NOT NULL,
			    menu_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('menus', 'id') . ',
			    status CHAR( 1 ) NOT NULL,
			    ordering ' . $typeInteger. '( 11 ) NULL,
			    created ' . $typeDatetime . ' NOT NULL,
			    modified ' . $typeDatetime . ' NULL,
                target VARCHAR( 255 ) NOT NULL,
			    page_id INT NULL ' . $this->pgsqlForeignKey('pages', 'id') . ',
			    admin_id ' . $typeInteger. '( 11 ) NOT NULL ' . $this->pgsqlForeignKey('admins', 'id') . '
				' . $this->mysqlForeignKey('menu_submenu', 'menu_id', 'menus', 'id', true, true) . '
			    ' . $this->mysqlForeignKey('admin_submenu', 'admin_id', 'admins', 'id', true) . '
                ' . $this->mysqlForeignKey('page_submenu', 'page_id', 'pages', 'id') . ')' . $storageEngine . ';');

		$this->conn->execute('CREATE table visitors(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    ip VARCHAR( 50 ) NOT NULL,
			    browser VARCHAR( 100 ) NOT NULL,
			    platform VARCHAR( 100 ) NOT NULL,
			    device VARCHAR( 100 ) NOT NULL,
			    date_created DATE NOT NULL,
				time_created TIME NOT NULL)' . $storageEngine . ';');

		$this->conn->execute('CREATE table widgets(
			    id ' . $autoIncrement . ' PRIMARY KEY,
			    title VARCHAR( 100 ) NOT NULL,
			    content TEXT NOT NULL,
			    content_limit INT NULL)' . $storageEngine . ';');

		/**
		 * Insert Core admin for debugging
		 */

		$hasher = new DefaultPasswordHasher();

		// Generate an API 'token'
		$apiKeyPlain = Security::hash(Security::randomBytes(32), 'sha256', false);

		// Bcrypt the token so BasicAuthenticate can check
		// it during login.
		$apiKey = $hasher->hash($apiKeyPlain);
		$randomPassword = $this->generateRandomString();

		$returnData = [
			'username'     => 'purplecore',
			'password'     => $randomPassword,
			'display_name' => 'Core',
			'level'        => '1',
		];

		$this->conn->insert('admins', [
			'username'      => 'purplecore',
			'password'      => $this->hashPassword($randomPassword),
			'api_key_plain' => $apiKeyPlain,
			'api_key'       => $apiKey,
			'email'         => $email,
			'photo'         => NULL,
			'display_name'  => 'Core',
			'level'         => '1',
			'first_login'   => 'yes',
			'created'       => Carbon::now($timezone)
		]);

		/**
		 * Insert blog types data
		 */
		$this->conn->insert('blog_types', ['name' => 'standard']);
		$this->conn->insert('blog_types', ['name' => 'image']);
		$this->conn->insert('blog_types', ['name' => 'video']);

		/**
		 * Insert page template data
		 */
		$this->conn->insert('page_templates', [
			'name'            => 'General (Block Editor)',
			'type'            => 'general',
			'column_position' => '1'
		]);
		$this->conn->insert('page_templates', [
			'name'            => 'Blog',
			'type'            => 'blog',
			'column_position' => '2'
		]);
		$this->conn->insert('page_templates', [
			'name'            => 'Custom Page (Your Code)',
			'type'            => 'custom',
			'column_position' => '1'
		]);

		/**
		 * Insert settings data
		 */
		$this->conn->insert('settings', [
			'name'  => 'sitename',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'siteurl',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'foldername',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'tagline',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'template',
			'value' => 'EngageTheme'
		]);
		$this->conn->insert('settings', [
			'name'  => 'email',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'phone',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'secondaryfooter',
			'value' => 'NULL::Created with &#60;a href=http://purple-cms.com&#62;Purple&#60;/a&#62;'
		]);
		$this->conn->insert('settings', [
			'name'  => 'metakeywords',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'metadescription',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'ldjson',
			'value' => 'enable'
		]);
		$this->conn->insert('settings', [
			'name'  => 'contactheader',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'address',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'websitelogo',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'colorscheme',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'homepagestyle',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'favicon',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'timezone',
			'value' => '(UTC+08:00) Asia/Makassar'
		]);
		$this->conn->insert('settings', [
			'name'  => 'recaptchasitekey',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'recaptchasecret',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'customprimary',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'customsecondary',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'googlemapapi',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'googleanalyticscode',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'dateformat',
			'value' => 'F d, Y'
		]);
		$this->conn->insert('settings', [
			'name'  => 'timeformat',
			'value' => 'g:i a'
		]);
		$this->conn->insert('settings', [
			'name'  => 'comingsoon',
			'value' => 'disable'
		]);
		$this->conn->insert('settings', [
			'name'  => 'datetimemaintenance',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'homepagelink',
			'value' => 'show'
		]);
		$this->conn->insert('settings', [
			'name'  => 'defaultbackgroundlogin',
			'value' => 'yes'
		]);
        $this->conn->insert('settings', [
			'name'  => 'backgroundlogin',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'backgroundmaintenance',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'footer-column4',
			'value' => 'disable'
		]);
		$this->conn->insert('settings', [
			'name'  => 'userplan',
			'value' => 'free'
		]);
		$this->conn->insert('settings', [
			'name'  => 'totalpage',
			'value' => '4'
		]);
		$this->conn->insert('settings', [
			'name'  => 'instafeeduserid',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'developermode',
			'value' => 'off'
		]);
		$this->conn->insert('settings', [
			'name'  => 'logoff',
			'value' => '0'
		]);
		$this->conn->insert('settings', [
			'name'  => 'postlimitperpage',
			'value' => '5'
		]);
		$this->conn->insert('settings', [
			'name'  => 'postpermalink',
			'value' => 'day-name' // day-name, month-name, or post-name
		]);
		$this->conn->insert('settings', [
			'name'  => 'socialshare',
			'value' => '\"email\",\"twitter\",\"facebook\",\"googleplus\",\"linkedin\",\"pinterest\",\"messenger\",\"line\",\"whatsapp\"'
		]);
		$this->conn->insert('settings', [
			'name'  => 'socialtheme',
			'value' => 'flat'
		]);
		$this->conn->insert('settings', [
			'name'  => 'socialfontsize',
			'value' => '14'
		]);
		$this->conn->insert('settings', [
			'name'  => 'sociallabel',
			'value' => 'true'
		]);
		$this->conn->insert('settings', [
			'name'  => 'socialcount',
			'value' => 'false'
		]);
        $this->conn->insert('settings', [
			'name'  => 'smtphost',
			'value' => ''
		]);
        $this->conn->insert('settings', [
			'name'  => 'smtpauth',
			'value' => 'true'
		]);
        $this->conn->insert('settings', [
			'name'  => 'smtpusername',
			'value' => ''
		]);
        $this->conn->insert('settings', [
			'name'  => 'smtppassword',
			'value' => ''
		]);
        $this->conn->insert('settings', [
			'name'  => 'smtpsecure',
			'value' => ''
		]);
        $this->conn->insert('settings', [
			'name'  => 'smtpport',
			'value' => ''
		]);
        $this->conn->insert('settings', [
			'name'  => 'senderemail',
			'value' => ''
		]);
        $this->conn->insert('settings', [
			'name'  => 'sendername',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'purpleapipublic',
			'value' => $this->hashPassword('public-purple is awesome')
		]);
		$this->conn->insert('settings', [
			'name'  => 'apiaccesskey',
			'value' => $this->apiKeyGenerator()
		]);
		$this->conn->insert('settings', [
			'name'  => 'productionkey',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'twiliosid',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'twiliotoken',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'mailchimpapikey',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'mailchimplistid',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'mediastorage',
			'value' => 'server'
		]);
		$this->conn->insert('settings', [
			'name'  => 'awss3accesskey',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'awss3secretkey',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'awss3region',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => 'awss3bucket',
			'value' => ''
		]);
		$this->conn->insert('settings', [
			'name'  => '2fa',
			'value' => 'disable'
		]);

		return $returnData;
	}
}