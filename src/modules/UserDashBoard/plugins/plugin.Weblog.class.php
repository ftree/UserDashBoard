<?php
Loader::requireOnce('modules/UserDashBoard/plugins/interface.plugin.class.php');
class PluginWeblog implements UserDashBoardPluginInterface
{
	public function name() {
		return 'PluginWeblog';
	}
	public function title() {
		return __('Weblog');
	}
	public function size() {
		return 1;
	}
	
	public function getContent() {
	  	$output= pnModAPIFunc('UserDashBoard',
	  						  'user',
	  						  'includeBlock',
	  						  array('module' 	=> 'pnWebLog', 
	  						  	    'blockname' => 'latest'));
	  	return $output;		
	}
}
