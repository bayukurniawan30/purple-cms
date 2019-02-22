<?php
// src/View/Helper/SetupHelper.php
namespace App\View\Helper;

use Cake\View\Helper;

class SetupHelper extends Helper
{
    public function checkSubfolder()
    {
    	$serverName = $_SERVER['SERVER_NAME'];
    	if ($serverName == 'localhost') {
			$explodeurl = explode('/', $_SERVER['REQUEST_URI']);
			$subfolder  = $explodeurl[1];
	    }
	    else {
	    	if (dirname($_SERVER['PHP_SELF']) == '/') {
	        	$subfolder = '';
	        }
	        else {
	        	$subfolder = str_replace("/", "", dirname($_SERVER['PHP_SELF']));
	        }
	    }
	    return $subfolder;
    }
}