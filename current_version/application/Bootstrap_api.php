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

require_once 'Globals.php'; //classe Globals
require_once 'Bootstrap.php'; //classe Globals

class Bootstrap_api extends Bootstrap
{
	/**
	* Load and parse application.ini file
	*/
	protected function _initConfig()
	{
		parent::_initConfig();
	}
	
	protected function _initCache()
	{
		$frontend = array(
			'lifetime' => 3600 * 24,
			'automatic_serialization' => true
		);
		
		$backend = array('cache_dir' => CMS_PATH.'/tmp/zend_cache/');
	
		if (extension_loaded('apc'))
			$cache = Zend_Cache::factory('Core', 'apc', $frontend, $backend);
		else
			$cache = Zend_Cache::factory('Core', 'file', $frontend, $backend);
		
		Zend_Registry::set('cache', $cache);
		
		Zend_Date::setOptions(array('cache' => $cache));
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
		CMS_Page_Object::setCache($cache);
		CMS_Cache::setCacheObject($cache);
		
		/*
		$classFileIncCache = CMS_PATH.'/tmp/zend_cache/pluginLoaderCache.php';
		
		if (file_exists($classFileIncCache))
			include_once $classFileIncCache;
		
		Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
		*/
	}
	
	/**
	 * Initialize Smarty as termplate engine for Views (.tpl files)
	 */
	protected function _initView()
	{
		// Récupération de la config
		$config = Zend_Registry::get('config');
	
		// Initialisation de smarty
		$view = new CMS_Smarty_Smarty($config->smarty);
		
		$zend_layout = Zend_Layout::startMvc(array(
			'view' 				=> $view,
			'viewSuffix' 		=> 'tpl',
			'InflectorTarget' 	=>':script.:suffix'
		));
		
		return $view;
	}
	
	protected function _initRestRoute()
	{
		$this->bootstrap('frontController');
		$frontController = Zend_Controller_Front::getInstance();
		$restRoute = new CMS_Rest_Route($frontController);
		$frontController->getRouter()->addRoute('default', $restRoute);
	}
	    
	/**
	* Register the default controler (front)
	 */
	protected function _initControllersPlugins()
	{
		$front = CMS_Controller_Front::getInstance();
		
		$front->registerPlugin(new CMS_Acl_Plugins_CheckRights())
		->registerPlugin(new CMS_Controller_Plugin_LangSelector())
		->registerPlugin(new CMS_Controller_Plugin_ProcessSEO())
 		->registerPlugin(new CMS_Controller_Plugin_Log())
		->registerPlugin(new CMS_Controller_Plugin_ApiSwitcher())
		->registerPlugin(new CMS_Controller_Plugin_ApiAuth())
		->registerPlugin(new Zend_Controller_Plugin_PutHandler());
	}
}
