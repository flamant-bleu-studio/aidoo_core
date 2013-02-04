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

class GalerieImage_Lib_Manager
{
	const ID_TYPE_GALERIE = 1;
	const NAME_TYPE_GALERIE = "galerie";
	const PERMISSION_TYPE_GALERIE = "mod_galeriePhoto";
	
	const ID_TYPE_DIAPORAMA = 2;
	const NAME_TYPE_DIAPORAMA = "diaporama";
	const PERMISSION_TYPE_DIAPORAMA = "mod_diaporama";
	
	public static function getIdType($name)
	{
		if ($name == "galerie")
			return self::ID_TYPE_GALERIE;
		else if ($name == "diaporama")
			return self::ID_TYPE_DIAPORAMA;
		else
			return null;
	}
	
	public static function getNameType($id)
	{
		if ($id == self::ID_TYPE_GALERIE)
			return self::NAME_TYPE_GALERIE;
		else if ($id == self::ID_TYPE_DIAPORAMA)
			return self::NAME_TYPE_DIAPORAMA;
		else
			return null;
	}
	
	public static function getPermissionType($value)
	{
		if( is_numeric($value) )
		{
			if ($value == self::ID_TYPE_GALERIE)
			
				return self::PERMISSION_TYPE_GALERIE;
			else if ($value == self::ID_TYPE_DIAPORAMA)
				return self::PERMISSION_TYPE_DIAPORAMA;
			else
				return null;
		}
		else
		{
			if ($value == self::NAME_TYPE_GALERIE)
				return self::PERMISSION_TYPE_GALERIE;
			else if ($value == self::NAME_TYPE_DIAPORAMA)
				return self::PERMISSION_TYPE_DIAPORAMA;
			else
				return null;
		}
	}
	
	public static function unsetInactiveImage($images) {

		if( count($images) > 0 ) {
			foreach( $images as $key => $image ) {
				if(!$image->isPermanent)  {
					$date_start = strtotime($image->date_start);
					$date_end = strtotime($image->date_end);
					
					$now = time();
					if($date_start > $now || $date_end < $now)
						unset($images[$key]);
				}
			}
		}
		
		return $images;
	}
	
	public static function createArrayImages($order_image, $datas)
	{
		$return = array();
		
		if( count($datas) > 0 )
		{
			foreach ( $order_image as $id )
			{
				$image = $datas[$id];
				
				$temp = array();
				$temp["isPermanent"] = ($image["isPermanent"] == "true") ? 1 : 0;
				
				if( $temp["isPermanent"] === 0 )
				{
					if($image["date_start"] != "")
						$temp["date_start"] = CMS_Application_Tools::_convertDateTimePickerToUs($image["date_start"]);
					else
						$temp["date_start"] = null;
					
					if($image["date_end"] != "")
						$temp["date_end"] = CMS_Application_Tools::_convertDateTimePickerToUs($image["date_end"]);
					else 
						$temp["date_end"] = null;
				}
				
				$temp["description"]= stripslashes($image["description"]);
				$temp["path"] 		= $image["path"];
				$temp["path_thumb"] = $image["path_thumb"];
				$temp["path2"] 		= $image["path2"];
				$temp["path_thumb2"] = $image["path_thumb2"];
				$temp["bg_color"] 	= $image["bg_color_image"];
				
				$temp["datas"] 		= json_encode(array(
					"height" 		=> $image["height"], 
					"width" 		=> $image["width"], 
					"thumb_height"	=> $image["thumb_height"], 
					"thumb_width" 	=> $image["thumb_width"],
					"addLink"		=> ($image["addLink"] == "true") ? 1 : 0,
					"window"		=> ($image["window"] == "true") ? 1 : 0,
					"link_type"		=> (int) $image["link_type"],
					"external_page" => $image["external_page"],
					"page_link" 	=> $image["page_link"]
				));
				
				array_push($return, $temp);
			}
		}
		
		return $return;
	}
}