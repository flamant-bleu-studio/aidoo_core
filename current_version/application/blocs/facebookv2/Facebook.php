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

class Bloc_Facebookv2_Facebook extends CMS_Bloc_Abstract  implements CMS_Bloc_Interface {
	
	public $width;
	public $height;
	public $color;
	public $faces;
	public $colorBorder;
	public $stream;
	
	protected $_adminFormClass = "Bloc_Facebookv2_AdminForm";
	
	protected static $_translatableFields = array();
	
	public function runtimeAdmin($view){
		$config = CMS_Application_Config::getInstance();
		$social = json_decode($config->get("social"));
		
		if( !$social->facebook )
			$view->configFacebookLink = "false";
		else
			$view->configFacebookLink = "true";
	}
	
	public function runtimeFront($view){
		$config = CMS_Application_Config::getInstance();
		$social = json_decode($config->get("social"));
		$view->facebookLink = $social->facebook;
		
		$view->width = $this->width;
		$view->height = $this->height;
		$view->color = $this->color;
		$view->faces = $this->faces ? 'true' : 'false';
		$view->colorBorder = $this->colorBorder;
		$view->stream = $this->stream ? 'true' : 'false';
	}
	
	public function save($post){
		
		$this->width 		= (int)$post["width"];
		$this->height 		= (int)$post["height"];
		$this->color 		= $post["color"];
		$this->faces 		= $post["faces"];
		$this->colorBorder 	= $post["colorBorder"];
		$this->stream 		= $post["stream"];
		
		$id = parent::save($post);
		
		return $id;
	}
}