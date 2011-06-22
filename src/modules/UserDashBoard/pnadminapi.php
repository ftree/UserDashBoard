<?php

/**
 * get available admin panel links
 *
 * @author Mark West
 * @return array array of admin links
 */
function UserDashBoard_adminapi_getlinks()
{
    $links = array();

    if (SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        $links[] = array('url' => pnModURL('UserDashBoard', 'admin', 'CommonSettings'), 'text' => __('Common Settings'));
        $links[] = array('url' => pnModURL('UserDashBoard', 'admin', 'PluginConfig'), 'text' => __('Plugin Configuration'));
        $links[] = array('url' => pnModURL('UserDashBoard', 'admin', 'DefaultConfig'), 'text' => __('Default Configuration'));
    }

    return $links;
}

function UserDashBoard_adminapi_PluginsReload()
{
    $PluginDir= 'modules/UserDashBoard/plugins';
    $files = FileUtil::getFiles($PluginDir, false, true, 'class.php', 'f');
    // Get all plugins from DB
    $plugins = DBUtil::selectObjectArray('userdashboard_plugins', '','', -1, -1, 'name');
//prayer($plugins);  
  
    foreach ($files as $file)
    {
    	if (substr($file,0, 6) == 'plugin') {
    		$classname = str_replace('.class.php','',str_replace('plugin.','',$file));
//prayer($file);    		
//prayer($classname);			
    		Loader::requireOnce($PluginDir.'/'.$file);
    		$class='Plugin'.$classname;
    		$plugin = new $class();
    		$pName = $plugin->name();
//prayer($pName);    		
    		if (array_key_exists($pName,$plugins)) {
   				$plugins[$pName]['status'] = 'exists';
    		} else {
				$obj = array ('name' => $pName,
							  'title' => $plugin->title(),
							  'size' => $plugin->size(),
							  'file' => $PluginDir.'/'.$file);
				$res = DBUtil::insertObject($obj,'userdashboard_plugins');
				if ($res !== false) {
					$plugins[$pName] = $res;
					$plugins[$pName]['status'] = 'new';
				}
    		}
    	}	
    }
    foreach ($plugins as $plugin)
    {
    	if (!isset($plugin['status'])) {
    		$plugins[$plugin['name']]['status'] = 'delete';
    		DBUtil::deleteObjectByID('userdashboard_plugins',$plugin['id']);
    	}
    }
    return $plugins;
}
  