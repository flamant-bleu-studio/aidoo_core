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

class Bloc_BlocResponsive_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface
{
	public $background_color;
	public $text;
	public $text_color;
	public $id_page;
	public $icon;
	
	protected $_adminFormClass = "Bloc_BlocResponsive_AdminForm";
	
	protected static $_translatableFields = array();
	protected static $_searchableFields = array();
	
	public function runtimeFront($view)
	{
		$view->background_color = $this->background_color;
		$view->text = !empty($this->text) ? $this->text : null;
		$view->text_color = $this->text_color;
		$view->icon = !empty($this->icon) ? $this->icon : null;
		
		if (!empty($this->id_page)) {
			$page = new CMS_Page_Object((int)$this->id_page);
			$view->url = $page->getUrl();
		}
		else {
			$view->url = null;
		}
	}
	
	public function save($post){
		
		$this->background_color = $post['background_color'];
		$this->text = $post['text'];
		$this->text_color = $post['text_color'];
		$this->id_page = $post['id_page'];
		$this->icon = $post['icon'];
		
		$id = parent::save($post);
		
		return $id;
	}
}