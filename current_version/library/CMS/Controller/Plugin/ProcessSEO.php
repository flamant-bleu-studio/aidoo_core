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

class CMS_Controller_Plugin_ProcessSEO extends CMS_Controller_Plugin_Abstract_Abstract
{
	protected $_view;
	protected $_isPageAdmin;
	protected $_isAjax;
	protected $_isHome;
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) {

		// Désactivation de la récupération des GET et POST par le biais de getParam();
		$request->setParamSources(array());
		
		// URL
		$baseUrl 	= Zend_Controller_Front::getInstance()->getBaseUrl();
		$requestUri = substr($request->getRequestUri(), strlen($baseUrl)+1);

		// Retrait du slash en fin d'URI
		if( substr($requestUri, -1) == '/')
			$requestUri = substr($requestUri, 0, (strlen($requestUri)-1));
		
		// Si un ? est trouvé dans l'URL : des paramètres GET sont présents et on les laisse passer
		if( strpos($requestUri, '?') !== false){
			// Retrait des paramètres GET
			$requestUri = substr($requestUri, 0, (strpos($requestUri, '?')));
		}
		
		/*
		 * Traitement des langues 
		 */
		
		// Langue disponible + langue par défaut
		$config	= CMS_Application_Config::getInstance();
		$availableLangs = json_decode($config->get("availableFrontLang"), true);
		define("DEFAULT_LANG_ID", $config->get("defaultFrontLang"));
		define("DEFAULT_LANG_CODE", $availableLangs[DEFAULT_LANG_ID]);
		
		$availableBackLang = json_decode($config->get("availableBackLang"), true);
		define("DEFAULT_BACK_LANG_ID", $config->get("defaultBackLang"));
		define("DEFAULT_BACK_LANG_CODE", $availableBackLang[DEFAULT_BACK_LANG_ID]);
		
		/**
		 * @todo : si accueil et lang defaut : pas de slash
		 * 			si accueil et lang diff : slash obli.
		 * 			si page : pas de slash
		 */
		
		// HOME PAGE ?
		if(!$requestUri || strlen($requestUri) == 2) {
			$this->_isHome = true;
			define("ISHOME", true);
		}
		else {
			
			if(strpos($requestUri, '/') == 2 && strlen($requestUri) > 2){
				$requestUriWithoutLang 	= substr($requestUri, 3);
			    $current_lang_code 		= substr($requestUri, 0, 2);
			    $current_lang_id 		= array_search($current_lang_code, $availableLangs);
			}
			else{
				$requestUriWithoutLang = $requestUri;
				
				$current_lang_code 		= DEFAULT_LANG_CODE;
			    $current_lang_id 		= DEFAULT_LANG_ID;
			}
				
			// ADMIN ?
			if(stripos($requestUriWithoutLang, 'administration') === 0){
				$this->_isPageAdmin = true;
			}
			// AJAX ?
			else if (stripos($requestUriWithoutLang, 'ajax/') === 0 ){
				$request->setParam('_isAjax', true);
				$this->_isAjax = true;
			}
		}
		
		if(!defined("DISABLE_CORE_PAGE") || !DISABLE_CORE_PAGE){
			
			// Page d'accueil
			if($this->_isHome){
				
				$page = CMS_Page_Current::getInstance(CMS_Page_Object::HOME_ID);
				
				// Accueil sans code langue
				if(!$requestUri ) {
					$current_lang_id = DEFAULT_LANG_ID;
				    $current_lang_code = DEFAULT_LANG_CODE;
				}
				// Accueil avec code langue
				else {
					$current_lang_id = array_search($requestUri, $availableLangs);
					
					if($current_lang_id === false){
						define('NOTFOUND', true);
					}
				
					$current_lang_code 	= $requestUri;

					if($current_lang_code == DEFAULT_LANG_CODE){
						header("Status : 301 Moved Permanently", false, 301);
						header("location: " . $baseUrl . '/');
						exit;
					}
				}
			}
			// Seule les pages front utilisent le core_page
			else if(!$this->_isPageAdmin && !$this->_isAjax){
	
				$page = CMS_Page_Current::getInstance($requestUri);
				
				// Une page trouvée
				if($page){
					
					// Si url_rewrite match la page : le code langue est déduit depuis core_pages_lang
					if(CMS_Page_Current::$code_lang)
						$current_lang_code = CMS_Page_Current::$code_lang;
					else 
						$current_lang_code = DEFAULT_LANG_CODE;
						
					$current_lang_id 	= array_search($current_lang_code, $availableLangs);
				}
				// Aucune page trouvée, mais code langue présent dans l'URI
				else if(strpos($requestUri, '/') == 2 && strlen($requestUri) > 2){
				
					// Extraction du code lang
					$code = substr($requestUri, 0, 2);
					
					// Langue non disponible ?
					if(!in_array($code, $availableLangs))
						define('NOTFOUND', true);
						
					// Récupération page sans code lang
					$page = CMS_Page_Current::getInstance(substr($requestUri, 3));
					
					if($page){
						
						/*
						 * A cet endroit, le code langue a été extrait de l'URI.
						 * Un rewrite ne peut donc plus matcher.
						 * Seul une url système peut matcher une page
						 * 
						 * PS : un rewrite ne contiendra jamais de code langue dit "dynamique"
						 * 		le code langue est implicite dans core_pages_lang
						 */
						
						if(!CMS_Page_Current::$code_lang){

							// code langue = code langue par defaut du site
							if($code == DEFAULT_LANG_CODE){
								header("Status : 301 Moved Permanently", false, 301); 
							    header("location: " . $baseUrl . "/" . substr($requestUri, 3));
							    exit;
							}
							
							$current_lang_code 	= $code;
							$current_lang_id 	= array_search($current_lang_code, $availableLangs);
						}
						else 
							define('NOTFOUND', true);
					}
					else {
						define('NOTFOUND', true);
					}
				}
				else {
					define('NOTFOUND', true);
				}
			}

			define("CURRENT_LANG_ID", $current_lang_id);
			define("CURRENT_LANG_CODE", $current_lang_code);
		}
		

		if(!defined('CURRENT_LANG_ID') || defined('NOTFOUND') && NOTFOUND == true) {
			define('CURRENT_LANG_ID', DEFAULT_LANG_ID);
			define('CURRENT_LANG_CODE', DEFAULT_LANG_CODE);
		}
		
		

		if (!($this->_isPageAdmin || $this->_isAjax || (defined("DISABLE_CORE_PAGE") && DISABLE_CORE_PAGE === true))) {
			
			if(CMS_Page_Current::getInstance())
				CMS_Page_Current::pageInstanceToMonoLang();
			
			if (!$page->enable) {
		    	define('NOTFOUND', true);
			}
			else {
				// Si la page n'est pas visible, elle n'a pas de rewrite : skip !
				if($page->visible == 1) {
					
					if(defined('REDIRECT_URI_SYSTEM_TO_REWRITE') && REDIRECT_URI_SYSTEM_TO_REWRITE == true){
						
						$multiLangPage = CMS_Page_Current::getOriginalPage();
						
						// URL système demandée et rewrite existant
						if( $requestUriWithoutLang == $page->url_system && $multiLangPage->url_rewrite[CURRENT_LANG_ID]){
							//header("Status : 301 Moved Permanently", false, 301);
							header('location: ' . $baseUrl . '/' . $multiLangPage->url_rewrite[CURRENT_LANG_ID]);
							exit;
						}
						
						/** Redirection wildcard **/
						if ($page->isWildcard() && $page->url_system . $page->getUrlParams() == $requestUriWithoutLang && $multiLangPage->url_rewrite[CURRENT_LANG_ID]){
							// header("Status : 301 Moved Permanently", false, 301);
				    		header('location: ' . $baseUrl . '/' . $multiLangPage->url_rewrite[CURRENT_LANG_ID] . $page->getUrlParams());
				    		exit;
			        	}
					}
			    }
			    
			    if (!$this->_isHome)
				    $request->setRequestUri($baseUrl . '/' . $page->url_system . (($page->isWildcard()) ? $page->getUrlParams() : null));
			}
		}
	}	
	
	public function postDispatch(Zend_Controller_Request_Abstract $request) {
				
		$page = CMS_Page_Current::getInstance($request->getRequestUri());
		
		if ($page){
			if ($clone_rewrite_var_object = $page->__get('rewrite_var_object')) {
				$seo = new stdClass();
				$seo->title = $page->title ;
				$seo->description = $page->meta_description;
				$seo->keywords = $page->meta_keywords;
				
				foreach (get_object_vars($clone_rewrite_var_object) as $attr_name => $attr_val) {
					$seo->description 	= str_ireplace('%'.$attr_name.'%', $attr_val, $seo->description);
					$seo->keywords 		= str_ireplace('%'.$attr_name.'%', $attr_val, $seo->keywords);
					$seo->title 				= str_ireplace('%'.$attr_name.'%', $attr_val, $seo->title);
				}
							
				$this->_view->seoData = $seo;
			}
		}
	}
}
	