<?php
/**
 * the main user function
 *
 * @return       output
 */
function UserDashBoard_user_main()
{
	$settings = pnModGetVar('UserDashBoard');
	if (!pnUserLoggedIn()) {
	    $module = $settings['ExternalStartpage'];
	    $type   = ($settings['ExternalStarttype'] == '') ? 'user' : $settings['ExternalStarttype'];
	    $func   = ($settings['ExternalStartfunc'] == '') ? 'main' : $settings['ExternalStartfunc'];	    
	    $args   = explode(',', $settings['ExternalStartargs']);
	    $arguments = array();
	    foreach ($args as $arg) {
	        if (!empty($arg)) {
	            $argument = explode('=', $arg);
	            $arguments[$argument[0]] = $argument[1];
	            pnQueryStringSetVar($argument[0], $argument[1]);
	        }
	    }		
	  	return pnModFuncExec($module, $type, $func);
	} else {
		if ($settings['AllowCustomizing'] == 1) {
			
			$size = 0;

			// Create output
			$render = pnRender::getInstance('UserDashBoard',false);
		
			// Get boxes and templates
			//$templates 	= pnModAPIFunc('UserDashBoard','user','getTemplates');
			$boxes 		= pnModAPIFunc('UserDashBoard','user','get',array('block'=>0));
			//$templates 	= pnModAPIFunc('UserDashBoard','user','filterTemplates',array ('boxes' => $boxes, 'templates' => $templates));
	
			// If there are no boxes defined for users reset to default_config
			if (!$boxes || !(count($boxes) > 0)) {

				$authid = SecurityUtil::generateAuthKey();
				$result = pnModAPIFunc('UserDashBoard','user','reset');
			  	//return pnRedirect(pnModURL('UserDashBoard','user','reset',array('authid' => $authid)));
			}
		
			for ($i=0; $i < count($boxes); $i++) {
			  	$result = pnModAPIFunc('UserDashBoard','user','getBoxCode',array('box'		 =>$boxes[$i],
			  																	 'allowEdit' => false));
				$boxes[$i]['boxcode'] = $result;
			}
	
			// Assign to template
			//$render->assign('templates',$templates);
			$render->assign('boxes', 		$boxes);
			$render->assign('boxes_uname',	pnUserGetVar('uname'));
			$render->assign('authid',		SecurityUtil::generateAuthKey());
		
			// Return output
			return $render->fetch('UserDashBoard_user_main.htm');
		} else {
		    $module = $settings['InternalStartpage'];
		    $type   = ($settings['InternalStarttype'] == '') ? 'user' : $settings['InternalStarttype'];
		    $func   = ($settings['InternalStartfunc'] == '') ? 'main' : $settings['InternalStartfunc']; 
		    $args   = explode(',', $settings['InternalStartargs']);
		    $arguments = array();
		    foreach ($args as $arg) {
		        if (!empty($arg)) {
		            $argument = explode('=', $arg);
		            $arguments[$argument[0]] = $argument[1];
		            pnQueryStringSetVar($argument[0], $argument[1]);
		        }
		    }		
		  	return pnModFuncExec($module, $type, $func);			
		}
	}
}

function UserDashBoard_user_create()
{
  	// No Access for guests
	if (!pnUserLoggedIn()) {
	  	return LogUtil::registerPermissionError();
	}

	// Create output
	$render = pnRender::getInstance('UserDashBoard',false);

	// Get boxes and templates
	$boxes 		= pnModAPIFunc('UserDashBoard','user','get',array('block'=>0));
	$plugins 	= pnModAPIFunc('UserDashBoard','user','getOpenPlugins');

	// If there are no boxes defined for users reset to default_config
	if (!$boxes || !(count($boxes) > 0)) {
	  	$authid = SecurityUtil::generateAuthKey();
	  	return pnRedirect(pnModURL('UserDashBoard','user','reset',array('authid' => $authid)));
	}

	for ($i=0; $i < count($boxes); $i++) {
	  	$result = pnModAPIFunc('UserDashBoard','user','getBoxCode',array('box'		 =>$boxes[$i],
	  																	 'allowEdit' => true));
		$boxes[$i]['boxcode'] = $result;
	}

	// Assign to template
	$render->assign('plugins',		$plugins);
	$render->assign('boxes', 		$boxes);
	$render->assign('boxes_uname',	pnUserGetVar('uname'));
	$render->assign('authid',		SecurityUtil::generateAuthKey());

	// Return output
	return $render->fetch('UserDashBoard_user_main.htm');
}

function UserDashBoard_user_add()
{
  	// Get Parameter and do a little security check
	$box 	= FormUtil::getPassedValue('box');
	$block 	= FormUtil::getPassedValue('block',0);
	$page 	= FormUtil::getPassedValue('page',1);
	$pos 	= (int)FormUtil::getPassedValue('pos');
	if (!isset($box) || ($box == '') || !pnUserLoggedIn()) {
	    return pnRedirect(pnModURL('UserDashBoard'));
	}

	// Add new box
	$uid = pnUserGetVar('uid');

	$result = pnModAPIFunc('UserDashBoard','user','add',array('uid' => $uid, 
															  'box' => $box, 
															  'pos' => $pos,
															  'block' => $block,
															  'page' => $page));
	if (!$result) {
	  	LogUtil::registerError(_BOXES_ADD_ERROR);
	} else {
	  	LogUtil::registerStatus(_BOXES_BOX_ADDED);
	}

	// Clean order values
  	pnModAPIFunc('UserDashBoard','user','cleanOrder',array('uid' => $uid,
  														   'block' => $block,
														   'page' => $page));

	// Redirect to main page
	return pnRedirect(pnModURL('UserDashBoard','user','create'));
}

function UserDashBoard_user_switch()
{
  	$box1 = (int) FormUtil::getPassedValue('box1');
  	$box2 = (int) FormUtil::getPassedValue('box2');
  	$uid  	= (int) FormUtil::getPassedValue('uid',0);
	if ($uid <> -1) {
		$uid = pnUserGetVar('uid');
	}
  	
	// Little validation check
  	if (($box1 == 0) || ($box2 == 0) || !pnUserLoggedIn()) {
	    return pnRedirect(pnModURL('UserDashBoard'));
	}

	// Switch the boxes
	$result = pnModAPIFunc('UserDashBoard','user','switch',array('box1' => $box1, 'box2' => $box2, 'uid' => $uid));
	if (!$result) {
	  	LogUtil::registerError('Box could not be moved! An error occured.');
	}

	// Redirect
	if ($uid == -1) {
		return pnRedirect(pnModURL('UserDashBoard','admin','DefaultConfig'));
	} else {
		return pnRedirect(pnModURL('UserDashBoard','user','create'));
	}	
}

function UserDashBoard_user_del()
{
  	$box 	= (string) FormUtil::getPassedValue('box');
  	$id  	= (int) FormUtil::getPassedValue('id');
  	$uid  	= (int) FormUtil::getPassedValue('uid',0);
	if ($uid <> -1) {
		$uid = pnUserGetVar('uid');
	}
  	// Call API to delete box
  	$result = pnModAPIFunc('UserDashBoard','user','del',array('box' => $box, 'id' => $id, 'uid' => $uid));
  	if (!$result) {
	    LogUtil::registerError(__('Box could not be deleted! An error occured.'));
	}

	// Redirect
	if ($uid == -1) {
		return pnRedirect(pnModURL('UserDashBoard','admin','DefaultConfig'));
	} else {
		return pnRedirect(pnModURL('UserDashBoard','user','create'));
	}
}

function UserDashBoard_user_reset()
{
  	// Check authid and make little security check
  	if (!pnUserLoggedIn()
//	  || !(SecurityUtil::confirmAuthKey())
	) {
	    LogUtil::registerError(_BOXES_AUTH_OR_PERM_ERROR);
	    return pnRedirect(pnModURL('UserDashBoard'));
	}

	$result = pnModAPIFunc('UserDashBoard','user','reset');
	if ($result) {
	  	LogUtil::registerStatus(_BOXES_RESET_DONE);
	} else {
	  	LogUtil::registerError(_BOXES_RESET_ERROR);
	}

	// Redirect to main boxes page
	return pnRedirect(pnModURL('UserDashBoard','user','create'));
}