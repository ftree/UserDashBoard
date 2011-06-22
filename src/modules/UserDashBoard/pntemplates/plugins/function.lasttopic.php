<?php
/**
 * @package      lobby
 * @version      $Id $
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      no public license - read license.txt in doc directory for details
 */

function smarty_function_lasttopic($params, &$smarty)
{
    $params['numrows'] = pnModGetVar('lobby','postsperpage');
	$numrows    = (int)$params['numrows'];
	$replies    = (int)$params['replies'] + 1; // topic is not counted as reply
	$id         = (int)$params['id'];
	$fid        = (int)$params['fid'];
	$topic      = (int)$params['topic'];
	$lasttopic  = (int)$params['lasttopic'];

	// Paging only needed for $replies > $numrows
	if ($replies > $numrows) {
		$page    = $replies/$numrows;
		$page    = (int)$page;
		// now we need the number of the first posting at this page for the pager...
		$res = $page * $numrows;
		// plus 1 because we will start with 1
		$res++;
	}
	$args = array (
			'id'         => $id,
			'fid'        => $fid,
			'topic'      => $topic,
			'do'         => 'forum',
			'lobbypager' => $res,
			'lasttopic'	=> $lasttopic // only for visited links display, managed via css
		);
	$url = pnModURL('lobby','user','group',$args);
	return $url;
}
