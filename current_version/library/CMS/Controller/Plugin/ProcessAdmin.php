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

class CMS_Controller_Plugin_ProcessAdmin extends CMS_Controller_Plugin_Abstract_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$this->initVarEnv($request);
    	
    	// Si c'est une requete pour une page admin
    	if($this->_isAdmin) {
    		// S'il n'y a pas d'erreur (éviter d'inclure 2 fois le fichier de hook)
    		if(!$request->getParam('error_handler')) {
		        $moduleInitName = "backInit";
		        
		        $front = Zend_Controller_Front::getInstance();
		        
		        $moduleList = $front->getControllerDirectory();
		        $backAcl = CMS_Acl_Back::getInstance();
		        
		        foreach ($moduleList as $moduleName => $val)
					@call_user_func(array($moduleName."_Bootstrap", $moduleInitName));
				
	    		$this->prepareMenu($request);
	    		
	    		$user = Zend_Registry::get('user');
	    		$this->_view->user = $user; 
    		}
    	}
    }

    
    private function prepareMenu($request)
    {    	
    	$hooks = CMS_Application_Hook::getInstance();

    	$menu = $hooks->apply_filters("Back_Main_Menu_Generate");
    	
        $moduleName 		= $request->getModuleName();
        $actionName 		= $request->getActionName();
        $controllerName		= $request->getControllerName();
        $currentRouteName 	= Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
		
        foreach ($menu as  $key => $value) {
        	if(isset($value['children']) && $value['children']) {
        		foreach ($value['children'] as $value2) {
        			if(($value2['moduleName'] == $moduleName) && ($value2['controllerName'] == $controllerName) && ($value2['routeName'] == $currentRouteName)) {
        				$activeMenu = $key;
        				$titleSubMenu = $value2['title'];
        			}
        		}
        	}
        	else if(isset($value2['moduleName']) && $value2['moduleName'] == $moduleName && isset($value2['controllerName']) && $value2['controllerName'] == $controllerName && isset($value2['routeName']) && $value2['routeName'] == $currentRouteName) {
        		$activeMenu = $key;
        		$titleSubMenu = "";
        	}
        }
        
    	$this->_view->assign("adminMenu", $menu);
    	if(isset($activeMenu))
    		$this->_view->assign("activeMenu", $activeMenu);
    	if(isset($titleSubMenu))
    		$this->_view->assign("titleSubMenu", $titleSubMenu);
    }
    
}
	