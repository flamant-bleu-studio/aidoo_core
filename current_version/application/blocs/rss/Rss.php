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

class Bloc_Rss_Rss extends CMS_Bloc_Abstract implements CMS_Bloc_Interface {
	
	public $url_rss;
	public $nb_rss;
	
	protected $_adminFormClass = "Bloc_Rss_AdminForm";
	protected static $_translatableFields = array();
	
	public function runtimeFront($view){
		$view->id = $this->id_item;
	}
	
	public function save($post){
		
		$this->title = $post["title"];		
		$this->url_rss = $post["url_rss"];
		$this->nb_rss = $post["nb_rss"];
		
		$id = parent::save($post);
		
		return $id;
	}
	
}