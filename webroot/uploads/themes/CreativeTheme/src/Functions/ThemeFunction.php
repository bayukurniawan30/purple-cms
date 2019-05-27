<?php

namespace EngageTheme\Functions;

use Cake\ORM\TableRegistry;
use Cake\ORM\Query;

class ThemeFunction
{
    private $themeSlug = 'engage_theme';

    public function __construct($webroot) 
    {
        $this->webroot = $webroot;
    }
    public function homeBlogs()
    {
		$blogs = TableRegistry::get('Blogs')->find('all', [
                	'order' => ['Blogs.created' => 'DESC']])->contain('BlogCategories', function (Query $q) {
            			return $q->contain('Pages');
            		})->contain('Admins')->where(['Blogs.status' => '1'])->limit(10);
        return $blogs;
    }
    public function sidebarAbout()
    {
    	$text = '<p class="sidebar-p">I am a creative illustrator and graphic designer with more than 10 years of experience. </p><p class="sidebar-p">Originally from Toronto, currently based in London. </p>';
    	return $text;
    }
    public function myFunction()
    {
        return '<p class="text-primary">This is my function in custom block</p>';
    }
}