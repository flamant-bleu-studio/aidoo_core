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

class CMS_Facebook_Tools
{
	/**
	 * Récupération du nombre de Facebook Comments
	 * @param string $url
	 * @param string $format (xml or json) (default : json)
	 */
	public static function getCountCommentsbox($url, $format = "json")
	{
    	$fb_stats 	= file_get_contents('http://api.facebook.com/restserver.php?format='. $format .'&method=links.getStats&urls='. $url, true);
    	$datas 		= reset(json_decode($fb_stats, true));
    	
    	return $datas ? ($datas['commentsbox_count'] ? $datas['commentsbox_count'] : 0) : 0;
	}
}