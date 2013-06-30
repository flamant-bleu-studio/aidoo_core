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

class Blocs_Lib_Manager {
	
	public static function getAllBlocXml()
	{
		$return = array();
		
		if(!function_exists("orderBlocFunction")){
			function orderBlocFunction($a, $b){
				return strcmp($a["name"], $b["name"]);
			}
		}
		
		foreach (new DirectoryIterator(APPLICATION_PATH."/blocs/") as $file)
		{
			if ($file->isDir() && file_exists($file->getPathname().'/bloc.xml')){
				$blocXML = new Zend_Config_Xml($file->getPathname().'/bloc.xml');
				$blocXML = $blocXML->toArray();
				
				$return[$blocXML["type"]] = $blocXML;
			}
		}
		
		uasort($return, "orderBlocFunction");
		
		return $return;
	}
	
	public static function isValidBlocType($type){
		
		foreach (new DirectoryIterator(APPLICATION_PATH."/blocs/") as $file)
		{
			if ($file->isDir() && file_exists($file->getPathname().'/bloc.xml')){
				$blocXML = new Zend_Config_Xml($file->getPathname().'/bloc.xml');
				$blocXML = $blocXML->toArray();
				
				if($blocXML["type"] == $type)
					return true;
			}
		}
		
		return false;
	}
	
	public static function getRenderTemplate($activeTemplateId = null, $activeDiaporamaId = null)
	{
		/** Get view smarty and change path render **/
		$view = Zend_Layout::getMvcInstance()->getView();
		$scriptPath = $view->getScriptPaths();
		$view->addScriptPath(APPLICATION_PATH . '/modules/blocs/views/');
		
		/** Templates & Blocs & Placeholders  **/
		$templates = Blocs_Object_Template::get();
		$defaultTemplate = Blocs_Object_Template::get(array("defaut" => "1"));
		$blocs = CMS_Bloc_Abstract::get();
		$blocsXml = self::getAllBlocXml();
		$templatesToSelect = array(0 => "par défaut");
		$sidebar = array();
		$placeholders = array("sideleft1", "sideright1");
		
		if ( count($templates) > 0 ) { 
			foreach ($templates as $template) {
				$sidebar[$template->id_template]["sideleft1"] = array();
				$sidebar[$template->id_template]["sideright1"] = array();
				
				$templatesToSelect[$template->id_template] = "- ".$template->title;
				
				$item_position = $template->getItemsPosition();
				$item_position = $item_position['classic'];
				
				if ($item_position && count($item_position) > 0) {
					foreach ($item_position as $placeholder => $items) {
						if( in_array($placeholder, $placeholders) )
							$sidebar[$template->id_template][$placeholder] = $items;
					}
				}
			}
		}
		
		/** Template Default **/
		$sidebar[0] = $sidebar[$defaultTemplate[0]->id_template];
		
		/** Diaporamas **/
		$diaporamas = GalerieImage_Object_Galerie::get(array("type" => GalerieImage_Object_Galerie::TYPE_DIAPORAMA));
		$listDiaporamas = array();
    	$listDiaporamas["null"] = "Aucun";
    	$listDiaporamas[0] = "par défaut";
    	if( count($diaporamas) > 0 )
    	{
			foreach ($diaporamas as $diaporama)
				$listDiaporamas[$diaporama->id] = "- ".$diaporama->title;
    	}
    	
    	/** Var Smarty **/
		$view->templates = $templates;
		$view->defaultTemplate = $defaultTemplate;
		$view->templatesToSelect = $templatesToSelect;
		$view->blocs = $blocs;
		$view->blocsXml = $blocsXml;
		$view->listDiaporamas = $listDiaporamas;
		$view->sidebar = $sidebar;
		$view->activeTemplateId = $activeTemplateId;
		$view->activeDiaporamaId = $activeDiaporamaId;

		/** Create render **/
		$render = $view->render("/render/preview.tpl");
		
		/** Set old script path **/
		$view->setScriptPath($scriptPath);
		
		/** Return render **/
		return $render;
	}
	
}