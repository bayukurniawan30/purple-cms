<?php
// src/View/Helper/SetupHelper.php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Routing\Router;

class SetupHelper extends Helper
{
    public function checkSubfolder()
    {
		$baseUrl   = Router::url(['_name' => 'home']);
		$subfolder = str_replace("/", "", $baseUrl);
	    return $subfolder;
    }
}