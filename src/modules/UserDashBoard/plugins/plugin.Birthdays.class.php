<?php
Loader::requireOnce('modules/UserDashBoard/plugins/interface.plugin.class.php');
class PluginBirthdays implements UserDashBoardPluginInterface
{
	public function name() {
		return 'PluginBirthdays';
	}
	public function title() {
		return __('Geburtstagskinder');
	}
	public function size() {
		return 1;
	}
	
	public function getContent() {
	  	$output = pnModAPIFunc('UserDashBoard',
	  						   'user',
	  						   'includeBlock',
	  						   array('module' 	 => 'MyProfile', 
	  						   		 'blockname' => 'birthday'));
	  	return $output;		
	}
}
