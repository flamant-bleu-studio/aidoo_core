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

function appendBlocsTabMenu($tabs)
{
	$backAcl = CMS_Acl_Back::getInstance();
	if($backAcl->hasPermission("mod_bloc", "view"))
	{
		$tabs['siteManage']['children'][] = array("title" => "Blocs & Disposition", "routeName" => "blocs_back",  "moduleName" => "blocs", "controllerName" => "back", "icon" => "bloc.jpg");
	}
	return $tabs;
}
$hooks->add('Back_Main_Menu_Generate', 'appendBlocsTabMenu', 100);


function regenerateSearchableContentBlocs($return)
{
	$blocs = CMS_Bloc_Abstract::get();
	
	foreach ($blocs as $bloc) {
		$bloc->generateSearch();
	}
	
	return $return;
}

$hooks->add('regenerateSearchableContent', 'regenerateSearchableContentBlocs');