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

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	/**
	* Load and parse application.ini file
	*/
	protected function _initConfig()
	{
		// Récupération de la configuration
		$config = new Zend_Config($this->getOptions());
		// Ajout au registre
		Zend_Registry::set('config', $config);
		
		CMS_Controller_Front::getInstance()->throwExceptions(false);
		
		// Déclaration d'un nouvel autoloader pour les Blocs
		Zend_Loader_Autoloader::getInstance()->pushAutoloader(array('CMS_Bloc_Autoloader', 'autoload'), 'Bloc_');
	}

	protected function _initRouter()
	{
	    $router = CMS_Controller_Front::getInstance()->getRouter();
	    
		$route = new Zend_Controller_Router_Route(
	        ':lang',
			array(
	        	'module' 		=> 'front',
	    		'controller' 	=> 'front',
	    		'action' 		=> 'home',
				'lang'			=> ''
	         ),
	         array('lang' => '[a-z]{2}')
	    );
		
	    $router->removeDefaultRoutes();
	    $router->addRoute('front', $route);
	    $router->addRoute('front_lang', $route);
	}
	
    /**
     * Initialize Zend Controller Action helpers 
     */	
	protected function _initActionHelpers()
	{
		Zend_Controller_Action_HelperBroker::addHelper(new CMS_Controller_ActionHelper_Route());
	}
	
    /**
     * Register the database: Settings are taken from the configuration.ini file   
     */
	protected function _initDb()
	{
    	// Récupération du fichier de configuration
    	$config = Zend_Registry::get('config');
    	
    	define('DB_TABLE_PREFIX', $config->database->params->table_prefix);
    	
    	// Création de la connexion à la bdd
		$db = Zend_Db::factory($config->database->adapter, $config->database->params);
		$db->query("SET NAMES UTF8");
		
		// Set comme connexion par defaut
		Zend_Db_Table::setDefaultAdapter($db);
		
		// Ajout au registre
		$registry = Zend_Registry::set('db', $db);
        
        return $db;
    }    
    
    /**
     * Add only active module in DB 
     * This function need to be after _initDb()
     */
    protected function _initLoadModule() {
    	$config = CMS_Application_Config::getInstance();
    	$front 	= Zend_Controller_Front::getInstance();
    	
    	$activeModule = json_decode($config->get('activeModule'), true);
    	
    	if (is_array($activeModule)) {
	    	foreach ($front ->getControllerDirectory() as $module => $path)
	    		if (!in_array($module, $activeModule))
	    			$front->removeControllerDirectory($module);
    	}
    	else {
    		foreach ($front ->getControllerDirectory() as $module => $path)
	    		$modules[] = $module;
    		
    		$config->set('activeModule', json_encode($modules));
    	}
    }
}
