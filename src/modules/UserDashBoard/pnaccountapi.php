<?php

/**
 * Return an array of items to show in the your account panel
 *
 * @return   array   
 */
function UserDashBoard_accountapi_getall($args)
{
     
 	$items[] = array('url'     => pnModURL('UserDashBoard', 'user', 'create'),
                     'module'  => 'UserDashBoard',
                     'title'   => __('Configure your start page'),
                     'icon'    => 'UserDashBoard.png');
 
    // Return the items
    return $items; 
}

