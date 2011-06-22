<?php
/**
 * @package      lobby
 * @version      $Id $
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2009
 * @license      no public license - read license.txt in doc directory for details
 */

/**
 * @param	$mode	(0=short, 1=long, 2=extended)
 * @return 	date
 */
function smarty_modifier_date($datetime=null, $mode=0)
{
	$ts = strtotime($datetime);
	// If today only display hour and minute and only day otherwise
	if ($ts > strtotime(date("Y-m-d",time()))) {
	  	return date("H:i",$ts)." "._LOBBY_OCLOCK;
	} else {
	  	if ($mode == 0){
			return date("d. M",$ts);
		} else if ($mode == 1){
			return date("d. M 'y",$ts);
		} else if ($mode == 2){
			return date("d. M 'y H:i",$ts);
		}
	}
}
