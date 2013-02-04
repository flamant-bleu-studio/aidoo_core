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

$hooks = CMS_Application_Hook::getInstance();

function appendGalerieImageTabMenu($tabs)
{
	$backAcl = CMS_Acl_Back::getInstance();
	
	if($backAcl->hasPermission("mod_galeriePhoto", "view"))
	{
		$tabs['siteLife']['children'][] = array("title" => "Galeries Photo", "routeName" => "galeriePhoto_back",  "moduleName" => "galerieImage", "controllerName" => "back", "icon" => "actu.png");
	}
	
	if($backAcl->hasPermission("mod_diaporama", "view"))
	{
		$tabs['siteLife']['children'][] = array("title" => "Diaporama", "routeName" => "diaporama_back",  "moduleName" => "galerieImage", "controllerName" => "back", "icon" => "diapo.png");
	}
	
	return $tabs;
}

$hooks->add('Back_Main_Menu_Generate', 'appendGalerieImageTabMenu', 150);

	function ApiCreateGalerieImage($tab)
	{
		$tab["galerieImage"] = array(
			"name" => "Galerie image",
			"api_name" => "GalerieImage_Lib_Api"
		);
	
		return $tab;
	}
	$hooks->add('listCreateApi', 'ApiCreateGalerieImage', 151);