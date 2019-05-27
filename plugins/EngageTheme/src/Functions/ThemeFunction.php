<?php

namespace EngageTheme\Functions;

use Cake\ORM\TableRegistry;

class ThemeFunction
{
    private $themeSlug = 'engage_theme';

    public function __construct($webroot) 
    {
        $this->webroot = $webroot;
    }
    public function example()
    {
		return "This is example of theme function.";
    }
}