<?php


/**
 * the main admin function
 * 
 * @return       output
 */
function UserDashBoard_admin_main()
{
	return UserDashBoard_admin_CommonSettings();
}

function UserDashBoard_admin_CommonSettings()
{
	// perform permission check
    if (!SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // start a new pnRender display instance
	$render = & pnRender::getInstance('UserDashBoard',false);
  
	// get the Settings
	$settings = pnModGetVar('UserDashBoard');

	// assign it to the Form
	$render->assign('settings', $settings);

	// fetch, process and display template
	return $render->fetch('UserDashBoard_admin_CommonSettings.htm');
}

function UserDashBoard_admin_UpdateCommonSettings()
{
    // security check
    if (!SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // get settings from form - do before authid check
    $settings = FormUtil::getPassedValue('settings', null, 'POST');

    // if this form wasnt posted to redirect back
    if ($settings === NULL) {
        return pnRedirect(pnModURL('UserDashBoard', 'admin', 'main'));
    }

    // confirm the forms auth key
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError();
    }	
    
    // Write the vars
    $configvars = pnModGetVar('UserDashBoard');
    foreach($settings as $key => $value) {
        $oldvalue = pnModGetVar('UserDashBoard',$key);
        if ($value != $oldvalue) {
            pnModSetVar('UserDashBoard',$key, $value);
        }
    }    

    return pnRedirect(pnModURL('UserDashBoard', 'admin', 'main'));    
}

function UserDashBoard_admin_PluginsReload() {
	// perform permission check
    if (!SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // Security check
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('UserDashBoard', 'admin', 'PluginConfig'));
    }

    $plugins = pnModAPIFunc('UserDashBoard', 'admin', 'PluginsReload');
    
    if ($plugins !== false) { 
    	LogUtil::registerStatus(__("Plugins successfully reloaded."));	
    } else {
    	LogUtil::registerError(__("Plugins reloading failed!"));
    }
    
    SessionUtil::setVar('UserDashBoard_Plugins', $plugins);
    SessionUtil::setVar('UserDashBoard_Preloaded', true);
    
    return pnRedirect(pnModURL('UserDashBoard', 'admin', 'PluginConfig')); 

}

function UserDashBoard_admin_ChangePluginsState() {
	// perform permission check
    if (!SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }
    // Security check
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerAuthidError(pnModURL('UserDashBoard', 'admin', 'PluginConfig'));
    }

    $id = (int) FormUtil::getPassedValue('id', null, 'GET');
    $state = (int) FormUtil::getPassedValue('state', null, 'GET');
    
    if (!is_numeric($id)) {
        return LogUtil::registerArgsError(pnModURL('UserDashBoard', 'admin', 'PluginConfig'));
    }
    if (!is_numeric($state)) {
        return LogUtil::registerArgsError(pnModURL('UserDashBoard', 'admin', 'PluginConfig'));
    }

    $obj = array('id'=>$id,
    			 'active'=>$state);
    
	$res = DBUtil::updateObject($obj,'userdashboard_plugins');
    prayer("<br><br><br><br><br><br><br>TEST<br>");
    prayer($res);
    if ($res['active'] == $state) { 
    	if ($state == 1) {
    		LogUtil::registerStatus(__("Plugin successfully activated."));
    	} else {
    		LogUtil::registerStatus(__("Plugin successfully deactivated."));
    	}
    } else {
        if ($state == 1) {
    		LogUtil::registerError(__("Plugins activation failed!"));
    	} else {
    		LogUtil::registerError(__("Plugins deactivation failed!"));
    	}    	
    }
    return pnRedirect(pnModURL('UserDashBoard', 'admin', 'PluginConfig'));     
}

function UserDashBoard_admin_PluginConfig($plugins, $preloaded)
{
	// perform permission check
    if (!SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    $plugins = SessionUtil::getVar('UserDashBoard_Plugins');
    $preloaded = SessionUtil::getVar('UserDashBoard_Preloaded');

    SessionUtil::delVar('UserDashBoard_Plugins');
    SessionUtil::delVar('UserDashBoard_Preloaded');
    
    // start a new pnRender display instance
	$render = & pnRender::getInstance('UserDashBoard',false);

	// get the Settings
	$settings = pnModGetVar('UserDashBoard');
	if (!($preloaded === true)) {
		$plugins = DBUtil::selectObjectArray('userdashboard_plugins','','id');
	}

	// assign it to the Form
	$render->assign('settings', $settings);
	$render->assign('plugins', $plugins);
	
	// fetch, process and display template
	return $render->fetch('UserDashBoard_admin_PluginConfig.htm');	
}

function UserDashBoard_admin_DefaultConfig()
{
	// perform permission check
    if (!SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }

    // start a new pnRender display instance
	$render = & pnRender::getInstance('UserDashBoard',false);
  
	// get the Settings
	$settings = pnModGetVar('UserDashBoard');

	$plugins 	= pnModAPIFunc('UserDashBoard','user','getPlugins');
	$boxes 		= pnModAPIFunc('UserDashBoard','user','get',array('block'=>0,'uid'=>-1));
	$plugins 	= pnModAPIFunc('UserDashBoard','user','filterPlugins',array ('boxes' => $boxes, 'plugins' => $plugins));	
	for ($i=0; $i < count($boxes); $i++) {
	  	$result = pnModAPIFunc('UserDashBoard','user','getBoxCode',array('box'		 =>$boxes[$i],
	  																	 'allowEdit' => true,
	  																	 'admin'	 => true));
		$boxes[$i]['boxcode'] = $result;
	}

	// assign it to the Form
	$render->assign('settings', $settings);
	$render->assign('boxes', 	$boxes);
	$render->assign('plugins', 	$plugins);
	// fetch, process and display template
	return $render->fetch('UserDashBoard_admin_DefaultConfig.htm');	
}
function UserDashBoard_admin_add()
{
	// perform permission check
    if (!SecurityUtil::checkPermission('UserDashBoard::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerPermissionError();
    }	
  	// Get Parameter and do a little security check
	$box 	= FormUtil::getPassedValue('box');
	$block 	= FormUtil::getPassedValue('block',0);
	$page 	= FormUtil::getPassedValue('page',1);
	$pos 	= (int)FormUtil::getPassedValue('pos');
	if (!isset($box) || ($box == '')) {
	    return pnRedirect(pnModURL('UserDashBoard','admin','DefaultConfig'));
	}

	$uid = -1;
	// Add new box
	$result = pnModAPIFunc('UserDashBoard','user','add',array('uid' 	=> $uid, 
															  'box' 	=> $box, 
															  'pos' 	=> $pos,
															  'block' 	=> $block,
															  'page' 	=> $page));
	if (!$result) {
	  	LogUtil::registerError(__('Add Error occurrred!'));
	} else {
	  	LogUtil::registerStatus(__('Box was added.'));
	}

	// Clean order values
  	pnModAPIFunc('UserDashBoard','user','cleanOrder',array('uid' 	=> $uid,
  														   'block' 	=> $block,
														   'page' 	=> $page));

	// Redirect to main page
	return pnRedirect(pnModURL('UserDashBoard','admin','DefaultConfig'));
}
function UserDashBoard_admin_updateTables () {
	prayer("<br><br><br><br><br><br><br><br><br>");	
	if (DBUtil::createTable('userdashboard_plugins')) {
		prayer("Table userdashboard_plugins angelegt");
	} else {
		prayer("Table userdashboard_plugins anlegen fehlgeschlagen");
	}
}