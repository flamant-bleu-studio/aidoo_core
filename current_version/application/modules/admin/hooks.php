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

function appendAdminVieduSite($tabs)
		{
			$tabs['siteLife'] = array("title" => "Vie du site", "icon" => "vie_site.png");
			return $tabs;
		}
		
		function appendAdminAutoConfig($tabs)
		{
			$tabs['auto'] = array("title" => "Auto", "icon" => "motors.png");
			return $tabs;
		}
		
		function appendAdminImmoConfig($tabs)
		{
			$tabs['immo'] = array("title" => "Immobilier", "icon" => "motors.png");
			return $tabs;
		}
		
		function appendAdminGestionSite($tabs)
		{
			$tabs['siteManage'] = array("title" => "Gestionnaire du site", "icon" => "gest_site.png");
			return $tabs;
		}
		

	    function appendAdminTabMenu($tabs)
		{
			$tabs['dashboard'] = array("title" => "Tableau de bord", "routeName" => "admin", "moduleName" => "admin", "controllerName" => "back", "icon" => "tableau_bord.png");
			return $tabs;
		}
		
	    function appendAdminConfig($tabs)
		{
			$tabs['config'] = array("title" => "Configurations du site", "icon" => "config_site.png");
			return $tabs;
		}
	
		function appendAdminTabMenuSiteConfig($tabs)
		{     
			
			$backAcl = CMS_Acl_Back::getInstance();
			if($backAcl->hasPermission("admin", "view"))
			{
				$tabs['config']['children'][] = array("title" => "Langues", "routeName" => "admin_configuration", "moduleName" => "admin", "controllerName" => "lang", "icon" => "site_config.png");
				$tabs['config']['children'][] = array("title" => "Site Configuration", "routeName" => "admin_configuration", "moduleName" => "admin", "controllerName" => "config", "icon" => "site_config.png");
			}
			return $tabs;
		}
		
		function appendAdminPagesProConfig($tabs) {
			$tabs['pagesPro'] = array("title" => "PagesPro", "icon" => "config_site.png");
			return $tabs;
		}
		
		function appendAdminLikeResto($tabs) {
			$tabs['likeresto'] = array("title" => "LikeResto");
			return $tabs;
		}
		
		$hooks = CMS_Application_Hook::getInstance();
		
		
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminTabMenu', 1);
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminGestionSite', 95);
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminVieduSite', 96);
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminAutoConfig', 549);
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminImmoConfig', 550);
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminLikeResto', 551);
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminPagesProConfig');
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminConfig', 599);
		$hooks->add('Back_Main_Menu_Generate', 'appendAdminTabMenuSiteConfig', 900);
		