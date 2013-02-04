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

function appendSkinsTabMenu($tabs)
	{
		$backAcl = CMS_Acl_Back::getInstance();
		if($backAcl->hasPermission("mod_skins", "view"))
		{		
			$tabs['config']['children'][] = array("title" => "Skins", "routeName" => "skins_back",  "moduleName" => "skins", "controllerName" => "back", "icon" => "skin.png");
			//$tabs['config']['children'][] = array("title" => "FavIcon", "routeName" => "skins_favicon",  "moduleName" => "skins", "controllerName" => "favicon", "icon" => "skin.png");
		}
		
		return $tabs;
	}
	
    $hooks = CMS_Application_Hook::getInstance();
	$hooks->add('Back_Main_Menu_Generate', 'appendSkinsTabMenu', 600);