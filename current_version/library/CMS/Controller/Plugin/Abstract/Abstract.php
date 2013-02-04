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

abstract class CMS_Controller_Plugin_Abstract_Abstract extends Zend_Controller_plugin_Abstract {
	
    protected $_view;
    protected $_layout;
    protected $_isPageAdmin;
    protected $_isAjax;
    protected $_isAdmin;
    protected $_isLoggin;
    protected $_isError;
    protected $_isMiddle;
    protected $_config;
    
    public function __construct()
    {
    	$this->_layout 		= Zend_Layout::getMvcInstance();
		$this->_view 		= $this->_layout->getView();
		$this->_isLoggin 	= isset($this->_moduleName) && $this->_moduleName == "users" && isset($this->_controllerName) && $this->_controllerName == "login";
		$this->_isError  	= isset($this->_moduleName) && ($this->_moduleName == "default" || $this->_moduleName == "front") && isset($this->_controllerName) && $this->_controllerName == "error";
		
    }
    
    public function initVarEnv($request)
    {
    	$this->_config	 = CMS_Application_Config::getInstance();
    	$this->_isAdmin  = $this->_request->getParam('_isAdmin');
    	$this->_isAjax	 = $this->_request->getParam('_isAjax');
    	$this->_isMiddle = $this->_request->getParam('_isMiddle');
    	
    	$baseUrl 	= Zend_Controller_Front::getInstance()->getBaseUrl();
    	$requestUri = substr($request->getRequestUri(), strlen($baseUrl)+1);
    	
    	// Retrait du slash en fin d'URI
    	if( substr($requestUri, -1) == '/')
    		$requestUri = substr($requestUri, 0, (strlen($requestUri)-1));
    	
    	// ADMIN ?
    	if(strpos($requestUri, '/') == 2 && strlen($requestUri) > 2)
    		$requestUriWithoutLang = substr($requestUri, 3);
    	else
    		$requestUriWithoutLang = $requestUri;
    	
    	if(stripos($requestUriWithoutLang, 'administration') === 0)
    		$this->_isPageAdmin = true;
    }
}
