<?php
Loader::requireOnce('modules/UserDashBoard/plugins/interface.plugin.class.php');
class PluginUserPictures implements UserDashBoardPluginInterface
{
	public function name() {
		return 'PluginUserPictures';
	}
	public function title() {
		return __('Neueste Bilder in den Benutzergalerien');
	}
	public function size() {
		return 3;
	}
	
	public function getContent() {
		$output = pnModAPIFunc('UserDashBoard',
							   'user',
							   'includeBlock',
							   array('module' 	 => 'UserPictures', 
							   		 'blockname' => 'latestsmall'));
	  	return $output;		
	}
}
