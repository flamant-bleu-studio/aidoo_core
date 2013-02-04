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

class CMS_Application_Translate {
	
	private static $_back_cache_prefix = "CMS_Translate_Back_";
	private static $_front_cache_prefix = "CMS_Translate_Front_";
	
	/**
	 * @var Zend_Cache_Core
	 */
	private static $_cacheObject;

	
	public function __construct(){
		self::instantiateCache();
	}
	
	/**
	 * Créer un fichier C contenant les chaines de caractères traduisibles
	 * extraitent des fichiers TPLs
	 * 
	 * @param array $files fichiers TPLs à analyser
	 * @param string $outputFile Fichier C où enregistrer les chaines récupérées
	 */
	public static function getTranslatableStringFromTPL($files, $outputFile){
	
		// extensions Smarty
		$extensions = array('tpl');
		$ldq = preg_quote('{');
		$cmd = preg_quote('t');
		$rdq = preg_quote('}');
		
		// Ouverture du fichier
		$file = new SplFileInfo($outputFile);
		$fileobj = $file->openFile('w');
		
		// "fix" string - strip slashes, escape and convert new lines to \n
		if( !function_exists(fs) ) {
			function fs($str)
			{
				$str = stripslashes($str);
				$str = str_replace('"', '\"', $str);
				$str = str_replace("\n", '\n', $str);
				return $str;
			}
		}
		
		foreach($files as $file) {
			
			// Uniquement si le fichier est un TPL
			if(substr($file, strrpos($file, '.') + 1) != "tpl"){
				continue;
			}
			
			$content = @file_get_contents($file);
			
			// Si le fichier n'est pas vide
			if (empty($content)) 
				continue;
		
			preg_match_all("/{$ldq}\s*({$cmd})\s*([^{$rdq}]*){$rdq}([^{$ldq}]*){$ldq}\/\\1{$rdq}/", $content, $matches);
			
			for ($i=0; $i < count($matches[0]); $i++) {
				if (preg_match('/plural\s*=\s*["\']?\s*(.[^\"\']*)\s*["\']?/', $matches[2][$i], $match)) {
					$fileobj->fwrite('ngettext("'.fs($matches[3][$i]).'","'.fs($match[1]).'",x);'."\n");
				} else {
					$fileobj->fwrite('gettext("'.fs($matches[3][$i]).'");'."\n");
				}
			}
		}
		
		return true;
	}
	
	public static function generateFrontPoFile($folderEntity, $type){
		
		$moduleConfig 		= $folderEntity . "/routes.xml";
		$blocConfig			= $folderEntity . "/bloc.xml";
		
		$langPath 			= $folderEntity . "/lang/front";
		$tradFromTPLFile 	= $langPath 	. "/trad.c";
		$poFile				= $langPath 	. "/lang.po";
		$newPoFile			= $langPath 	. "/lang_new.po";
		
		$config = CMS_Application_Config::getInstance();
		$langs = json_decode($config->get("availableFrontLang"), true);
		
		try{
			if($type == "module") {
				if(!file_exists($moduleConfig))
					return false;
				$configXml = new Zend_Config_Xml($moduleConfig, 'config');
			}
			else if($type == "bloc") {
				if(!file_exists($blocConfig))
					return false;
				$configXml = new Zend_Config_Xml($blocConfig);
			}
				
			$config = $configXml->toArray();
		}
		catch (Zend_Config_Exception $e){
			return false;
		}
		catch(Exception $e){
			//return false;

			//Debug :
			throw new Exception("Erreur dans la lecture du fichier de conf.");
		}

		if($config["lang"]['translatable'] != 'true')
			return false;
		

		if($config["lang"]["frontFiles"]["file"] || $config["lang"]["frontFiles"]["folder"]){
			
			// Récupération des fichiers et dossiers à parser
			$allFiles 		= $config["lang"]["frontFiles"]["file"];
			$allFolders 	= $config["lang"]["frontFiles"]["folder"];
						
			$phpToScan = array();
			$tplToScan = array();
			
			// Si il y a des dossiers à analyser
			if($allFolders){
				
				$foldersToScan = array();
				
				// Reset level si un seul element
				if(!is_int($key = @key($allFolders))){
					$allFolders = array($allFolders);
				}
			
				foreach($allFolders as $file){
					$foldersToScan[] = $folderEntity . $file["src"];
				} 
				
				foreach ($foldersToScan as $folder){
		
					if(!file_exists($folder))
						continue;
						
					$recursiveDirectoryIterator = new RecursiveDirectoryIterator($folder);

					foreach(new RecursiveIteratorIterator($recursiveDirectoryIterator, RecursiveIteratorIterator::SELF_FIRST) as $file) {
						
						// Récupération de l'extension du fichier
						$ext = substr($file->getFileName(), strrpos($file->getFileName(), '.') + 1);
						
						// On tri les TPL et les PHP
						if($ext == "tpl")
							$tplToScan[] = $file->getPathName();
						else if($ext == "php")
							$phpToScan[] = $file->getPathName();
					}
				}
				unset($recursiveDirectoryIterator);
				unset($foldersToScan);
			}
			
			if($allFiles){
				
				$filesToScan = array();
				
				// Reset level si un seul element
				if(!is_int($key = @key($allFiles))){
					$allFiles = array($allFiles);
				}
				
				foreach($allFiles as $file){
					$filesToScan[] = $folderEntity . $file["src"];
				}
		
				foreach ($filesToScan as $file){
		
					if(!file_exists($file))
						continue;
						
					// Récupération de l'extension du fichier
					$ext = substr($file, strrpos($file, '.') + 1);
					
					// On tri les TPL et les PHP
					if($ext == "tpl")
						$tplToScan[] = $file;
					else if($ext == "php")
						$phpToScan[] = $file;
				}
				
				unset($filesToScan);
			}
			
			// Génération du fichier contenant les chaines à traduire dans les TPLs
			if(CMS_Application_Translate::getTranslatableStringFromTPL($tplToScan, $tradFromTPLFile))
				$phpToScan[] = $tradFromTPLFile;

			/**
			 * @todo : Le retours des commandes exec sont concat dans la variable $output
			 */
				
			// Génération du nouveau PO avec les fichiers récupérés de la configuration du module
			exec("xgettext --force-po --from-code=UTF-8 --keyword='_t' -o ".$newPoFile." ". implode(" ", $phpToScan)." 2>&1", $output);
			
			// Si un ancien PO n'existe pas
			if(!file_exists($langPath."/".reset($langs).".po")){
				
				// Set charset
				exec("sed --in-place ".$newPoFile." --expression=s/CHARSET/UTF-8/", $output);
				
				// Copy du fichier PO vers toutes les langues disponibles
				foreach($langs as $id => $code){
					copy($newPoFile, $langPath."/".$code.".po");
				}
			}
			else {
				// Mise à jour de tous les fichiers PO
				foreach($langs as $id => $code){
					exec("msgmerge --update ".$langPath."/".$code.".po ".$newPoFile." 2>&1", $output);
				}
				
			}
			
			

			unlink($newPoFile);
			unlink($tradFromTPLFile);

		}
		
		return true;
	}
	
	public static function generateCompiledTranslateFile(){

		$langPath 		= "/lang/front";

		$config = CMS_Application_Config::getInstance();
		$langs = json_decode($config->get("availableFrontLang"), true);
		
		$poFiles = array();
		
		/*
		 * Modules
		 */
		
		$modulesPath 	= APPLICATION_PATH.'/modules/';
		
		foreach (new DirectoryIterator($modulesPath) as $moduleDir) // Liste tous les modules
		{
			if($moduleDir->isDir() && !$moduleDir->isDot()) {
				
				foreach($langs as $id => $code){
					if(file_exists($moduleDir->getPathName() . $langPath."/".$code.".po")){
						$poFiles[$code][] = $moduleDir->getPathName() . $langPath."/".$code.".po";
					}
				}
				
			}
		}
		
		/*
		 * Blocs
		 */
		
		$blocsPath 	= APPLICATION_PATH.'/blocs/';
		
		foreach (new DirectoryIterator($blocsPath) as $blocDir) // Liste tous les blocs
		{
			if($blocDir->isDir() && !$blocDir->isDot()) {
				
				foreach($langs as $id => $code){
					if(file_exists($blocDir->getPathName() . $langPath."/".$code.".po")){
						$poFiles[$code][] = $blocDir->getPathName() . $langPath."/".$code.".po";
					}
				}
				
			}
		}
		
		/*
		 * Generate .mo
		 */

		foreach($poFiles as $code => $files){
			
			$tmpFile = CMS_PATH."/tmp/upload/".$code.".po";
			
			touch($tmpFile);
			
			exec("msgcat --force-po --use-first -o ".$tmpFile." ".implode(" ", $files)." 2>&1", $output);
			exec("msgfmt -o ".CMS_PATH."/tmp/zend_cache/".self::_getFrontCacheID()."_".$code.".mo ".$tmpFile." 2>&1", $output);
			
			@unlink($tmpFile);
		}
		
		return true;
	}

	private static function _getBackCacheID(){
		return self::$_back_cache_prefix.UNIQUE_ID.MULTI_SITE_ID;
	}
	private static function _getFrontCacheID(){
		return self::$_front_cache_prefix.UNIQUE_ID.MULTI_SITE_ID;
	}
	
	/**
	 * 
	 * Retourne une instance de Zend_Translate configurée pour le back office
	 * 
	 * @return Zend_Translate
	 */
	public function getBackTranslateObject(){
		
		$datas = $this->_getBackDatas();
	
		$translate = null;
		
		foreach($datas as $code => $data){
			
			if(!$translate){
				$translate = new Zend_Translate(array(
			        'adapter' => 'array',
			        'locale'  => $code,
					'content' => $data
			    ));
			}
			else {
				$translate->addTranslation(array(
			        'content' => $data,
			        'locale'  => $code
			    ));
			}
		}
		
		return $translate;
	}
	
	public function getFrontTranslateObject($current_lang_code){
		
		$file = CMS_PATH."/tmp/zend_cache/".self::_getFrontCacheID()."_".$current_lang_code.".mo";
		
		if(!file_exists($file))
			self::generateCompiledTranslateFile();	
		
		$file = (file_exists($file)) ? $file : null;
		
		return new Zend_Translate(
			'gettext', 
			$file,
			$current_lang_code,
			array('disableNotices' => true)
		);
	}
	
	private function _getBackDatas(){

		if(!$datas = static::$_cacheObject->load(self::_getBackCacheID())){
			
			$datas = $this->_loadBackDatas();
			$this->_saveCache($datas, self::_getBackCacheID());
		}
		
		return $datas;
	}
	
	/**
	 * Retourne un tableau de chemin des dossiers modules et/ou blocs traduisibles
	 * @param string $type null, "blocs", "modules"
	 */
	public static function getTranslatableEntity($type = null){
		
		$modulePath 	= APPLICATION_PATH.'/modules/';
		$pathBlocs 		= APPLICATION_PATH.'/blocs/';
		
		$blocConfig 	= "/bloc.xml";
		$moduleConfig 	= "/routes.xml";

		$folders = array();
		
		if(!$type || $type == "modules") {
			foreach (new DirectoryIterator($modulePath) as $fileInfo) // Liste tous les modules
			{
				if($fileInfo->isDir() && !$fileInfo->isDot()) {
					
					$path = $fileInfo->getPathName();
					
					if(!file_exists($path . $moduleConfig))
						continue;
					
					try{
						$configXml = new Zend_Config_Xml($path . $moduleConfig, 'config');
						$config = $configXml->toArray();
					}
					catch (Exception $e){
						continue;
					}
			
					if($config["lang"]['translatable'])
						$folders[] = $path;
				
				}
			}
		}

		if(!$type || $type == "blocs") {
			foreach (new DirectoryIterator($pathBlocs) as $fileInfo) // Liste tous les blocs
			{
				if($fileInfo->isDir() && !$fileInfo->isDot()) {
	
					$path = $fileInfo->getPathName();
					
					if(!file_exists($path . $blocConfig))
						continue;
					
					try{
						$configXml = new Zend_Config_Xml($path . $blocConfig);
						$config = $configXml->toArray();
					}
					catch (Exception $e){
						continue;
					}
			
					if($config["lang"]['translatable'])
						$folders[] = $path;
				}
			}
		}
		
		return $folders;
	}
	
	private function _loadBackDatas(){
		
		$config = CMS_Application_Config::getInstance();
		$langs = json_decode($config->get("availableBackLang"), true);
		
		/**
		 * @todo Gérer les fichiers de traductions manquants
		 */
		$missingTranslateFiles = array();
		$translateArray = array();
		
		/*
		 * Modules
		 */
		
		$pathModules = APPLICATION_PATH.'/modules/';
		
		foreach (new DirectoryIterator($pathModules) as $fileInfo) // Liste tout les modules
		{
			if($fileInfo->isDir() && !$fileInfo->isDot()) {
				
				$modulePath = $pathModules . $fileInfo->getFilename();
				$configFile = $modulePath . '/routes.xml';
				
				if(!file_exists($configFile))
					continue;
					
				try{
					$configXml = new Zend_Config_Xml($pathModules . $fileInfo->getFilename() . '/routes.xml', 'config');
					$config = $configXml->toArray();
				}
				catch (Zend_Config_Exception $e){
					continue;
				}
				catch(Exception $e){
					//continue;

					//Debug :
					throw new Exception("Erreur dans la génération du cache des langues du back office");
				}
				
				if($config["lang"]['translatable'] != 'true')
					continue;
				
				foreach($langs as $id => $code){
					if(!file_exists($modulePath."/lang/back/".$code.".php")){
						$missingTranslateFiles[$fileInfo->getFilename()][] = $code;
					}
					else{
						
						$tempArray = include $modulePath."/lang/back/".$code.".php";
						
						if(!is_array($translateArray[$code]))
							$translateArray[$code] = array();
							
						$translateArray[$code] = array_merge($translateArray[$code], $tempArray);
						
					}
				}
				
			}
		}
		
		/*
		 * Blocs
		 */
		
		$pathBlocs = APPLICATION_PATH.'/blocs/';
		
		foreach (new DirectoryIterator($pathBlocs) as $fileInfo) // Liste tout les blocs
		{
			if($fileInfo->isDir() && !$fileInfo->isDot()) {
				
				$blocPath = $pathBlocs . $fileInfo->getFilename();
				$configFile = $blocPath . '/bloc.xml';
				
				if(!file_exists($configFile))
					continue;
					
				try{
					$configXml = new Zend_Config_Xml($pathBlocs . $fileInfo->getFilename() . '/bloc.xml');
					$config = $configXml->toArray();
				}
				catch (Zend_Config_Exception $e){
					continue;
				}
				catch(Exception $e){
					//continue;

					//Debug :
					throw new Exception("Erreur dans la génération du cache des langues du back office");
				}
				
				if(!isset($config["lang"]['translatable']) || $config["lang"]['translatable'] != 'true')
					continue;
					
				foreach($langs as $id => $code){
					if(!file_exists($blocPath."/lang/back/".$code.".php")){
						$missingTranslateFiles[$fileInfo->getFilename()][] = $code;
					}
					else{
						
						$tempArray = include $blocPath."/lang/back/".$code.".php";
						
						if(!is_array($translateArray[$code]))
							$translateArray[$code] = array();
							
						$translateArray[$code] = array_merge($translateArray[$code], $tempArray);
						
					}
				}
				
			}
		}
		
		return $translateArray;
	}
	
	private function _removeCache($cacheID){
		self::$_cacheObject->remove($cacheID);
	}
	
	private function _saveCache($datas, $cacheID){
		self::$_cacheObject->save($datas, $cacheID);
	}
	
	public function cleanCache(){
		$this->_removeCache(self::_getBackCacheID());
		$this->_saveCache($this->_getBackDatas(), self::_getBackCacheID());
	}

	private static function instantiateCache(){
		
		if(!self::$_cacheObject) {
			$frontend = array(
	            'lifetime' => 3600 * 24,
	            'automatic_serialization' => true
			);
			$backend = array(
				'cache_dir' => CMS_PATH.'/tmp/zend_cache/'
			);
	
			if (extension_loaded('apc'))
				static::$_cacheObject = Zend_Cache::factory('Core', 'apc', $frontend, $backend);
			else 
				static::$_cacheObject = Zend_Cache::factory('Core', 'file', $frontend, $backend);
		}
	}
	
}
