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

class CMS_Controller_ActionHelper_Route extends Zend_Controller_Action_Helper_Abstract {

    public function direct($action = null, array $params = null) {
		$this->short(action, $params);
    }

    /**
     * Génère une url, selon la route courante, en réutilisant les paramètres actuels
     *
     * @param string $action Nom de l'action où aller
     * @param array $params Paramètres optionnels à injecter
     *
     * @return string url générée
     */
    public function current() {
    	
    	$pageURL = 'http';
    	if ($_SERVER["HTTPS"] == "on") {
    		$pageURL .= "s";
    	}
    	$pageURL .= "://";
    	if ($_SERVER["SERVER_PORT"] != "80") {
    		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    	} else {
    		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    	}
    	
    	return $pageURL;
    }
    
    /**
	 * Génère une url, selon la route courante, en réutilisant les paramètres actuels
	 * 
	 * @param string $action Nom de l'action où aller
     * @param array $params Paramètres optionnels à injecter
     * 
	 * @return string url générée
	 */    
    public function magic($action = null, array $params = null) {
    	return $this->short($action, $params, false);
    }
    
    /**
     * Génère une url, selon la route courante et avec des paramètres optionnels
     * 
     * @param string $action Nom de l'action où aller
     * @param array $params Paramètres optionnels à injecter
     * @param bool $resetParams Réutilise les paramètres de route actuels
     * 
     * @return string url générée
     */
    public function short($action = null, array $params = null, $resetParams = true) {
    	
    	$router = Zend_Controller_Front::getInstance()->getRouter();

    	$route_params = array('action' => $action);
    	
    	if( $params ) {
    		foreach($params as $key => $value) {
    			$route_params[$key] = $value;
    		}
    	}
    	
    	if (!isset($route_params['lang']) && $this->getRequest()->getParam('lang')){
    		$route_params['lang'] = $this->getRequest()->getParam('lang'); 
    	}
    	
        $url = str_replace(BASE_URL, '', $router->assemble($route_params, $router->getCurrentRouteName(), $resetParams));
        if(!$this->getRequest()->getParam("_isAdmin") && $page = CMS_Page_Object::get($url)) {
        	$page->setRewriteVarObject($params['object']);
        	$return = $page->getUrl();
        } else {
        	$return = $url;
        }
        
        return $return;
    }
	
    /**
     * Génère une url, selon une route et des paramètres optionnels
     * 
     * @param string $route_name Nom de la route à utiliser
     * @param array $params Les paramètre à injecter
     * 
     * @return string url générée
     */
    public function full($route_name, array $params = array()) {
    	$router = Zend_Controller_Front::getInstance()->getRouter();
    	
    	if(isset($params['lang'])) {
	    	$lang_param = $params['lang'];
	    	unset($params['lang']);
    	}
    	else {
    		$lang_param = null;
    	}
    	
		$url = $router->assemble($params, $route_name, true);
		
        $url = str_replace(BASE_URL, '', $url);
        
        // Front
        if (!$this->getRequest()->getParam("_isAdmin")) {
        	
        	if ($page = CMS_Page_Object::get($url)) {
        		
        		if(isset($params['object']))
        			$page->setRewriteVarObject($params['object']);   
        		     		
        		$url = $page->getUrl();
        	}
        }
        // Back
    	else if($this->getRequest()->getParam("_isAdmin") && ($this->getRequest()->getParam('lang') || isset($lang_param))) {
			
    		$lang = (isset($lang_param)) ? $lang_param : $this->getRequest()->getParam('lang');
    		
    		if($lang != DEFAULT_BACK_LANG_CODE)
    			$url = "/" . $lang . $url;
    	}
    	
        return $url;
    }
        
}