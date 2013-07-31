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

class packager_BackController extends CMS_Controller_Action
{
    public function modulesAction()
    {
    	$this->redirectIfNoRights('mod_packager', 'edit');
    	$this->view->modules = $this->getList('modules');
    }
    
	public function blocsAction()
    {
    	$this->redirectIfNoRights('mod_packager', 'edit');
    	$this->view->blocs = $this->getList('blocs');
    }
	
    public function pluginsAction()
    {
    	$this->redirectIfNoRights('mod_packager', 'edit');
    	$this->view->plugins = $this->getList('plugins');
    }
    
    public function  editpluginAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_packager", "edit"))
		{
			
			$zendConfig = CMS_Application_Config::getInstance();
			
			$pluginFile = $this->_request->getParam('id');
			$type = $this->_request->getParam('type');
			
			$configPackageList = json_decode($zendConfig->get("activePlugins"),true);
			
			if (!array_key_exists($pluginFile, $configPackageList)) {
				$configPackageList[$pluginFile] = $type;
			} else {
				unset($configPackageList[$pluginFile]);
			}
			
			$zendConfig->set("activePlugins", json_encode($configPackageList));
			
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
        
    public function forceinstallAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_packager", "edit"))
		{
	    	$packageName = $this->_request->getParam('id');
			$this->executePackageConfig(CMS_PATH . '/tmp/upload', $packageName);
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }

    public function packageAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_packager", "edit"))
		{
	    	$packageName = $this->_request->getParam('id');
	    	$packageType = $this->_request->getParam('type');
	    	
	    	$packageType = strtolower($packageType);
	    	
	    	$packageTypePath = "";
	    	
	    	switch ($packageType)
	    	{
	    		case "plugin":
	    		$packageTypePath = "plugins";
	    		break;
	    		
	    		case "bloc":
	    		$packageTypePath = "blocs";
	    		break;
	    		
	    		case "module":
	    		$packageTypePath = "modules";
	    		break;
	    	}
	    	
	    	
	    	$zipfile = CMS_PATH.'/tmp/'.$packageName.'.zip';
	    	@unlink($zipfile);
	    	
			$filter     = new CMS_Filter_Compress(array(
				'adapter' => 'Zip',
				'options' => array(
				'archive' => $zipfile
				),
			));
	    	
			if (@filetype(APPLICATION_PATH.'/'.$packageTypePath.'/'.$packageName."/".$packageName.".xml") === 'file')
			{
				$config = new Zend_Config_Xml(APPLICATION_PATH.'/'.$packageTypePath.'/'.$packageName."/".$packageName.".xml");
				
				$compressed = $filter->filter(APPLICATION_PATH.'/'.$packageTypePath.'/'.$packageName."/".$packageName.".xml");
				
				$configArray = $config->toArray();
	    		if ($configArray['files'])
		    	{
		             foreach ($configArray['files'] as $fileToPack)
		             {
		             	$compressed = $filter->filter(APPLICATION_PATH.'/'.$packageTypePath.'/'.$packageName."/".$fileToPack);
		             }
		    	}
		    	
		    	if ($configArray['folder'])
		    	{
		    		$compressed = $filter->filter(BASE_PATH.'/'.$configArray['folder']['dest'].'/');
		    	}
		    	
			}
			
			if (($compressed) && (file_exists($zipfile)))
			{
				    header('Content-Description: File Transfer');
				    header('Content-Type: application/octet-stream');
				    header('Content-Disposition: attachment; filename='.basename($zipfile));
				    header('Content-Transfer-Encoding: binary');
				    header('Expires: 0');
				    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				    header('Pragma: public');
				    header('Content-Length: ' . filesize($zipfile));
				    ob_clean();
				    flush();
				    readfile($zipfile);
				    exit;
			}
			else
			{
				_error(_t('packaging had failed'));
				
			}
			
			$this->_redirect($this->_helper->route->short('index'));
		
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    	
    }
    
    public function uninstallAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_packager", "edit"))
		{
	    	$packageName = $this->_request->getParam('id');
	    	$packageType = $this->_request->getParam('type');
    	
	    	$instanceLeft = $this->checkInstalledInstance($packageName,$packageType);
	    	
	    	if ($instanceLeft == false)
	    	{
		    	return $this->_redirect( $this->_helper->route->short('execuninstall',array('id' => $packageName, 'type'=>$packageType)));
	    	}
	    	else
	    	{
	    		$this->view->instances = $instanceLeft; 
	    		$this->view->packageType = $packageType;
	    		$this->view->packageName = $packageName;    	
	    	}
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
    public function execuninstallAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_packager", "edit"))
		{
	    	$packageName = $this->_request->getParam('id');
	    	$packageType = $this->_request->getParam('type');
	    	
	    	$this->removeInstalledInstance($packageName,$packageType);
		    $this->uninstallPackage($packageName,$packageType);
		    
		    return $this->_redirect( $this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
	public function updateaclAction()
	{
		if($this->getRequest()->isPost()) 
		{
			$backAcl = CMS_Acl_Back::getInstance();
			if($backAcl->updatePermissionsFromAclForm("mod_packager", $_POST['ACL']))
				_message(_t("Rights updated"));
			else 
				_error(_t("Insufficient rights"));
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
    public function enableAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_packager", "edit"))
		{
	    	$packageName = $this->_request->getParam('id');
	    	$packageType = $this->_request->getParam('type');
    	
	    	$this->enablePackage($packageName,$packageType);
	    	
	    	return $this->_redirect( $this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
    public function editcmspluginsAction()
    {
    	$pluginFile 	= $this->_request->getParam('id');
    	$action 		= $this->_request->getParam('type');
    	 
    	$zendConfig = CMS_Application_Config::getInstance();
    	
    	$activeCMSPluginsConfig = $zendConfig->get("activeCMSPlugins");
    	$activeCMSPluginsConfig = json_decode($activeCMSPluginsConfig,true);
    	
    	if (!array_key_exists($pluginFile, $activeCMSPluginsConfig[$action])) {
    		$activeCMSPluginsConfig[$action]['CMS_Controller_Plugin_'.$pluginFile] = $pluginFile;
    	} else {
    		unset($activeCMSPluginsConfig[$action]['CMS_Controller_Plugin_'.$pluginFile]);
    	}
    	
//     	 if ($action == 'enable' && !array_key_exists($pluginFile, $activeCMSPluginsConfig)) {
//     	 	$activeCMSPluginsConfig['CMS_Controller_Plugin_'.$pluginFile] = $pluginFile;
//     	 } else if ($action == 'disable') {
//     	 	unset($activeCMSPluginsConfig['CMS_Controller_Plugin_'.$pluginFile]);
//     	 }
    	 
    	 $zendConfig->set('activeCMSPlugins', json_encode($activeCMSPluginsConfig));
    	 
    	 return $this->_redirect( $this->_helper->route->short('index'));
    }
    
   	public function loadUnloadModuleAction() {
   		$packageName = $this->_request->getParam('id');
   		
    	$zendConfig = CMS_Application_Config::getInstance();
    	$activeModule = $zendConfig->get("activeModule");
    	$activeModule = json_decode($activeModule,true);
    	
    	
    	if ($activeModule && (($key = array_search($packageName, $activeModule)) !== false)) {
    		unset($activeModule[$key]);
    	} else {
    		$activeModule[] = $packageName;
    	}
    	
    	$zendConfig->set('activeModule', json_encode($activeModule));
    	return $this->_redirect( $this->_helper->route->short('index'));
    }
    
    private function getList($type)
    {
    	switch ($type) {
    		case 'blocs':
    			$return = $this->getListBlocs();
    			break;
    		case 'modules':
    			$return = $this->getListModules();
    			break;
    		case 'plugins':
    			$return = $this->getListPlugins();
    			break;
    		default:
    			_error('Invalid type packages');
    			break;
    	}
    	
    	return $return;
    }
    
    public function getListBlocs()
    {
		$zendConfig = CMS_Application_Config::getInstance();
		
		/**
		* Liste des blocs
		*/
		$activeBloc = $zendConfig->get('activeBloc');
		$activeBloc = json_decode($activeBloc, true);
		
		$blocList = array();
		
		$pathBlocs = APPLICATION_PATH.'/blocs/';
		
		if(is_dir($pathBlocs)) {
			foreach (new DirectoryIterator($pathBlocs) as $fileInfo) {
				if ($fileInfo->isDot() || $fileInfo->isFile())
					continue;
				
				$pathBlocConfig = $pathBlocs.$fileInfo->getFilename().'/bloc.xml';
				
				if (file_exists($pathBlocConfig) === true) {
					$config = new Zend_Config_Xml($pathBlocConfig);
					
			    	$configArray = $config->toArray();
			    	$configArray['load'] = in_array($configArray['name'], $activeBloc ? $activeBloc : array()) ? 1 : 0;
			    	
			    	$blocList[] = $configArray;
				}
			}
		}
		
		return $blocList;	
    }
    
     public function getListPlugins()
     {
     	$zendConfig = CMS_Application_Config::getInstance();
     	
		/**
		 * Liste des plugins (filtres)
		 */
		$activePluginsConfig = $zendConfig->get("activePlugins");
		$configPackageList = json_decode($activePluginsConfig,true);
		
		$pluginFolders = array(
			'core' 		=> APPLICATION_PATH.'/plugins/',
			'project'	=> PUBLIC_PATH.'/core_addons/plugins/'
		);
		
		foreach ($pluginFolders as $typeFolder => $pluginFolder) {
			if(is_dir($pluginFolder)) {
				foreach (new DirectoryIterator($pluginFolder) as $fileInfo) {
					if ($fileInfo->isDot() || $fileInfo->isFile())
						continue;
					
					$pathPluginConfig = $pluginFolder.$fileInfo->getFilename().'/'.$fileInfo->getFilename().'.xml';
					
					$config = new Zend_Config_Xml($pathPluginConfig);
					
					$configArray = $config->toArray();
					$configArray['file'] 	= $fileInfo->getFilename();
					$configArray['type'] 	= $typeFolder;
					$configArray['active'] 	= ($configPackageList[$fileInfo->getFilename()] == $typeFolder) ? true : false;
					
					$pluginList[] = $configArray;
				}
			}
		}
		
		/**
		 * Liste des plugins
		 */
		$activeCMSPluginsConfig = $zendConfig->get("activeCMSPlugins");
		$activeCMSPluginsConfig = json_decode($activeCMSPluginsConfig,true);
		
		$CMSpluginList = array();
		$CMSpluginPath = BASE_PATH .'/library/CMS/Controller/Plugin/';
		
		if(is_dir($CMSpluginPath)) {
			foreach (new DirectoryIterator($CMSpluginPath) as $fileInfo) {
				if ($fileInfo->isDot() || !$fileInfo->isFile())
					continue;
				
				$fileName = basename($fileInfo, '.php');
				
				$CMSpluginList[] = array(
					'name' 			=> $fileName,
					'activeClassic' => $activeCMSPluginsConfig['classic'] ? array_search($fileName, $activeCMSPluginsConfig['classic']) : null,
					'activeApi' 	=> $activeCMSPluginsConfig['api'] ? array_search($fileName, $activeCMSPluginsConfig['api']) : null,
				);
			}
		}
     }

     public function getListModules()
     {
     	$zendConfig = CMS_Application_Config::getInstance();
     	
		/**
		 * Liste des modules
		 */
		$activeModule = $zendConfig->get("activeModule");
		$activeModule = json_decode($activeModule,true);
		
		$moduleList = array();
		$moduleFolders = array(
			'core' => APPLICATION_PATH.'/modules/',
			'project' => PUBLIC_PATH.'/core_addons/modules/'
		);
		
		foreach ($moduleFolders as $typeFolder => $moduleFolder) {
			if (is_dir($moduleFolder)) {
				foreach (new DirectoryIterator($moduleFolder) as $fileInfo) {
					if ($fileInfo->isDot() || $fileInfo->isFile())
						continue;
					
					$fileXml = $moduleFolder.$fileInfo->getFilename().'/routes.xml';
					
					if (file_exists($fileXml) === true) {
						try {
							$configXml = new Zend_Config_Xml($fileXml, 'config');
							
							$config = $configXml->toArray();
							
							/** Actif ou non **/
							$load = (in_array($fileInfo->getFilename(), $activeModule)) ? true : false;
							
							$config['load'] = $load;
							$config['locationPath'] = $typeFolder;
							
							array_push($moduleList, $config);
						}
						catch (Exception $e) { }
					}
				}
			}
		}
		
		return $moduleList;
    }
    
    public function checkInstalledInstance($packageName,$packageType)
    {
    	switch ($packageType)
    	{
    		case "Bloc":
    		$blocModel = new blocManager_Model_DbTable_blocManager();
			$select = $blocModel->select();
			$select->where("type=?",$packageName);
			$row = $blocModel->fetchAll($select);
			if($row->count() > 0)
			{
				return $row;
			}
			else
			{
				return null;
			}
    		break;
    	}
    }
    
    public function removeInstalledInstance($packageName,$packageType)
    {
    	switch ($packageType)
    	{
    		case "bloc":
			$blocModel = new blocManager_Model_DbTable_blocManager();
			$select = $blocModel->select();
			$select->where("type=?",$packageName);
			$rows = $blocModel->fetchAll($select);
			if($rows->count() > 0) 
			{
		        foreach ($rows as $row) {
		             $blocToRemove = new blocManager_Model_DbTable_blocManager();
		             $blocToRemove->deletebloc($row['id']);
		        }
			}
    		break;
    	}
    }
}
	