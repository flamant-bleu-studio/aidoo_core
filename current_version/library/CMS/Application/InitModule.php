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

class CMS_Application_InitModule {
	
	public static function initModule($moduleName = null, $dirname = null)
	{
		if($moduleName === null || $dirname === null)
			throw new Zend_Exception('initModule require module name and dirname');
			
		self::initAutoload($moduleName, $dirname);
		self::_initRouter($moduleName, $dirname);
	}
	
    public static function initAutoload($moduleName, $dirname)
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
        	'namespace' => ucfirst($moduleName),
        	'basePath' => $dirname ? $dirname : APPLICATION_PATH . '/modules/'.$moduleName,
    		'resourceTypes' => array(
		        'lib' => array(
		            'path'      => 'lib/',
		            'namespace' => 'Lib'
		        ),
		        'objetcs' => array(
		            'path'      => 'objects/',
		            'namespace' => 'Object'
		        )
        	)));
    }
    
	protected static function _initRouter($moduleName, $dirname) 
	{
		$routes = new Zend_Config_Xml($dirname.'/routes.xml', 'routes');
		
	    $router = Zend_Controller_Front::getInstance()->getRouter();
	    $routeLang = $router->getRoute('front');
		
		foreach ($routes as $name => $info) {
			
			$class = (isset($info->type)) ? $info->type : 'Zend_Controller_Router_Route';
			
	        $route = call_user_func(array($class, 'getInstance'), $info);
	        
	        $router->addRoute($name, $route);
	        
			if( $info->defaults->_isAdmin == "true" || $name == "admin_logout" ) {
		        $router->addRoute($name.'_lang', $routeLang->chain($route));
			}
		}	
	}
}