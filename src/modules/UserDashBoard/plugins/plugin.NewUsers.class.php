<?php
Loader::requireOnce('modules/UserDashBoard/plugins/interface.plugin.class.php');
class PluginNewUsers implements UserDashBoardPluginInterface
{
	public function name() {
		return 'PluginNewUsers';
	}
	public function title() {
		return __('Neue Mitglieder');
	}
	public function size() {
		return 1;
	}
	
	public function getContent() {
	  	$output = pnModAPIFunc('UserDashBoard',
	  						   'user',
	  						   'includeBlock',
	  						   array('module' 	 => 'MyProfile', 
	  						   		 'blockname' => 'newbies'));
	  	return $output;		
	}
}
