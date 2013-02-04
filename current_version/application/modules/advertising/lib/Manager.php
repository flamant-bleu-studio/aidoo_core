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

class Advertising_Lib_Manager
{
	public static function createArrayAdvert($datas)
	{
		$return = array();
		
		$adverts = json_decode($datas, true);
		
		if( count($adverts) > 0 )
		{
			foreach ( $adverts as $key => $advert )
			{
				$temp = array();
				$temp["image_path"] = $advert["image_path"];
				$temp["image_path_thumb"] = $advert["image_path_thumb"];
				$temp["image_width"] = $advert["image_width"];
				$temp["image_height"] = $advert["image_height"];
				$temp["image_thumb_width"] = $advert["image_thumb_width"];
				$temp["image_thumb_height"] = $advert["image_thumb_height"];
				$temp["window"] = ($advert["window"] != "") ? 1 : 0;
				$temp["link_type"] = $advert["link_type"];
				$temp["external_page"] = $advert["external_page"];
				$temp["page_link"] = $advert["page_link"];
				$temp["weight"] = $advert["weight"];
				$temp["addtext"] = $advert["addtext"];
				$temp["text"] = $advert["text"];
				
				$temp_datas["datas"] = json_encode($temp);
				
				array_push($return, $temp_datas);
			}
		}
		
		return $return;
	}
			
	public static function serializeDatas($campaign, $ordre_pub)
	{
		$adverts = array();
		for ($i=0; $i<$campaign['nb_pub']; $i++)
		{
			$advert['image'] 		= $campaign['imageH'.$ordre_pub[$i]];
			$advert['link_type'] 	= $campaign['link_typeH'.$ordre_pub[$i]];
			$advert['external_page']= $campaign['external_pageH'.$ordre_pub[$i]];
			$advert['page_link'] 	= $campaign['page_linkH'.$ordre_pub[$i]];
			$advert['window'] 		= $campaign['windowH'.$ordre_pub[$i]];
			
			$advert['text_image']	= $campaign['text_imageH'.$ordre_pub[$i]];
			$advert['textarea'] 	= $campaign['textareaH'.$ordre_pub[$i]];
			$advert['weight'] 		= $campaign['weightH'.$ordre_pub[$i]];
			
			$adverts[] = $advert;
			
			unset($campaign['imageH'.$ordre_pub[$i]]);
			unset($campaign['link_typeH'.$ordre_pub[$i]]);
			unset($campaign['external_pageH'.$ordre_pub[$i]]);
			unset($campaign['page_linkH'.$ordre_pub[$i]]);
			unset($campaign['windowH'.$ordre_pub[$i]]);
			
			unset($campaign['text_imageH'.$ordre_pub[$i]]);
			unset($campaign['textareaH'.$ordre_pub[$i]]);
			unset($campaign['weightH'.$ordre_pub[$i]]);
		}
		
		unset($campaign["text_image"]);
		unset($campaign["image"]);
		unset($campaign["link_type"]);
		unset($campaign["page_link"]);
		unset($campaign["external_page"]);
		unset($campaign["window"]);
		unset($campaign["textarea"]);
		unset($campaign["weight"]);
		
		$campaign['datas'] = json_encode($adverts);
		return $campaign;
	}
	
	public static function unserializeDatas($campaign)
	{
		$campaign["decoded"] = json_decode($campaign["datas"],true); 
		return $campaign["decoded"];
	}
	
public static function findRandomWeighted($items) 
{
	$weight = 0;
	
	foreach($items as $item) 
	{
		$item['weight'] = ($item['weight'] != "") ? $item['weight'] : 1;
		$weight += $item['weight'];
	}
	
	$index = rand(1, $weight);
	
	foreach($items as $item) 
	{
		$temp_weight = ($item['weight'] != "") ? $item['weight'] : 1;
		$index -= $temp_weight;
		
		if($index <= 0) 
			return $item;
	}
	
	return null;
}
	
	/*
	public static function getAllAdvertising()
	{
		$model = new Advertising_Model_DbTable_Advertising();
		
		return $model->getAll();
	}
	
	public static function getAdvertising($id)
	{
		$advert = new Advertising_Object_Advertising($id);
		
		return $advert;
	}
	
	
	
	public static function deleteAdvertising($id)
	{
		$model = new Advertising_Model_DbTable_Advertising();
		$model->deleteAdvert($id);
	}*/

}
