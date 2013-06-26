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

require_once 'Smarty/Smarty.class.php';


class CMS_Smarty_Smarty extends Zend_View_Abstract
{
    protected $_smarty = false;
    protected $_suffix = 'tpl';
    
    public function __construct($config = null)
    {
        parent::__construct();
        $this->_smarty = new Smarty();
		
		$this->setConfig($config);
		$this->loadPlugins();
	}
	
	public function loadPlugins() 
	{
		// Chargement des plugins smarty type "block"
		$blockMethods = get_class_methods('CMS_Smarty_Plugins_Block');
		foreach($blockMethods as $value)
			$this->_smarty->registerPlugin('block', $value, 'CMS_Smarty_Plugins_Block::' . $value, false);
		
		// Chargement des plugins smarty type "function"
		$functionMethods = get_class_methods('CMS_Smarty_Plugins_Function');
		foreach($functionMethods as $value) 
			$this->_smarty->registerPlugin('function', $value, 'CMS_Smarty_Plugins_Function::' . $value);

		// Chargement des plugins smarty type "function" sans cache
		$functionMethods = get_class_methods('CMS_Smarty_Plugins_FunctionNocache');
		foreach($functionMethods as $value) 
			$this->_smarty->registerPlugin('function', $value, 'CMS_Smarty_Plugins_FunctionNocache::' . $value, false);
	}
	
	/**
	 * setConfig method
	 * @param  Array || Zend_Config object
	 * @return Acti_View_Smarty object
	 */
	public function setConfig($config)
	{
		if ($config instanceof Zend_Config)
			$config = $config->toArray();
		else if (!is_array($config))
			throw new Zend_View_Exception('setConfig() expects a Zend_Config object or array');
		
		$this->setDebug($config['debug']);
		$this->setUseSubDirs($config['use_sub_dirs']);
		
		// Chemins
		$this->setCompileDir($config['compile']['path']);
		$this->setCacheDir($config['cache']['path']);
		
		// Compilation
		if ((bool)$config['compile']['force'])
			$this->_smarty->force_compile = true;
				
		// Cache
		if ((bool)$config['cache']['enabled']) {
			$this->_smarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->_smarty->setCacheLifetime((int)$config['cache']['lifetime']);
		}
	
		return $this;
	}
	
    public function assign($var,$value=null)
    {
        if(is_string($var))
            $this->_smarty->assign($var, $value);
        elseif(is_array($var)) {
            foreach ($var as $key => $value)
                $this->_smarty->assign($key, $value);
        }
        else
            throw new Zend_View_Exception('assign() expects a string or array, got '.gettype($var));
    }  
    
    public function getEngine() 
    {
		return $this->_smarty;
	}
	
	public function __set($var,$value)
	{
		$this->assign($var,$value);
	}
	
	public function setDebug($debug = false)
	{
		$this->_smarty->debugging = $debug;
	}
	
	public function setUseSubDirs($active = false)
	{
		$this->_smarty->use_sub_dirs = $active;
	}
	
	public function setCompileDir($directory)
	{
		if(empty($directory))
			throw new Exception('Invalid configuration, compile directory is empty');
		
		$this->_smarty->compile_dir = $directory;
	}
	
	public function setCacheDir($directory)
	{
		if (!empty($directory))
			$this->_smarty->cache_dir = $directory;
	}

    public function escape($var)
    {
        if(is_string($var)) 
        	return parent::escape($var);
        else if (is_array($var)) {
            foreach ($var as $key => $val)
				$var[$key] = $this->escape($val);
		}
        
		return $var;
    }
	
    public function output($name)
    {
        print parent::render($name);
    }
    
    public function renderInnerTpl($name)
    {
    	return parent::render($name);
    }
    
    public function renderByViewName($name)
    {
    	return parent::render($name.'.'.$this->_suffix);
    }
    
    protected function _run()
    {
    	$template = @func_get_arg(0);
    	$this->_smarty->display($template);
    }
        
    public function initViewAndOverride($path, $overridePath = null, $viewPathName = null)
    {
    	
    	/*
    	 * Ajout des chemins des vues (la seconde ligne sera prise en prio si vue existante)
    	*/
    	$this->setScriptPath($path);
    	
    	if($overridePath)
    		$this->addScriptPath($overridePath);
    
    	/*
    	 * Si configuration mobile activée et tpl correspondant existant, changement du suffix des vues
    	*/
    	if(defined('MOBILE')) {
    		
    		$request = Zend_Controller_Front::getInstance()->getRequest();
    		
    		$path .= ($viewPathName) ? $viewPathName : $request->getControllerName() . '/' . $request->getActionName() ;

    		// Si mode tablette et t.tpl existant
    		if(MOBILE == 'tablet' && file_exists($path . '.t.tpl')){
    			Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->setViewSuffix('t.tpl');
    			$this->_suffix = 't.tpl';
    		}
    		// Si mode tablette et m.tpl existant
    		else if(MOBILE == 'mobile' && file_exists($path . '.m.tpl')){

    			Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->setViewSuffix('m.tpl');
    			$this->_suffix = 'm.tpl';
    		}
    		// Si le suffix est changé mais le tpl correspondant inexistant : remise à zero 
    		else if($this->_suffix != 'tpl'){
    			Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->setViewSuffix('tpl');
    			$this->_suffix = 'tpl';
    		}
    	}
    
    }
    
    

}
