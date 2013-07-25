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

class Admin_LangController extends CMS_Controller_Action {

	public function indexAction() {
		
		$this->redirectIfNoRights('admin', 'view');
		
		$config = CMS_Application_Config::getInstance();
		
		$backLangs 		= json_decode($config->get("availableBackLang"), true);
		$backDefault 	= $config->get("defaultBackLang");
		
		$frontLangs = json_decode($config->get("availableFrontLang"), true);
		$frontDefault = $config->get("defaultFrontLang");

		$this->view->backLangs = $backLangs;
		$this->view->backDefault = $backDefault;
		$this->view->frontLangs = $frontLangs;
		$this->view->frontDefault = $frontDefault;

		$allLanguages = Zend_Locale::getTranslationList('Language');

		$new = array();
		foreach ($allLanguages as $key => $value) {
			if(strlen($key) <= 2 && Zend_Locale::isLocale($key, true))
				$new[$key] = $value;
		}
		asort($new, SORT_LOCALE_STRING);

		$this->view->liste = $new;
		
	}
	public function checkFilesAction(){
		
		$warnings = $this->checkFiles();
		
		if(!$warnings)
			_message(_t("All translations files and permissions are OK"));
		else {
			if($warnings['missing']){
				_warning(_t("Files missing") . " :");
				foreach($warnings['missing'] as $file){
					_warning(" - " .$file);
				}
			}
			if($warnings['notwritable']){
				_warning(_t("Folders not writable") . " :");
				foreach($warnings['notwritable'] as $file){
					_warning(" - " .$file);
				}
			}
		}
		
		
		return $this->_redirect( $this->_helper->route->short('index', array('controller' => 'lang')));
	}
	
	public function checkFiles($type = null){
		
		if($type !== null && $type != "front" && $type != "back")
			throw new Exception(_t("Invalid type"));
		
		$config 	= CMS_Application_Config::getInstance();
		$frontLang 	= json_decode($config->get("availableFrontLang"), true);
		$backLang 	= json_decode($config->get("availableBackLang"), true);
		
		$allFolders = array(
			CMS_Application_Translate::getTranslatableEntity("blocs"),
			CMS_Application_Translate::getTranslatableEntity("modules")
		);
		
		$missing = array();
		$notWritable = array();
		
		foreach($allFolders as $folders){
			foreach($folders as $folder){
				
				if($type == "front" || !$type)
					if(!is_writable($folder."/lang/front"))
						$notWritable[] = $folder."/lang/front/";
						
				if($type == "back" || !$type)
					if(!is_writable($folder."/lang/back"))
						$notWritable[] = $folder."/lang/back/";
						
				if($type == "front" || !$type)
					foreach($frontLang as $id_lang => $code_lang)
						if(!file_exists($folder."/lang/front/" . $code_lang . ".po"))
							$missing[] = $folder."/lang/front/" . $code_lang . ".po";
				
				if($type == "back" || !$type)
					foreach($backLang as $id_lang => $code_lang)
						if(!file_exists($folder."/lang/back/" . $code_lang . ".php"))
							$missing[] = $folder."/lang/back/" . $code_lang . ".php";
			}
		}

		if( empty($missing) && empty($notWritable))
			return null;
			
		return array(
			"missing" 		=> $missing,
			"notwritable" 	=> $notWritable
		);
		
	}
	
	public function duplicateLangFile($type, $from_code_lang, $to_code_lang){
		
		if($type != "front" && $type != "back")
			throw new Exception(_t("Invalid type"));
			
		$folders = CMS_Application_Translate::getTranslatableEntity();
		$return = array();
		
		$ext = ($type == "front") ? ".po" : ".php" ;

		foreach($folders as $folder){
						
			if(!is_writable($folder . "/lang/" . $type) || !file_exists($folder."/lang/" . $type . "/" . $from_code_lang . $ext))
				$return[] = $folder."/lang/" . $type . "/" . $to_code_lang . $ext;
				
			if(!file_exists($folder."/lang/" . $type . "/" . $to_code_lang . $ext))
				copy($folder."/lang/" . $type . "/" . $from_code_lang . $ext, $folder."/lang/" . $type . "/" . $to_code_lang . $ext);
			
		}
		
		return $return;
	}
	
	public function removeLangFile($type, $code_lang){
		
		if($type != "front" && $type != "back")
			throw new Exception(_t("Invalid type"));
			
		$folders = CMS_Application_Translate::getTranslatableEntity();
		$return = array();
		
		$ext = ($type == "front") ? ".po" : ".php" ;

		foreach($folders as $folder){
						
			if(!is_writable($folder . "/lang/" . $type))
				$return[] = $folder."/lang/" . $type . "/" . $code_lang . $ext;
				
			@unlink($folder."/lang/" . $type . "/" . $code_lang . $ext);
			
		}
		
		return $return;
	}
	
	public function addlanguageAction() {
		$backAcl = CMS_Acl_Back::getInstance();

		if($backAcl->hasPermission("admin", "view")) {

			if($this->getRequest()->isPost()) {

				$from_lang_id 	= (int)$_POST['from_lang_id'];
				$lang 			= $_POST['lang'];
				$type 			= $_POST['type'];

				if(!Zend_Locale::isLocale($lang, true) || !$from_lang_id)
					throw new Zend_Exception(_t("Invalid Language ID"));
				
				$config = CMS_Application_Config::getInstance();
					
				if($type == "front"){
					
					$frontLang = json_decode($config->get("availableFrontLang"), true);
	
					if(!in_array($lang, $frontLang)) {
						
						/*
						 * Enregistrement de la langue en config
						 */
						$frontLang[] = $lang;
						$config->set("availableFrontLang", json_encode($frontLang));
						
						$from_lang_code = $frontLang[$from_lang_id];
						$to_id_lang = array_search($lang, $frontLang);
						
						/*
						 * Duplication de la langue en BDD
						 */
						$model = new Admin_Model_DbTable_Lang();
						$tableList = $model->listLangTable();
				
						foreach($tableList as $name){
							$model->duplicateLang($name, $from_lang_id, $to_id_lang);
						}
		
						/*
						 * Création des fichiers de la nouvelle langue
						 */
						$warningFiles = $this->duplicateLangFile($type, $from_lang_code, $lang);
						
						/*
						 * Mise à jour de cache core_pages
						 */
						CMS_Page_Object::updateCache();
						
						_message(_t("Language added"));
					}
					else
						_error(_t("this language is already existing"));
				}
				else if($type == "back"){
					
					$backLang = json_decode($config->get("availableBackLang"), true);
	
					if(!in_array($lang, $backLang)) {
						
						/*
						 * Enregistrement de la langue en config
						 */
						$backLang[] = $lang;
						$config->set("availableBackLang", json_encode($backLang));
						
						$from_lang_code = $backLang[$from_lang_id];
						
						/*
						 * Création des fichiers de la nouvelle langue
						 */
						$warningFiles = $this->duplicateLangFile($type, $from_lang_code, $lang);
						
						_message(_t("Language added"));
					}
					else
						_error(_t("this language is already existing"));
				}
					
				if(!empty($warningFiles)){
	
					_error(_t("Unable to create translation files") . " :");
					foreach($warningFiles as $file){
						_error(" - " .$file);
					}
				
				}
				
				return $this->_redirect( $this->_helper->route->short('index', array('controller' => 'lang')));
			}
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function deleteFrontLangAction() {
		$backAcl = CMS_Acl_Back::getInstance();

		if($backAcl->hasPermission("admin", "view")) {
			$lang = $this->_request->getParam('id');

			if(Zend_Locale::isLocale($lang, true)) {
				$config = CMS_Application_Config::getInstance();

				$languages = json_decode($config->get("availableFrontLang"), true);

				if(!in_array($lang, $languages))
					throw new Zend_Exception('Language not found');

				$id_lang = array_search($lang, $languages);
				unset($languages[$id_lang]);

				$config->set("availableFrontLang", json_encode($languages));

				$model = new Admin_Model_DbTable_Lang();
				$tableList = $model->listLangTable();
		
				foreach($tableList as $name){
					$model->deleteLang($name, $id_lang);
				}
		
				CMS_Page_Object::updateCache();
				
				$this->removeLangFile("front", $lang);
				
				_message(_t("Language deleted"));

				return $this->_redirect( $this->_helper->route->short('index', array('controller' => 'lang', 'id' => null)));

			}
			else
			throw new Zend_Exception('Language id invalid');
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	public function deleteBackLangAction() {
		$backAcl = CMS_Acl_Back::getInstance();

		if($backAcl->hasPermission("admin", "view")) {
			$lang = $this->_request->getParam('id');

			if(Zend_Locale::isLocale($lang, true)) {
				$config = CMS_Application_Config::getInstance();

				$languages = json_decode($config->get("availableBackLang"), true);

				if(!in_array($lang, $languages))
					throw new Zend_Exception('Language not found');

				$id_lang = array_search($lang, $languages);
				unset($languages[$id_lang]);

				$config->set("availableBackLang", json_encode($languages));
	
				$this->removeLangFile("back", $lang);
				
				_message(_t("Language deleted"));

				return $this->_redirect( $this->_helper->route->short('index', array('controller' => 'lang', 'id' => null)));

			}
			else
			throw new Zend_Exception('Language id invalid');
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function defaultFrontLangAction() {
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("admin", "view")) {
			
			$lang = $this->_request->getParam('id');

			if(Zend_Locale::isLocale($lang, true)) {
				
				$config = CMS_Application_Config::getInstance();
				$languages = json_decode($config->get("availableFrontLang"), true);

				if(!in_array($lang, $languages))
					throw new Zend_Exception('Language not found');

				$config->set("defaultFrontLang", array_search($lang, $languages));
				_message(_t("Front office default language changed"));
				
				return $this->_redirect($this->_helper->route->short('index', array('controller' => 'lang', 'id' => null)));
			}
			else
				throw new Zend_Exception(_t('Invalid code lang'));

		}
		else {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}

	}
	public function defaultBackLangAction() {
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("admin", "view")) {
			
			$lang = $this->_request->getParam('id');

			if(Zend_Locale::isLocale($lang, true)) {
				
				$config = CMS_Application_Config::getInstance();
				$languages = json_decode($config->get("availableBackLang"), true);

				if(!in_array($lang, $languages))
					throw new Zend_Exception('Language not found');

				$config->set("defaultBackLang", array_search($lang, $languages));
				_message(_t("Back office default language changed"));
				
				return $this->_redirect($this->_helper->route->short('index', array('controller' => 'lang', 'id' => null)));
			}
			else
				throw new Zend_Exception(_t('Invalid code lang'));

		}
		else {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}

	}	
	
	
	public function cleanCacheBackAction()
	{

		$translateApp = new CMS_Application_Translate();
		$translateApp->cleanCache();

		_message(_t("Back-office cache cleaned"));
		return $this->_redirect( $this->_helper->route->short('index', array("controller" => "lang")));
	}

	public function cleanTranslateFrontFilesAction(){
		
		$warnings = $this->checkFiles();
		
		/*if($warnings['notwritable']){
			_error(_t("Folders not writable") . " :");
			foreach($warnings['notwritable'] as $file){
				_error(" - " .$file);
			}
			
			//return $this->_redirect( $this->_helper->route->short('index', array("controller" => "lang")));
		}*/
		
		if( ini_get('safe_mode') )
			die("Safe_mode activé : Vérifier que la fonction php 'exec' fonctionne bien ...");

		/*
		 * Modules
		 */
			
		$folders = CMS_Application_Translate::getTranslatableEntity("modules");
		
		foreach ($folders as $folder) // Liste tous les modules
			CMS_Application_Translate::generateFrontPoFile($folder, "module");
		
		/*
		 * Blocs
		 */
		
		$folders = CMS_Application_Translate::getTranslatableEntity("blocs");
		
		foreach ($folders as $folder) // Liste tous les blocs
			CMS_Application_Translate::generateFrontPoFile($folder, "bloc");
	
		return $this->_redirect( $this->_helper->route->short('index', array("controller" => "lang")));
	}
	
	public function generateCachedTranslateFrontFileAction(){
		
		if( ini_get('safe_mode') )
			die("Safe_mode activé : Vérifier que la fonction php 'exec' fonctionne bien ...");
			
		$warnings = $this->checkFiles();
		
		if($warnings){
			if($warnings['missing']){
				_warning(_t("Files missing, translation cache incomplete") . " :");
				foreach($warnings['missing'] as $file){
					_warning(" - " .$file);
				}
			}
		}
		
		CMS_Application_Translate::generateCompiledTranslateFile();

		return $this->_redirect( $this->_helper->route->short('index', array("controller" => "lang")));
	}


}