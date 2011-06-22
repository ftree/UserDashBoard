<?php
/**
 * initialise block
 *
 */
function UserDashBoard_Centerblock_init()
{
    //pnSecAddSchema('MediaAttach:TorD_UserInfo:', '::');
}

/**
 * get information on block
 *
 * @return       array       The block information
 */
function UserDashBoard_Centerblock_info()
{
    // block informations
    return array('module'         => 'UserDashBoard',
                 'text_type'      => 'Centerblock',
                 'text_type_long' => __('Customizable Centerblock'),
                 'allow_multiple' => true,
                 'form_content'   => false,
                 'form_refresh'   => false,
                 'show_preview'   => false,
                 'admin_tableless' => true);
}

/**
 * display block
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the rendered bock
 */
function UserDashBoard_Centerblock_display($blockinfo)
{
	// Show only if Modul is active
    if (!pnModAvailable('UserDashBoard')) {
        return false;
    }

    // No Access for guests
	if (!pnUserLoggedIn()) {
	  	    return false;
	}
	
	//prayer($blockinfo);
	$module = pnModGetName();
	$type = FormUtil::getPassedValue('type','user');
	$func = FormUtil::getPassedValue('func','');

	$edit = ($module=='UserDashBoard' && $type=='user' && $func=='create');
	$blockid = $blockinfo['bid'];
	
	// Create output
	$render = pnRender::getInstance('UserDashBoard',false);
	// Get boxes and templates
	$boxes 	= pnModAPIFunc('UserDashBoard','user','get',array('block'=>$blockid));
	
	if ($edit) {
		$plugins 	= pnModAPIFunc('UserDashBoard','user','getOpenPlugins', array('onlySingle' => false));
	} else {
		$plugins 	= "";
	}
	for ($i=0; $i < count($boxes); $i++) {
	  	$block 				= $blockinfo;
		if ($edit) {
	  		$result = pnModAPIFunc('UserDashBoard','user','getBoxCode',array('box'		 =>$boxes[$i],
	  																		 'allowEdit' => $edit,
	  																		 'blockid'   => $blockid));
			$block['content'] 		= $result;	  	
			$block['title'] 		= "";
		} else {
	  		$block['content'] 		= $boxes[$i]['output'];
	  		$block['title'] 		= $boxes[$i]['title'];
	    	$block['bid'] 			= $blockinfo['bid'].'-'.$boxes[$i]['id'];	  		
		}
	  	
		$boxes[$i]['boxcode'] = pnBlockThemeBlock($block); //$result;
	}


	// Assign to template
	$render->assign('templates',	$plugins);
	$render->assign('allowEdit', 	$edit);
	$render->assign('boxes', 		$boxes);
	$render->assign('boxes_uname',	pnUserGetVar('uname'));
	$render->assign('block', 		$blockid);
	$render->assign('authid',		SecurityUtil::generateAuthKey());

	return $render->fetch('UserDashBoard_block_center.htm');	
	
}

