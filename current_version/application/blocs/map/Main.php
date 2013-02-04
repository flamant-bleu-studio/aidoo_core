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

class Bloc_Map_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	protected $_adminFormClass = "Bloc_Map_AdminForm";
	
	protected static $_translatableFields = array();
	
	public $service;
	public $apiKey;
	public $getDirections;
	public $mode;
	public $latitude;
	public $longitude;
	public $zoom;
	public $mapWidth;
	public $mapHeight;
	
	public function runtimeFront($view)
	{
		if ($this->mode == "byPage") {
			if (!defined("BLOC_MAP_LATITUDE") || !defined("BLOC_MAP_LONGITUDE"))
				$this->noRenderBloc = true;
			else {
				$this->latitude = BLOC_MAP_LATITUDE;
				$this->longitude = BLOC_MAP_LONGITUDE;
			} 
		}
		
		$datas = $this->toArray();
		$view->datas = $datas;
	}
	
	public function save($post)
	{		
		$this->fromArray($post);
		
		preg_match('/([\d]+)/', $post["mapWidth"], $matches);
		$this->mapWidth 	= $matches[0];
		
		preg_match('/([\d]+)/', $post["mapHeight"], $matches);
		$this->mapHeight 	= $matches[0];
		
		$id = parent::save($post);
		
		return $id;
	}
	

}