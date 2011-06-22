<?php

/*
function UserDashBoard_userapi_getTemplates()
{
	// FileUtil is not loaded by default
  	Loader::loadClass('FileUtil');
  	$files = FileUtil::getFiles('modules/UserDashBoard/plugins',false,true,'.inc');
  	// Get name of plugins
  	$result = array();
  	foreach ($files as $file) {
	  	Loader::includeOnce('modules/UserDashBoard/plugins/'.$file);
	  	$dummy = explode('.inc',$file);
	  	$dummy2 = explode('plugin.',$dummy[0]);
	  	$plugin = $dummy2[1];
	  	// execute plugin function to get plugin MetaData
	  	$func = 'box_'.$plugin.'_getMetaData';
	  	if ($func()) {
            $result[] = $func();
        }
	}
  	return $result;
}
*/
function UserDashBoard_userapi_getPlugins()
{
	$plugins = DBUtil::selectObjectArray('userdashboard_plugins', '','', -1, -1, 'name');
/*
	foreach ($plugins as $plugin) {
		$file = $plugin['file'];
    		Loader::requireOnce($PluginDir.'/'.$file);
    		$class='Plugin'.$classname;
    		$plugin = new $class();
    		$pName = $plugin->name();		
	}	
*/	
  	return $plugins;
}

function UserDashBoard_userapi_get($args)
{
	$size = 0;
	$block = $args['block'];
	$blockinfo = $args['blockinfo'];
  	$uid = (int)$args['uid'];
	if (!isset($args['uid']) || $uid=="") {
		$uid = pnUserGetVar('uid');
	}
	$where = "udb_uid = $uid";
	if (isset($args['block'])) {
		$where .= " AND udb_block=$block";
	}
	$order = 'udb_pos ASC, udb_id DESC';
	$boxes = DBUtil::selectObjectArray('userdashboard_boxes',$where,$order);
	$plugins = UserDashBoard_userapi_getPlugins();
	if (!$boxes) {
	  	return false;
	} else {
	  	$res = array();
	  	// Some extra code for sorting items
	  	$previous = '';
	  	$next = array();
	  	$bid = $blockinfo['bid'];
	  	foreach ($boxes as $box) {
	  		$plugin = $plugins[$box['plugin']];
	  		if (is_array($plugin)) {
		  		Loader::requireOnce($plugin['file']);
	    		$class=$plugin['name'];
	    		$cplugin = new $class();
		  		
		  		$box['title']  		= $cplugin->title();
	    	    $box['size']   		= $cplugin->size();
	    	    $box['output'] 		= $cplugin->getContent();
	    	    $box['output'] 		= str_replace("class=\"pn-block pn-blockposition- pn-bkey- pn-bid-\"", "", $box['output']);
	    	    
	    	    // Assign plugin that is on left side of actual object
	    	    $box['previous'] 	= (int)$previous;
	    	    $box['permanent'] 	= false; //$meta['permanent'];
	    	    $box['width'] 		= 33*(int)$box['size'];
	    	    $next[$previous] 	= $box['id'];
	    	    $previous 			= $box['id'];
	    	    if ($size + $box['size'] <= 3) {
	    	    	$box['newline'] = false;
	    	    } else {
	    	    	$box['newline'] = true;
	    	    	$size = 0;
	    	    } 
	    	    $size += $box['size'];
	    	    $res[] = $box;
	  		}
/*	  		
	  	  	// Load file if not loaded yet
	  	  	Loader::includeOnce('modules/UserDashBoard/plugins/plugin.'.$plugin['plugin'].'.inc');
	  	  	$metadata = 'box_'.$plugin['plugin'].'_getMetaData';
	  	  	$content  = 'box_'.$plugin['plugin'].'_getOutput';
	  	  	if (function_exists($metadata) && ($metadata() != false)) {
    	  	  	$meta     = $metadata();
    	  	  	$content  = $content();
    		    $plugin['title']  = $meta['title'];
    		    $plugin['size']   = $meta['size'];
    		    $plugin['output'] = $content;
    		    // Assign plugin that is on left side of actual object
    		    $plugin['previous'] = (int)$previous;
    		    $plugin['permanent'] = $meta['permanent'];
    		    $plugin['width'] = 33*(int)$plugin['size'];
    		    $next[$previous] = $plugin['id'];
    		    $previous = $plugin['id'];
    		    if ($size + $plugin['size'] <= 3) {
    		    	$plugin['newline'] = false;
    		    } else {
    		    	$plugin['newline'] = true;
    		    	$size = 0;
    		    } 
    		    $size += $plugin['size'];
    		    $res[] = $plugin;
            }
*/            
		}
		// assign right item sort order information now
		$res2 = array();
		foreach ($res as $box) {
		  	$box['next'] = (int)$next[$box['id']];
		  	$res2[] = $box;
		}
		return $res2;
	}
}

function UserDashBoard_userapi_add($args)
{
  	// Process parameters
  	$uid 	= (int)$args['uid'];
  	$pos 	= (int)$args['pos'];
  	$block 	= ($args['block']=="") ? 0 :(int)$args['block'];
  	$page 	= ($args['page']=="") ? 0 :(int)$args['page'];
  	$box 	= $args['box'];

	// Check first if box was already added before
	$where = "udb_uid=$uid AND udb_block=$block AND udb_plugin = '".DataUtil::formatForStore($box)."'";
	$result = DBUtil::selectObjectCount('userdashboard_boxes',$where);
	if ($result != 0) {
	  	LogUtil::registerError(_BOXES_BOX_ALREADY_ADDED);
	  	return false;
	}

	// Construct object
	$obj = array (
			'uid'	=> $uid,
			'plugin'=> $box,
			'pos'	=> $pos,
			'page' 	=> $page,
			'block' => $block
		);

	// Add object
	$result = DBUtil::insertObject($obj,'userdashboard_boxes');
	if (!$result) {
	  	return false;
	} else {
	  	// Clean Order and return success
	  	UserDashBoard_userapi_cleanOrder($obj);
	  	return true;
	}
}

function UserDashBoard_userapi_cleanOrder($args)
{
  	// Process parameters
  	$uid = (int)$args['uid'];
	$page = (int)$args['page'];
	$block = (int)$args['block'];

	// Get all objects
	$where = "udb_uid = $uid AND udb_block=$block";
	$order = 'udb_pos ASC, udb_id DESC';
	$objects = DBUtil::selectObjectArray('userdashboard_boxes',$where,$order);
	$c = 0;
	foreach ($objects as $obj) {
	  	$c++;
	  	if ($obj['pos'] != $c) {
	  	  	$obj['pos'] = $c;
		    DBUtil::updateObject($obj,'userdashboard_boxes');
		}
	}
	return true;
}

function UserDashBoard_userapi_switch($args)
{
  	// Get parameters
  	$box1 = (int) $args['box1'];
  	$box2 = (int) $args['box2'];
  	$uid  = (int) $args['uid'];
	// Little validation check
  	if (($box1 == 0) || ($box2 == 0)) {
	    return false;
	}

	// Get Objects
	$obj1 = DBUtil::selectObjectByID('userdashboard_boxes',$box1);
	$obj2 = DBUtil::selectObjectByID('userdashboard_boxes',$box2);

	// Security Check
	if (($obj1['uid'] != $obj2['uid']) || ($obj1['uid'] != $uid)) {
	  	return false;
	}
	// Switch position numbers
	$dummy = $obj1['pos'];
	$obj1['pos'] = $obj2['pos'];
	$obj2['pos'] = $dummy;

	// Update Objects
	$result = (DBUtil::updateObject($obj1,'userdashboard_boxes') && DBUtil::updateObject($obj2,'userdashboard_boxes'));
	$obj1 = DBUtil::selectObjectByID('userdashboard_boxes',$box1);
	$obj2 = DBUtil::selectObjectByID('userdashboard_boxes',$box2);
	
	// Return result
	return $result;
}

function UserDashBoard_userapi_getOpenPlugins($args)
{
	$plugins = UserDashBoard_userapi_getPlugins();
	$uid = $args['uid'];
	$onlySingle = isset($args['onlySingle']) ? $args['onlySingle'] : false;
	
	if (!isset($args['uid']) || $uid=="") {
		$uid = pnUserGetVar('uid');
	}
	$where = "udb_uid = $uid";
	$boxes = DBUtil::selectObjectArray('userdashboard_boxes',$where);
  	
	// Filter out used templates
  	$results = array();	
	foreach ($plugins as $plugin) {
		$found = false;
	    foreach ($boxes as $box) {
		  	if ($box['plugin'] == $plugin['name']) {
				$found = true;
			} elseif ($onlySingle && $plugin['size'] != 1) {
				$found = true;
			}
		}
		if (!$found) {
		  	$results[] = $plugin;
		}
	}		
	return $results;
}

function UserDashBoard_userapi_del($args)
{
  	// Get Parameters
  	$box = (string) $args['box'];
  	$uid = (int) $args['uid'];
  	$id  = (int) $args['id'];

  	// Security Check
  	if (!isset($box) || ($box == '') || ($id == 0)) {
	    return false;
	}

	// Get Object
	$obj = DBUtil::selectObjectByID('userdashboard_boxes',$id);
	if (!$obj) {
	  	return false;
	} else {
	  	if (($obj['plugin'] != $box) || ($uid != $obj['uid'])) {
		    return false;
		} else {
/*			
			$plugin = DBUtil::selectObjectByID('userdashboard_plugins',$box,'name');
			if (!$plugin) {
				return false;
			}
	  	  	Loader::includeOnce($plugin['file']);
    		$class=$plugin['name'];
    		$plugin = new $class();
    		$pName = $plugin->name();	  	  	
	  	  	
	  	  	$metadata = 'box_'.$obj['plugin'].'_getMetaData';
	  	  	$meta     = $metadata();

	  	  	if ($meta['permanent']) {
	  	  	  	LogUtil::registerError(_BOXES_PERMAMENT_BOX_NODEL);
				return false;
			}
*/
		  	$result = DBUtil::deleteObject($obj,'userdashboard_boxes');
		  	return $result;
		}
	}
}

function UserDashBoard_userapi_includeBlock($args)
{

	$modname 	= $args['module'];
	$block 		= $args['blockname'];
	$blockinfo 	= $args['blockinfo'];
	
	if (empty($modname) || $modname == 'Core') {
        $modname = 'Legacy';
    }
    global $blocks_modules;

    pnBlockLoad($modname, $block);

    $displayfunc = "{$modname}_{$block}block_display";

    if (function_exists($displayfunc)) {
        // New-style blocks
        return $displayfunc($blockinfo);
    } else {
        return "";
    }	
	
	
  	//$render = pnRender::getInstance('UserDashBoard');
    //$render->caching = false;
  	//$render->assign($args);
  	//prayer("<br><br><br><br><br><br><br><br>");
  	//prayer($args);
  	//return pnBlockShow ($args['module'],$args['blockname'],$args['blockinfo']);	
  	//return $render->fetch('UserDashBoard_plugin_blockcall.htm');

}

function UserDashBoard_userapi_useTemplate($args)
{
  	$file = $args['file'];
  	if (!isset($file) || ($file == '')) {
	    return false;
	}
	$render = pnRender::getInstance('UserDashBoard');
	if (isset($args['vars'])) {
	  	$render->assign($args['vars']);
	}
	$file = 'plugins/plugin.'.$file.'.htm';
	$render->assign('file', $file);
	return $render->fetch('UserDashBoard_plugin_template.htm');
}

function UserDashBoard_userapi_reset($args)
{
	$where = "udb_uid = -1"; 
	$defaultboxes = DBUtil::selectObjectArray('userdashboard_boxes',$where);

 	// get all boxes for a user and delete these boxes
  	$old = UserDashBoard_userapi_get();
  	foreach ($old as $o) {
	    $result = DBUtil::deleteObject($o,'userdashboard_boxes');
	    if (!$result) {
		  	return false;
		}
	}
	// iInsert new objects
	foreach ($defaultboxes as $dbox) {
	  	$args = array (
	  		'box' 	=> $dbox['plugin'],
	  		'page' 	=> $dbox['page'],
	  		'block' => $dbox['block'],
	  		'pos' 	=> $dbox['pos'],
	  		'uid' 	=> pnUserGetVar('uid')
		  );
	  	$result = UserDashBoard_userapi_add($args);
	  	if (!$result) {
		    return false;
		}
	}

	// Return success
  	return true;
}

function UserDashBoard_userapi_getBoxCode($args) {
	$box 		= $args['box'];
	$allowEdit 	= $args['allowEdit'];
	$blockid	= isset($args['blockid']) ? $args['blockid'] : 1;
	$admin		= isset($args['admin']) ? $args['admin'] : false;
	
  	if (!isset($box) || (!($box['id'] > 0))) {
	    return false;
	}
  	$render = pnRender::getInstance('UserDashBoard',false);
  	$render->assign('box',$box);
  	$render->assign('blockid',$blockid);
  	$render->assign('allowEdit',$allowEdit);
  	$render->assign('admin',$admin);
  	return $render->fetch('UserDashBoard_user_singlebox.htm');
}