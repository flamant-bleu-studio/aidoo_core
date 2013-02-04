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

class CMS_Controller_Plugin_Maintenance extends CMS_Controller_Plugin_Abstract_Abstract {
	    
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {  
    	$this->initVarEnv($request);
    	
    	$auth = Zend_Auth::getInstance();
		
		// Si le site est en maintenance et qu'un user n'est pas logué et qu'il n'accède pas à l'admin : redirection
		if(CMS_Application_Config::getInstance()->get('maintenance') == 1 && !$auth->hasIdentity() && !preg_match("#(.*)/administration/login(/)?$#", $_SERVER["REQUEST_URI"]) ) {
			header('Location: /maintenance.html');
		}
		
		// Initialisation de l'AdminTopBar
		if($this->_isAdmin && ((defined("MULTI_SITE_ACTIVE") && MULTI_SITE_ACTIVE === true))) {
			global $array_multi;
			$this->_view->multi_site_select = $array_multi;
		}
		
		if (!$this->_isPageAdmin && !$this->_isAjax) {
			
			$config 		= CMS_Application_Config::getInstance();
			$seoGenericdata = json_decode($config->get("seo"));
			
            $page = CMS_Page_Current::getInstance();
            
			$seo = new stdClass();
			
	    	$seo->keywords 		= $page->meta_keywords ? $page->meta_keywords : $seoGenericdata->keywords;
	    	$seo->description 	= $page->meta_description ? $page->meta_description : $seoGenericdata->description;
	    	$seo->title 		= $page->title ? $page->title : $seoGenericdata->title;
			
		    $this->_view->seoData = $seo;
		}
	}
}
