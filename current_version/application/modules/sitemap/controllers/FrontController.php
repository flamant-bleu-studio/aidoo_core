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

class Sitemap_FrontController extends Zend_Controller_Action
{
	
	public function sitemapAction()
	{
		$menus = Menu_Object_Menu::getAllMenuID();
		
		$menus_generate = array();
		
		if($menus){
			foreach ($menus as $menu) {
				
				$temp = new Menu_Object_Menu((int)$menu);
				$temp->disableInactive();
				$temp->disableNoAccessItems();
				$temp->disableEmptyFolder();
				$temp->generate();
				
				$menus_generate[$menu] = $temp;
			}
		}
		
		$config = CMS_Application_Config::getInstance();
		$itemsToExclude = json_decode($config->get("sitemap"));
		
		$exclude = array();
		
		if($itemsToExclude){
			foreach ($itemsToExclude as $key => $item)
			{
				if( $item )
				{
					$temp = substr($key, 5);
					$exclude[$temp] = $temp;
				}
			}
		}
		
		$this->view->itemsToExclude = $exclude;
		$this->view->menus = $menus_generate;
	}
}
