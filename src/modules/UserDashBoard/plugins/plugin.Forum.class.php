<?php
Loader::requireOnce('modules/UserDashBoard/plugins/interface.plugin.class.php');
class PluginForum implements UserDashBoardPluginInterface
{
	public function name() {
		return 'PluginForum';
	}
	public function title() {
		return __('Forum');
	}
	public function size() {
		return 1;
	}
	
	public function getContent() {
	  	$output= pnModAPIFunc('UserDashBoard',
	  						  'user',
	  						  'includeBlock',
	  						  array('module' 	=> 'Dizkus', 
	  						  	    'blockname' => 'center'));
	  	return $output;		
	}
}
