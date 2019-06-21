<?php

namespace App\Purple;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class PurpleProjectPlugins 
{
	public function purplePlugins()
	{
        // Plugins List
        $listPlugin = new Folder(PLUGINS);
        $pluginJson = $listPlugin->findRecursive('plugin.json', true);

        $plugins = [];
        $pluginStart = 0;
        foreach ($pluginJson as $plugin) {
            $readPluginJson = new File($plugin);
            $readJson 		= $readPluginJson->read();

            if ($readJson != false) {
                $decodeJson                   				= json_decode($readJson, true);
                $plugins[$pluginStart]['yes']               = true;
                $plugins[$pluginStart]['name']              = $decodeJson['name'];
                $plugins[$pluginStart]['namespace']       	= $decodeJson['namespace'];
                $plugins[$pluginStart]['author']            = $decodeJson['author'];
                $plugins[$pluginStart]['version']           = $decodeJson['version'];
                $plugins[$pluginStart]['image']             = $decodeJson['image'];
                $plugins[$pluginStart]['preview']           = $decodeJson['preview'];
                $plugins[$pluginStart]['description']       = $decodeJson['description'];
                $plugins[$pluginStart]['dashboard_sidebar'] = $decodeJson['dashboard_sidebar'];
                $plugins[$pluginStart]['dashboard_assets']  = $decodeJson['dashboard_assets'];
            }
            else {
                $plugins[$pluginStart]['yes'] = false;
            }

            $pluginStart++;
        }

        return $plugins;
    }
}