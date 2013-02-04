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

class CMS_Controller_Plugin_ApiAuth extends CMS_Controller_Plugin_Abstract_Abstract {
	    
	const AUTH_HEADER_NAME 			= 'apikey';
	const AUTH_AJAX_NAME					= 'ajax_apiKey';
	const AUTH_AJAX_EXPIRE_NAME		= 'ajax_apiKey_expire';
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		define("BASE_URL", Zend_Controller_Front::getInstance()->getBaseUrl());
		define("SKIN_FRONT",  CMS_Application_Config::getInstance()->get("skinfront"));
		
		// Récupération des params en put et delete
		if ($this->getRequest()->getMethod() == 'PUT' || $this->getRequest()->getMethod() == 'DELETE')
			parse_str(file_get_contents('php://input'),$_POST);
		
    	$config = CMS_Application_Config::getInstance();
    	
        $apiKey = $request->getHeader(self::AUTH_HEADER_NAME);
        
        /* API REST (externe) */
        if ($apiKey == $config->get('apiKey'))
        	return;
        
        /* API JSON AJAX (interne) */
        if (isset($_POST[self::AUTH_AJAX_NAME]) && isset($_SESSION[self::AUTH_AJAX_NAME]) && isset($_SESSION[self::AUTH_AJAX_EXPIRE_NAME]))
			if ($_POST[self::AUTH_AJAX_NAME] == $_SESSION[self::AUTH_AJAX_NAME]) 
				if($_SESSION[self::AUTH_AJAX_EXPIRE_NAME] > time())
        			return;
        	
		$request->setModuleName('default')
			->setControllerName('error')
			->setActionName('errorapiauth')
			->setDispatched(true);
   	}
}
