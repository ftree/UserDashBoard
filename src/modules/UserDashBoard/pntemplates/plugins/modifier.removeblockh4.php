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
function smarty_modifier_removeblockh4($content, $mode=0)
{
  $content = str_replace('<h4>','<span class="boxes_h4">',$content);
  $content = str_replace('</h4>','</span>',$content);
  return $content;
  
}
