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

class Bloc_StaticHtml_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $bg;
	public $content;
	public $width;
	public $height;
	
	protected $_adminFormClass = "Bloc_StaticHtml_AdminForm";
	
	protected static $_translatableFields = array("content");
	protected static $_searchableFields = array("content");
	
	public function runtimeFront($view){	
		$view->content 	= $this->content;
		$view->bg 		= $this->bg;
		$view->width	= $this->width;
		$view->height	= $this->height;
	}
	
	public function save($post){
		
		$this->bg	  	 	= $post["bg"];
		$this->content 		= $post["content"];
		$this->width   		= $post["width"];
		$this->height 	 	= $post["height"];
		
		$id = parent::save($post);
		
		return $id;
	}
	
}