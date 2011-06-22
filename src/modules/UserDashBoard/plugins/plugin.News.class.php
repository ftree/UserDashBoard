<?php
Loader::requireOnce('modules/UserDashBoard/plugins/interface.plugin.class.php');
class PluginNews implements UserDashBoardPluginInterface
{
	public function name() {
		return 'PluginNews';
	}
	public function title() {
		return __('News');
	}
	public function size() {
		return 2;
	}
	
	public function getContent() {
		
		$width = $params['width'];
		$order = 'pn_cr_date DESC';
		$where = 'pn_published_status = 0';
		pnModDBInfoLoad('News');
	  	$result = DBUtil::selectObjectArray('news',$where,$order,0,7);
	  	$content = '
		  <div class="pn-block" style="width: '.$width.'px;">
		  ';
		foreach ($result as $s) {
		  	$s['title'] = DataUtil::formatForDisplayHTML($s['title']);
		  	$content.='<li>&#187; <a title="'.str_replace('"','\'',$s['title']).' von: '.$s['informant'].'" href="'.pnModURL('News','user','display',array('sid' => $s['sid'])).'">'.$this->ShortenText2($s['title'],40).'</a></li>';
		}
		$content.='<br /><a href="'.pnModURL('News','user','main').'">Alle Neuigkeiten anzeigen</a><br /><br />
		  </div>
		  ';
	
	  	return $content;
	}
	
	function ShortenText2($text,$chars) {
	    $chars = $chars;
		if (strlen($text) <= $chars) return $text;
	    $text = $text." ";
	    $text = substr($text,0,$chars);
	    $text = substr($text,0,strrpos($text,' '));
	    $text = $text."...";
	    return $text;
	}
}
