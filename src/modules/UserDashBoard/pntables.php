<?php

/**
 * Populate tables array for the module
 *
 * @return       array       The table information.
 */
function UserDashBoard_pntables()
{
    // Initialise table array
    $tables = array();

    $tables['userdashboard_boxes'] = DBUtil::getLimitedTablename('userdashboard_boxes');

    // Columns for tables
    $tables['userdashboard_boxes_column'] = array (
    			'id'					=> 'udb_id',
    			'uid'					=> 'udb_uid',
    			'pos'					=> 'udb_pos',
    			'block'					=> 'udb_block',
    			'plugin'				=> 'udb_plugin',
    			'page'					=> 'udb_page'
    			);
    // Add Primary Index
    $tables['userdashboard_boxes_primary_key_column'] = 'id';

    // column definition        			
    $tables['userdashboard_boxes_column_def'] = array (
    			'id'					=> "I AUTOINCREMENT PRIMARY",
    			'uid'					=> "I NOTNULL DEFAULT 0",
    			'pos'					=> "I NOTNULL DEFAULT 0",
    			'block'					=> "I NOTNULL DEFAULT 0",
    			'plugin'				=> "C(125) NOTNULL DEFAULT ''",
    			'page'					=> "I NOTNULL DEFAULT 1"
    			);

    // Add Index
	$tables['userdashboard_boxesd_column_idx'] = array('useridindex' => array('uid'));    			
    			
	// *********************************************************************************
    $tables['userdashboard_plugins'] = DBUtil::getLimitedTablename('userdashboard_plugins');

    // Columns for tables
    $tables['userdashboard_plugins_column'] = array (
    			'id'					=> 'udp_id',
    			'active'				=> 'udp_active',
    			'name'					=> 'udp_name',
    			'title'					=> 'udp_title',
    			'size'					=> 'udp_size',
				'file'					=> 'udp_file',
    			'ajax'					=> 'udp_ajax'
    			);	
    
    // Add Primary Index
    $tables['userdashboard_plugins_primary_key_column'] = 'id';
    	
    // column definition        			
    $tables['userdashboard_plugins_column_def'] = array (
    			'id'					=> "I AUTOINCREMENT PRIMARY	",
    			'active'				=> "I NOTNULL DEFAULT 0		",
    			'name'					=> "C(30)  NOTNULL 			",
    			'title'					=> "C(250) NOTNULL 			",
    			'size'					=> "I NOTNULL DEFAULT 1		",
    			'file'					=> "C(250) NOTNULL 			",
    			'ajax'					=> "I NOTNULL DEFAULT 0		"
    			);    
    			
    // Add Index
	// $tables['userdashboard_plugins_column_idx'] = array('useridindex' => array('uid')); 
	    
	// Return table information
	return $tables;
}
