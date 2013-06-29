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

class Admin_ConfigController extends CMS_Controller_Action
{
	public function indexAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if(!$backAcl->hasPermission("admin", "manage")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		$config = CMS_Application_Config::getInstance();
		

		$mobileForm 	= new Admin_Form_MobileConfig();
		$apiKey 			= $config->get('apiKey');

		$mobileForm = new Admin_Form_MobileConfig();
		$logForm	= new Admin_Form_LogConfig();

		
		if($this->getRequest()->isPost()) {
			
			$sizeLst = array();
			
			if(is_array($_POST['name']) && !empty($_POST['name'])){
				foreach($_POST['name'] as $key => $name){
					
					$name = strtolower(htmlspecialchars($name));
					$width = (int)$_POST['height'][$key];
					$height = (int)$_POST['height'][$key];
					
					if($name && $width && $height){
						$sizeLst[$name] = array(
							'name' => $name,
							'width' => (int)$_POST['width'][$key],
							'height' => (int)$_POST['height'][$key],
							'adaptiveResize' => (($_POST['adaptiveResize'][$key] == 'on') ? true : false)
						);
					}
				}
				
				if(!isset($sizeLst["default"]))
					throw new Exception(_t("One configuration must be named 'default'"));
			}

			$config->set("configThumbSizes", json_encode($sizeLst));

			_message(_t('Thumbs sizes updated'));
			return $this->_redirect( $this->_helper->route->short('index'));

		}
		else {
			
			$sizeLst = json_decode($config->get('configThumbSizes'), true);
			
			if(!is_array($sizeLst) || empty($sizeLst)){
				
				$sizeLst = array(array(
					'name' => "default",
					'width' => 150,
					'height' => 100,
					'adaptiveResize' => false
				));
				
				$config->set("configThumbSizes", json_encode($sizeLst));
			}
			
			$this->view->thumbs_sizes = $sizeLst;
			
			$datas = @json_decode($config->get('mobileConfig'), true);
			
			if(is_array($datas) && !empty($datas))
				$mobileForm->populate($datas);
			
			/**
			 * Populate formulaire de configuration des logs
			 */
			
			$logConfig = json_decode($config->get('logConfig'), true);
			
			if($logConfig) {
				$logForm->populate($logConfig);
			}
		}
		
		$mobileForm->setAction($this->_helper->route->short('save-mobile-option'));
		$this->view->mobileForm = $mobileForm;

		$this->view->apiKey = $apiKey;

		$logForm->setAction($this->_helper->route->short('save-log-option'));
		$this->view->logForm = $logForm;

		
		// Rights Management !
		$formAcl = new CMS_Acl_Form_BackAclForm("admin");
		$formAcl->setAction(BASE_URL.$this->_helper->route->short('update-acl'));
		$formAcl->addSubmit(_t("Submit"));

		$this->view->maintenance =$config->get('maintenance');
		$this->view->formAcl = $formAcl;
	}
	
	public function saveLogOptionAction()
	{
		$this->redirectIfNoRights('admin', 'manage');
		
		$form = new Admin_Form_LogConfig();
		
		if($form->isValid($_POST)){
			$config = CMS_Application_Config::getInstance();
			
			$config->set("logConfig", json_encode($form->getValues()));
			
			_message(_t('Options updated'));
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	public function saveMobileOptionAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		
		if(!$backAcl->hasPermission("admin", "manage")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		if($this->getRequest()->isPost()) {
			
			$mobileForm = new Admin_Form_MobileConfig();
			
			if($mobileForm->isValid($_POST)){
				$config = CMS_Application_Config::getInstance();
				
				$config->set("mobileConfig", json_encode($mobileForm->getValues()));
				
				_message(_t('Options updated'));
			}
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	

	public function generateapikeyAction()
	{
		$config = CMS_Application_Config::getInstance();
		$config->set('apiKey', md5(uniqid()));
		_message(_t('Api Key generated'));
		return $this->_redirect( $this->_helper->route->short('index'));
	}



	public function regeneratePicturesAction()
	{
		set_time_limit(0);
		
		$classImage = new CMS_Image();
		$classImage->reGenerateThumbsAllFolders();
		
		_message(_t('Regeneration of pictures finished'));
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	

	public function confirmresetcontentAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();

		if(!$backAcl->hasPermission("admin", "manage"))
		{
			return $this->_redirect( $this->_helper->route->short('index'));
		}
	}
	
	public function maintenanceAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
	
		if(!$backAcl->hasPermission("admin", "manage"))
		{
			return $this->_redirect( $this->_helper->route->short('index'));
		}
		
		$maint = new Admin_Model_DbTable_Config();
		if ($this->_request->getParam('id') == 1) {
			$maint->setConfigItem('maintenance', 1);
			_message(_t("Maintenance enable"));
		} else {
			$maint->setConfigItem('maintenance', 0);
			_message(_t("Maintenance disable"));
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}


	public function resetcontentAction()
	{

		$backAcl = CMS_Acl_Back::getInstance();

		if(!$backAcl->hasPermission("admin", "manage"))
		{
			return $this->_redirect( $this->_helper->route->short('index'));
		}

		if($_POST["bdd"] || $_POST["resource"])
		{
			$bdd = $_POST["bdd"];
			if(isset($bdd) && $bdd = "on")
			{
				$return	= $this->installDatabaseFromSQLScript();
			}
			
			$resource = $_POST["resource"];
			if(isset($resource) && $resource = "on")
			{
				$return = $this->removeResource();
			}
			
			if ( $return == true )
				_message(_t("All content had been erased"));
		}

		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function removeResource()
	{	
		if (!is_dir(PUBLIC_PATH . UPLOAD_FOLDER)) {
			_error('Directory "' . PUBLIC_PATH . UPLOAD_FOLDER . '" not found !');
			return false;
		}
		
		$this->removeDirectory(PUBLIC_PATH . UPLOAD_FOLDER, false);
		
		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $pathDirectory
	 * @param $deleteRacine
	 */
	private function removeDirectory($pathDirectory, $deleteRacine = true)
	{
    	$directory = new DirectoryIterator($pathDirectory);

    	foreach ($directory as $fileinfo) {
    		
        	if ($fileinfo->isFile() || $fileinfo->isLink())
            	unlink($fileinfo->getPathName());
            
        	elseif (!$fileinfo->isDot() && $fileinfo->isDir())
            	$this->removeDirectory($fileinfo->getPathName());
            	
    	}
		
    	if($deleteRacine)
	    	rmdir($pathDirectory);
	}
	
	/**
	 * 
	 * Return in array all querys in the file
	 * @param string $pathFile
	 * @param string $delimiter
	 */
	private  function returnQuerys ( $pathFile , $delimiter = ';')
	{
		$file = new SplFileObject($pathFile, 'r');
		
		foreach ($file as $line)
		{
		    if( (substr($line, 0, 2) == '--') || (trim($line) == '') ) continue; // Retire les commentaires et les lignes vides
		    $content .= trim($line);
		}
		
		$querys = explode($delimiter, $content); // Separe les requetes
		
		return $querys;
	}
	
	/**
	 *
	 */
	private function installDatabaseFromSQLScript()
	{
		$db = Zend_Registry::get('db');
		if ($db)
		{
			$pathModules = APPLICATION_PATH.'/modules/'; // Définition du dossier des modules
			$pathModuleAdmin = $pathModules.'admin';
			
			if( !is_dir($pathModuleAdmin) )
			{
				_error("Module 'admin' not found !");
				return false;
			}
			
			$fileInstallSql = $pathModuleAdmin.'/install.sql';
			
			if ( !file_exists($fileInstallSql) )
			{
				_error("File 'install.sql' in module 'admin' not found !");
				return false;
			}
			
			/**
			 * DROP ALL TABLES
			 */
			try {
				$config = Zend_Registry::get('config');
				$tables = $db->fetchAll("SHOW TABLES;");
				
				foreach ($tables as $table) {
					$db->query("DROP TABLE IF EXISTS `".$table['Tables_in_'.$config->database->params->dbname]."`;");
				}
			}
			catch(Exception $e) {
				throw new Exception(_t("Error delete table"));
			}
			
			$querys = $this->returnQuerys($pathModuleAdmin.'/install.sql');
			
			foreach ($querys  as $query)
			{
				if( empty($query) ) continue; // Retire les lignes vides
				try
				{
					$db->query($query); // Execute la requete
				}
				catch (Exception $e)
				{
					die("SQL ERROR: ".$query."<br/><br/>".$e->getMessage()); // Affichage des erreurs sql
				}
			}
			
			$iterator = new DirectoryIterator($pathModules);
			
			foreach ($iterator as $fileInfo) // Liste tout les modules
			{
				if(!$fileInfo->isDot() && (substr($fileInfo->getFilename(), 0, 1) != '.') && ($fileInfo->getFilename() != 'admin')) // Retire les dossiers '.', '..', '.xxxxxx' et 'admin'
				{
					$fileInstallSql = $pathModules.$fileInfo->getFilename().'/install.sql'; // Définition du fichier à chercher
					
					if(file_exists($fileInstallSql)) // install.sql exist
					{
						$querys = $this->returnQuerys($fileInstallSql);
						foreach ($querys as $query)
						{
							if( empty($query) ) continue; // Retire les lignes vides
							try
							{
								$db->query($query); // Execute la requete
							}
							catch (Exception $e)
							{
								die("SQL ERROR: ".$query."<br/><br/>".$e->getMessage()); // Affichage des erreurs sql
							}
						}
					}
					
					$fileInstallPhp = $pathModules.$fileInfo->getFilename().'/install.php'; // Définition du fichier à chercher
					
					if(file_exists($fileInstallPhp)) // install.php exist
					{
						require_once "".$fileInstallPhp.""; // execution du code php
					}
				}
			}
		}
		
		return true;			
	}
	
	public function updateAclAction()
	{
		if($this->getRequest()->isPost())
		{
			$backAcl = CMS_Acl_Back::getInstance();

			if($backAcl->updatePermissionsFromAclForm("admin", $_POST['ACL']))
			_message(_t("Rights updated"));
			else
			_error(_t("Insufficient rights"));
		}

		return $this->_redirect( $this->_helper->route->short('index'));
	}	
	
    public function deletecacheAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();

		if($backAcl->hasPermission("admin", "manage"))
		{
			$config = CMS_Application_Config::getInstance();
			$defaultSkinFront = $config->get("skinfront");
        	
	        $pathSkin = PUBLIC_PATH.'/skins/'.$defaultSkinFront;
	        
	        if( file_exists($pathSkin.'/cache.css') && unlink($pathSkin.'/cache.css') )
	        {
	        	if( file_exists($pathSkin.'/cache.js') && unlink($pathSkin.'/cache.js') )
		        {
		        	_message(_t("Cache deleted successfully"));
		        }
	        }
	        else
	        	_error(_t("Error in delete cache"));
			
			return $this->_redirect( $this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
    public function clearCacheTplAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
    
    	if(!$backAcl->hasPermission("admin", "manage")) {
    		_error(_t("Insufficient rights"));
    		return $this->_redirect($this->_helper->route->full('admin'));
    	}
    		
    	$smarty = Zend_Layout::getMvcInstance()->getView()->getEngine();
    	$smarty->clearCache();
    	
    	_message(_t("Cache deleted successfully"));
    		    			
    	return $this->_redirect( $this->_helper->route->short('index'));

    }
}