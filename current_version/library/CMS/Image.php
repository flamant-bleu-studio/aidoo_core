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

require_once BASE_PATH.'/library/PHPThumb/ThumbLib.inc.php';

class CMS_Image
{
	private $configThumbSizes;
	private $allowedExtensions;
	
	private $uploadUrl;
	private $uploadPath;
	
	private $uploadThumbsUrl;
	private $uploadThumbsPath;
	
	private $folder;
	
	/**
	 * @param array $allowedExtensions
	 * @param string $uploadPath
	 */
	public function __construct(array $options = null)
	{
		if ($options) {
			if (isset($options['allowedExtensions']))
				$this->setAllowedExtensions($options['allowedExtensions']);
			
			if (isset($options['uploadPath']))
				$this->setUploadPath($options['uploadPath']);
		}
	}
	
	/**
	 * Charge les extensions acceptées
	 * @param array $allowedExtensions
	 */
	public function setAllowedExtensions(array $allowedExtensions)
	{
		$this->allowedExtensions = $allowedExtensions;
	}
	
	public function setUploadPath($folder)
	{
		if($folder[0] != '/')
			$folder = '/' . $folder;
		
        $this->uploadPath = self::generateUploadPath($folder);
		$this->uploadUrl  = self::generateUploadUrl($folder);
		
		$this->uploadThumbsPath = self::generateUploadThumbsPath($folder);
		$this->uploadThumbsUrl  = self::generateUploadThumbsUrl($folder);
		
		$this->folder = $folder;
	}
	
	private static function generateUploadFolderPath()
	{
		return PUBLIC_PATH . MULTIUPLOAD_FOLDER;
	}
	
	private static function generateUploadPath($folder)
	{
		return PUBLIC_PATH . MULTIUPLOAD_FOLDER . $folder . '/';
	}
	
	private static function generateUploadUrl($folder)
	{
		return  BASE_URL . MULTIUPLOAD_FOLDER . $folder . '/';
	}
	
	private static function generateUploadThumbsPath($folder)
	{
		return PUBLIC_PATH . MULTIUPLOAD_FOLDER . $folder . '/thumbs/';
	}
	
	private static function generateUploadThumbsUrl($folder)
	{
		return  BASE_URL . MULTIUPLOAD_FOLDER . $folder . '/thumbs/';
	}
	
	/**
	 * Renvoit le lien de l'image
	 * @param string $folder Dossier resource d'upload
	 * @param string $name Nom de l'image
	 * @param string $configSize Nom de la miniature souhitée
	 */
	public static function getLink($folder, $name, $configSize = null)
	{
		if ($folder[0] != '/')
			$folder = '/' . $folder;
		
		if ($configSize !== null) {
			// Si la miniature demandée n'existe pas, on renvoit l'image non miniaturisé
			if (!file_exists(self::generateUploadThumbsPath($folder) . $configSize . '-' . $name))
				$configSize = null;
		}
		
		// L'image de base (non miniature)
		if ($configSize === null) {
			
			// Si l'image de base n'existe pas : retourne l'image par defaut configuré
			if (!file_exists(self::generateUploadPath($folder) . $name)) {
				$config = CMS_Application_Config::getInstance();
				return self::generateUploadUrl('') . $config->get('defaultImage');
			}
			
			return self::generateUploadUrl($folder) . $name;
		}
		
		// Un des formats miniature de l'image
		return self::generateUploadThumbsUrl($folder) . $configSize . '-' . $name;
	}
	
	/**
	 * Charge les tailles des images configurées 
	 */
	private function loadConfigThumbSizes()
	{
		$this->configThumbSizes = json_decode(CMS_Application_Config::getInstance()->get('configThumbSizes'), true);
	    
		if (!$this->configThumbSizes)
	    	throw new Exception(_t('Thumb sizes configuration is empty'));
	}
	
	public function upload()
	{
		$this->_checkConfig();
		$this->_checkConfigUpload();
		
		// Adapter de transfert de fichier + option
        $adapter = new Zend_File_Transfer_Adapter_Http();
        
        // Destination d'upload
        $adapter->setDestination($this->uploadPath);
        
        // Extensions de fichiers valides
        if (!empty($this->allowedExtensions))
	        $adapter->addValidator('Extension', true, $this->allowedExtensions);
		
	    $files 		= $adapter->getFileInfo();
        $file 		= key($files);
        $fileInfo 	= reset($files);
	    
	    $fileclass 	= new stdClass();
        
        $fileclass->oldName = $fileInfo['name'];
        
		$pos = strrpos($fileclass->oldName, '.');
		$fileclass->basename 	= substr($fileclass->oldName, 0, $pos);
        $fileclass->extension 	= substr($fileclass->oldName, $pos+1);
        
		// Upload et validation
		if (!$adapter->isUploaded($file))
			$fileclass->error = _t("File has not been uploaded");
		if (!$adapter->isValid($file)) {
			$errors = $adapter->getErrors();
			$error = reset($errors);
			
			if($error == "fileExtensionFalse")
				$fileclass->error = _t("This file extension isn't allowed");
			else 
				$fileclass->error = $error;
		}
		
		// Renommer le fichier
		$fileclass->name = $fileclass->basename . '_' . time() . '.' . $fileclass->extension;
		$adapter->addFilter('Rename', $fileclass->name);
		
		if ($fileclass->error)
			return $fileclass;
		
		// Réception dans le dossier de destination
		$adapter->receive($file);
		
		// Récupération du chemin après la récéption pour récupérer le chemin après que les filtres (rename, ...) est pris effet
		$fileclass->filePathName = $adapter->getFileName($file);
		
		$fileclass->size 		= CMS_Application_Tools::formatSize($fileInfo["size"]);
		$fileclass->type 		= $adapter->getMimeType($file);
		
		$fileclass->delete_url 	= BASE_URL.'/ajax/admin_upload/delete/?folder='. $this->folder .'&name=' . $fileclass->name;
		$fileclass->delete_type = "POST";
		
		$currentDomain = CMS_Application_Tools::getCurrentDomain();
		
		$fileclass->url = $currentDomain . $this->uploadUrl . $fileclass->name;
		
		if ($this->isValidExtention($fileclass->extension))
			$this->generateThumbs($fileclass->filePathName, $fileclass->name);
		
		$fileclass->thumbnail_url = self::getLink($this->folder, $fileclass->name, 'default');
		
		return $fileclass;
	}
	
	/**
	 * Vérifie que l'extension fournit en paramètre correspond à une extension d'image
	 * @param string $ext
	 */
	public function isValidExtention($ext)
	{
		return (in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif', 'bmp'))) ? true : false;
	}
	
	private function _checkConfig()
	{
		if (!$this->uploadPath)
			throw new Exception(_t("Invalid uploadPath"));
		
		if (!$this->uploadUrl)
			throw new Exception(_t("Invalid uploadUrl"));
	}
	
	private function _checkConfigUpload()
	{
		// Création du répertoire d'upload
        if (!file_exists($this->uploadPath))
        	if (!@mkdir($this->uploadPath))
        		throw new Exception(_t("Unable to create upload folder") . " : " . $this->uploadPath);
        
        // Création du répertoire des mignatures
        if (!file_exists($this->uploadThumbsPath))
        	if (!@mkdir($this->uploadThumbsPath))
        		throw new Exception(_t("Unable to create thumbs upload folder") . " : " . $this->uploadThumbsPath);
	}
	
	/**
	 * Re-génère les miniatures du dossier 'MULTIUPLOAD_FOLDER'
	 */
	public function reGenerateThumbsAllFolders()
	{
		$directoryResource = new DirectoryIterator(self::generateUploadFolderPath());
		
		foreach ($directoryResource as $file) {
			if (!$file->isDir() || $file->isDot())
				continue;
			
			$this->reGenerateThumbs($file->getFileName());
		}
	}
	
	/**
	 * Re-génère les miniatures d'un dossier resource précis
	 * @param string $folder Nom du dossier resource
	 */
	private function reGenerateThumbs($folder)
	{
		if ($folder[0] != '/')
			$folder = '/' . $folder;
		
		$this->loadConfigThumbSizes();
		$this->setUploadPath($folder);
		
		// Suppréssion des miniatures existantes
		$directoryThumbs = new DirectoryIterator($this->uploadThumbsPath);
		
		foreach ($directoryThumbs as $file) {
			if (!$file->isFile())
				continue;
			
			/*$fileInfo = new SplFileInfo($file->getPathName());
			if (!self::isValidExtention($fileInfo->getExtension()))
				continue;*/
			
			try {
				@unlink($file->getPathName());
			}
			catch (Exception $e) {
				//Zend_Registry::get('log')->warn("CMS_Image - reGenerateThumbs() - " . $e->getMessage());
			}
		}
		
		// Génération des miniatures
		$directory = new DirectoryIterator($this->uploadPath);
		
		foreach ($directory as $file) {
			if (!$file->isFile())
				continue;
			
			/*$fileInfo = new SplFileInfo($file->getPathName());
			if (!self::isValidExtention($fileInfo->getExtension()))
				continue;*/
			
			try {
				$this->generateThumbs($file->getPathName(), $file->getFileName());
			}
			catch (Exception $e) {
				//Zend_Registry::get('log')->warn("CMS_Image - reGenerateThumbs() - " . $e->getMessage());
			}
		}
	}
	
	/**
	 * Génère les miniatures de l'image spécifiée
	 * @param string $filePathName Image source
	 * @param string $nameFile Nom de l'image à créer
	 */
	private function generateThumbs($filePathName, $nameFile = null)
	{
		// Si pas de nom pour l'image à créer, récupération du nom de l'image source
		if (!$nameFile) {
			$nameFile = substr($filePathName, (strrpos($filePathName, '/') + 1));
		}
		
		$this->_checkConfig();
		$this->_checkConfigUpload();
		$this->loadConfigThumbSizes();
		
		foreach ($this->configThumbSizes as $configName => $opts) {
			
			$thumb = PhpThumbFactory::create($filePathName);
			
			// Si l'ont doit redimenssioner ou conserver les proportions
			if($opts['adaptiveResize'] == true)
				$thumb->adaptiveResize($opts['width'], $opts['height']);
			else
				$thumb->resize($opts['width'], $opts['height']);
			
			$thumb->save($this->uploadThumbsPath . $configName . "-" . $nameFile);
		}
	}
	
	public static function delete($folder, $name)
	{
		if ($folder[0] != '/')
			$folder = '/' . $folder;
		
		$path 		= self::generateUploadPath($folder);
		$pathThumb 	= self::generateUploadThumbsPath($folder);
		
		try {
			@unlink($path.$name);
			
			$configThumbSizes = json_decode(CMS_Application_Config::getInstance()->get('configThumbSizes'), true);
			
			foreach ($configThumbSizes as $configName => $opts)
				@unlink($pathThumb . $configName . "-" . $name);
		}
		catch (Exception $e) {
			//Zend_Registry::get('log')->warn("CMS_Image - delete() - " . $e->getMessage());
		}
	}
}