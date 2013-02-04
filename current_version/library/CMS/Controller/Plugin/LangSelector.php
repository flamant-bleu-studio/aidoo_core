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

class CMS_Controller_Plugin_LangSelector extends CMS_Controller_Plugin_Abstract_Abstract {
																					
	public function preDispatch(Zend_Controller_Request_Abstract $request){

		try {

			/*
			 * Chargement du fichier de langue
			 */
			$this->initVarEnv($request);
			
			$translateAPI = new CMS_Application_Translate();
			
			if($this->_isAdmin){
				$translate = $translateAPI->getBackTranslateObject();
				
// 				$config 		= CMS_Application_Config::getInstance();
				$backLangs 		= json_decode($this->_config->get("availableBackLang"), true);
				$code 			= $this->_request->getParam("lang");
				
				if(!$code)
					$code = $backLangs[$this->_config->get("defaultBackLang")];
				else {
					if(!in_array($code, $backLangs))
						throw new Zend_Exception('Language not found');
				}
				
				$translate->setLocale($code);
			
			}
			else {
				$translate = $translateAPI->getFrontTranslateObject(CURRENT_LANG_CODE);
				$translate->setLocale(CURRENT_LANG_CODE);
			}

			
			Zend_Registry::set('translate', $translate);
			
			/*
			 * Traduction des validateurs
			 */
	
			$defaultTranslate = new Zend_Translate(
			array(
				'adapter' => 'array',
				'content' => BASE_PATH.'/resources/languages',
				'locale'  => 'fr',
				'scan' => Zend_Translate::LOCALE_DIRECTORY
				)
			);
			Zend_Validate_Abstract::setDefaultTranslator($defaultTranslate);
			
		}catch(Exception $e){

			/**
			 * @todo La gestion des erreurs dans ce plugin n'est pas bien faite
			 */
			die($e->getMessage());
			
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

}