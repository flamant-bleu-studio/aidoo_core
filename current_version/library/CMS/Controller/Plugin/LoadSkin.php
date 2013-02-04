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

class CMS_Controller_Plugin_LoadSkin extends Zend_Controller_plugin_Abstract {
	
    private $_layout;
    private $_view;
    
    private $_moduleName;
    private $_controllerName;
    private $_actionName;
    
    private $skinName;
    private $skinPath;
    private $skinUrl;
    
    private $_config;
    private $_processLayout;
    
    private $_lang_id;
    
    private $blocExceptions;
    
    
    private $_isAdmin;
    private $_isLoggin;
    private $_isAjax;
    private $_isError;
    private $_isMiddle;
	
	/**
	 * 
	 * @todo TODO List for module
	 *       - Remove $blocContent injection from Loadskin
	 *       - Inject only one tpl variable for each bloc  
	 * @param Zend_Controller_Request_Abstract $request
	 */
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	try{
	    	// Vars init
	    	$this->_layout 	= Zend_Layout::getMvcInstance();
			$this->_view 	= $this->_layout->getView();
	    	
			$this->_moduleName 				= $request->getModuleName();
	        $this->_controllerName 			= $request->getControllerName();
	        $this->_actionName				= $request->getActionName();
	        $this->_view->moduleName 		= $this->_moduleName;
	        $this->_view->actionName 		= $this->_actionName;
	        $this->_view->controllerName	= $this->_controllerName;
	        
	        define("BASE_URL", Zend_Controller_Front::getInstance()->getBaseUrl());
	    	$this->_view->baseUrl = BASE_URL;
    	
	        $this->_config					= CMS_Application_Config::getInstance();
	        $this->_processLayout			= CMS_Application_ProcessLayout::getInstance();
	        
	        $this->_lang_id = $this->_config->getActiveLang();
	        
	        $this->_view->lang_id = $this->_lang_id;
	        
	        $this->_isAdmin  = $this->_request->getParam('_isAdmin');
	    	$this->_isLoggin = $this->_moduleName == "users" && $this->_controllerName == "login";
	    	$this->_isAjax	 = $this->_request->getParam('_isAjax');
	    	$this->_isError  = ($this->_moduleName == "default" || $this->_moduleName == "front") && $this->_controllerName == "error";
	    	$this->_isMiddle = $this->_request->getParam('_isMiddle');
	    	
	        // SMARTY initialization
	        $this->initSmarty($request);
	
	        // Démarrage du singleton Activity
	        CMS_Application_Activity::getInstance();
	       
	        // Init skins and layouts
			$this->processSkin();
			
	    	$this->processPlugins();
	    	$this->overrideTplByModule();
	    	
	    	if(defined("NOTFOUND")){
	    		throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
	    	}
		}
		catch(Exception $e){
			$this->throwException($e);
		}
    }
    
	public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
		//$this->_view->AppendTinyMCE		= $this->_processLayout->getTinyMCE();
        //$this->_view->AppendImageManager 	= $this->_processLayout->getImageManager();
        //$this->_view->AppendFileManager 	= $this->_processLayout->getFileManager();
        
   		 if(!$this->_isAdmin && !$this->_isLoggin && !$this->_isAjax && !$this->_isError || defined("NOTFOUND")){
    		$this->processBlocs();
//         	$this->processGoogleAnalytics();
    	}
    	
        if(!empty($this->blocExceptions)){
	    	$this->throwException($this->blocExceptions[0]);
        }
    }
    
    public function initSmarty(Zend_Controller_Request_Abstract $request)
    {
		$id = $request->getParam('id');
        $id = (($id == 0) ? '' : '-'.$id);
        $smarty = $this->_view->getEngine();
		$smarty->compile_id = $this->_moduleName.'-'.$this->_controllerName.'-'.$this->_actionName.$id;
    }
    
	public function processSkin()
	{
		//$this->_view->setScriptPath($modulePath);
		
    	if($this->_isAdmin || $this->_isLoggin || $this->_isAjax)
        {
        	$this->skinName = $this->_config->get("skinback");
        	$this->skinPath = ADMIN_PATH . $this->skinName;
        	$this->skinUrl  = ADMIN_URL . $this->skinName;
        	
			if(!$this->skinName || !file_exists($this->skinPath))
	        	throw new Exception(_t('No back skin set or skin not found'));		

        	if($this->_isAdmin)
        		$layoutFile = "back";
        	else
        		$layoutFile = "login_back";
        }
        else 
        { 
        	$this->skinName = $this->_config->get("skinfront");
        	$this->skinPath = PUBLIC_PATH.'/skins/'.$this->skinName;	    	
	    	$this->skinUrl = '/skins/' . $this->skinName;
	    	
        	if(!$this->skinName || !file_exists($this->skinPath))
        		throw new Exception(_t('No front skin set or skin not found'));
        	
        	// Detection et configuration des versions Mobile et Tablette
        	$mobileConfig = json_decode($this->_config->get('mobileConfig'), true);
        	
        	if($mobileConfig['mobile'] || $mobileConfig['tablet']) {
        	 
	        	$detect = new CMS_UserAgent();
	        	
	        	if($mobileConfig['tablet'] && $detect->isTablet()){
	        		$layoutFile = "front-t";
	        		define("MOBILE", "tablet");
	        	}
	        	elseif($mobileConfig['mobile'] && $detect->isMobile() && !$detect->isTablet()){
	        		$layoutFile = "front-m";
	        		define("MOBILE", "mobile");
	        	}
	        	else
	        		$layoutFile = "front";
        	}
        	else
        		$layoutFile = "front";

        }
        
        define("SKIN_FRONT", $this->_config->get("skinfront"));
    	
    	/* Désactivation du moteur de template si Ajax */
        if($this->_isAjax){
        	$this->_layout->disableLayout();
        	$this->_layout->disableInflector();
        	Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        }
        else {

	        $this->_layout->setLayoutPath($this->skinPath);
	        $this->_layout->setLayout($layoutFile);
        }

        define("SKIN_NAME", $this->skinName);
    	define("SKIN_PATH", $this->skinPath);
    	define("SKIN_URL", $this->skinUrl);
    	
    	if(defined('ISHOME'))
			$this->_view->isHome = ISHOME;
    	
		$this->_view->skinName = $this->skinName;
	    $this->_view->skinPath = $this->skinPath;
        $this->_view->skinUrl = $this->skinUrl;

	} 
	
	/**
	 * Configure et initialise l'emplacement des surcharges de templates
	 */
	public function overrideTplByModule() {
		$front 				= CMS_Controller_Front::getInstance();
		
		$modulePath 	= $front->getModuleDirectory().'/views/';
		$overridePath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/tpls_override/modules/'.$this->_moduleName.'/';
		
		$this->_view->initViewAndOverride($modulePath, $overridePath);
	}
	
    /**
     * Run all plugins
     */
	public function processPlugins()
    {
        $skinUrl = $this->skinUrl;

		$activePluginsConfig = $this->_config->get("activePlugins");
		$configPluginList = json_decode($activePluginsConfig,true);
    	
    	if ($configPluginList)
    	{
	    	foreach ($configPluginList as $pluginDir => $pluginType)
	    	{
				if ($pluginType == 'core')
					$dir = APPLICATION_PATH.'/plugins/';
				else if ($pluginType == 'project')
					$dir = PUBLIC_PATH.'/core_addons/plugins/';
				else 
					continue;
				
	    		@include ($dir.$pluginDir."/".$pluginDir.".php");
	    	}
    	}
    }
    
	/**
     * Run and Inject all blocs in the layout
     */
	public function processBlocs() {

    	// Récupération de la page courante
    	$currentPage = CMS_Page_Current::getInstance();

    	// Si pas de page courante (404)
    	if($currentPage){
    		
    		if(!$id_tpl = (int)$currentPage->template) {
    			$type = CMS_Page_Type::get(array("type" => $currentPage->type));
    			
    			if(!$id_tpl = (int)$type[0]->default_tpl){
    				$tpl = Blocs_Object_Template::get(array("defaut" => 1));
    				$id_tpl = (int)$tpl[0]->id_template;
    			}
    		}

    		$tpl = new Blocs_Object_Template($id_tpl);
    	}
    	else{
    		$id = CMS_Application_Config::getInstance()->get("tpl_404");
    		
    		if($id)
    			$tpl = new Blocs_Object_Template($id);
    		
    		if(!$tpl){
    			$tpl = Blocs_Object_Template::getOne(array("defaut" => 1));
    		}
    	}
    	
    	if($tpl) {
    		
    		$this->_view->template = $tpl;

	    	$blocsPosition = $tpl->getItemsPosition();
	    	
	    	if(defined('MOBILE')){
	    		if(MOBILE == 'mobile')
	    			$blocsPosition = $blocsPosition["mobile"];
	    		else if(MOBILE == 'tablet')
	    			$blocsPosition = $blocsPosition["tablet"];
	    		else
	    			throw new Exception('Invalid mobile configuration');
	    	}
	    	else {
	    		$blocsPosition = $blocsPosition["classic"];
	    	}
    	
	    	// Création d'un tableau contenant les instances de blocs
	    	$blocsInstance = array();
	    	
	    	// Vérification que des blocs existent (sinon warning lors du passage dans le foreach)
	    	if( $blocsPosition && (count($blocsPosition) > 0) ) {
	    	
	    		$test = array();
	    		$placeholderHTML = "";
		    	foreach($blocsPosition as $placeholders => $blocsIdLst){
		    		
		    		$i = 1;
		    		$lenght = count($blocsIdLst);
		    		
		    		foreach($blocsIdLst as $blocsId){
		    			if(!key_exists($blocsId, $blocsInstance)){
		    				$bloc = CMS_Bloc_Abstract::getBlocInstance($blocsId);
		    				if($bloc)
		    					$blocsInstance[$blocsId] = $bloc;
		    			}
	
		    			if($blocsInstance[$blocsId]){
		    				
		    				// Stockage class css temporaire pour restauration en fin de traitement
		    				$tmpClassCss = $blocsInstance[$blocsId]->classCss;
		    				
		    				if($lenght == 1)
		    					$blocsInstance[$blocsId]->classCss .= " single";
		    				else if($i == 1)
		    					$blocsInstance[$blocsId]->classCss .= " first"; 
		    				else if($i == $lenght)
		    					$blocsInstance[$blocsId]->classCss .= " last";
	
	    					try {
	    						$placeholderHTML .= $blocsInstance[$blocsId]->renderFront();
	    					}catch(Exception $e){
	    						if(defined("NOTFOUND"))
	    							$this->blocExceptions[] = $e;
	    						else
	    							throw $e;
	    					}
	    					
		    				$blocsInstance[$blocsId]->classCss = $tmpClassCss;
		    			}
		    			$i++;
		    		}
		    			
		    		$this->_view->assign($placeholders, $placeholderHTML);
		    		$placeholderHTML = "";
		    	}
	    	}
    	}
    }
    
    public function throwException($e)
    {
    	$this->_request->setModuleName('front');
        $this->_request->setControllerName('error');
        $this->_request->setActionName('error');
        
        $error = new Zend_Controller_Plugin_ErrorHandler();

       	$error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
        $error->request = clone($this->_request);
        $error->exception = $e;
        $this->_request->setParam('error_handler', $error);
    }
    
}