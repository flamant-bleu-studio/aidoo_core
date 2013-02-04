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

class Bloc_LangChooser_Main extends CMS_Bloc_Abstract implements CMS_Bloc_Interface
{
	protected $_adminFormClass = "Bloc_LangChooser_AdminForm";
	
	public function runtimeFront($view)
	{
		$currentPage = CMS_Page_Current::getInstance();
		
		$params = $currentPage->getUrlParams();

 		$page = new CMS_Page_Object($currentPage->id_page, 'all');
			
 		$langs = json_decode(CMS_Application_Config::getInstance()->get("availableFrontLang"), true);
 		$url = array();
 		
 		foreach($langs as $lang_code)
 			$url[$lang_code] = $page->getUrl($lang_code).$params;
 		
		$view->url = $url;
	}
	
	public function save($post)
	{
		$id = parent::save($post);
		
		return $id;
	}
}