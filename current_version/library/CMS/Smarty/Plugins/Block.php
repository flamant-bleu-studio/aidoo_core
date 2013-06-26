<?php

/**
 * CMS Aïdoo
 *
 * Copyright (C) 2013  Flamant Bleu Studio
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

class CMS_Smarty_Plugins_Block {
 
	public static function dynamic($param, $content, Smarty_Internal_Template $smarty, &$repeat) {
    	return $content;
	}
	
	public static function t($params, $text, Smarty_Internal_Template $smarty, &$repeat)
	{		
		$text = stripslashes($text);
		
		// set escape mode
		if (isset($params['escape'])) {
			$escape = $params['escape'];
			unset($params['escape']);
		}
		
		// set plural version
		if (isset($params['plural'])) {
			$plural = $params['plural'];
			unset($params['plural']);
			
			// set count
			if (isset($params['count'])) {
				$count = $params['count'];
				unset($params['count']);
			}
		}
		
		$t = Zend_Registry::get('translate');

		if (isset($count) && isset($plural)) {
			$text = $t->translate(array($text, $plural, $count));
		} else { // use normal*/
			$text = $t->translate($text);
		}
	
		// run strarg if there are parameters
		if (count($params)) {
			$text = strarg($text, $params);
		}
	
		if (!isset($escape) || $escape == 'html') { // html escape, default
		   $text = nl2br(htmlspecialchars($text));
	   } elseif (isset($escape) && ($escape == 'quote')) { // quote escape
		   $text = str_replace("'", "\'", $text);
	   } elseif (isset($escape) && ($escape == 'dquote')) { // double quote escape
		   $text = str_replace('"', '\"', $text);
	   } elseif (isset($escape) && ($escape == 'javascript' || $escape == 'js')) { // javascript escape
		   $text = str_replace('\'','\\\'',stripslashes($text));
	   }
	
		return $text;
	}
	
	public static function appendScript($params, $text, Smarty_Internal_Template $smarty, &$repeat)
	{		
		$text = stripslashes($text);
		
		$type = $params['type'];
		unset($params['type']);
		
		$append = CMS_Application_ProcessLayout::getInstance();
		
		if($type == "css")
		{
			$append->appendCssScript($text);
		}
		else if($type == "js")
		{
			$append->appendJsScript($text);
		}

	}

}

function strarg($str)
{
	$tr = array();
	$p = 0;

	for ($i=1; $i < func_num_args(); $i++) {
		$arg = func_get_arg($i);
		
		if (is_array($arg)) {
			foreach ($arg as $aarg) {
				$tr['%'.++$p] = $aarg;
			}
		} else {
			$tr['%'.++$p] = $arg;
		}
	}
	
	return strtr($str, $tr);
}