<?php

 /**
  * initialise the module
  *
  */
function UserDashBoard_init() {
  	// Create tables
  	$tables = array (
  		'userdashboard_boxes',
  		'userdashboard_plugins'
		  );

	foreach ($tables as $table) {
		if (!DBUtil::createTable($table)) {
		  	return false;
		}
	}
	pnModSetVar('UserDashBoard', 'AllowCustomizing', 1);
	
	// Return success
	return true;
}

/**
 * delete the module
 *
 */
function UserDashBoard_delete() {
  	// Drop tables
  	$tables = array (
  		'userdashboard_boxes',
  		'userdashboard_plugins'
		  );
	foreach ($tables as $table) {
		if (!DBUtil::dropTable($table)) {
		  	return false;
		}
	}
	// Delete module variables if there are any
	pnModDelVar('UserDashBoard');

	// Return success
	return true;
}
