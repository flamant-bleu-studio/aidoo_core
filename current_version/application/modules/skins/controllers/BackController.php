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

class Skins_BackController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_skins", "view"))
		{
			$this->view->backAcl = $backAcl;
				
	    	$config = CMS_Application_Config::getInstance();
	    	$defaultSkinFront = $config->get("skinfront");
	    	$defaultSkinBack = $config->get("skinback");
			
		    $currentSkins = array();
        
	        $dir = opendir(PUBLIC_PATH.'/skins/');
			while (false !== ($file = readdir($dir)))
			if (filetype(PUBLIC_PATH.'/skins/'.$file) === 'dir' && $file != "." && $file != ".." && $file != ".svn")
			{
				// check if package xml exists
				if (@filetype(PUBLIC_PATH.'/skins/'.$file."/skin.xml") === 'file')
				{
			    	$skinData = new Zend_Config_Xml(PUBLIC_PATH.'/skins/'.$file."/skin.xml");
			    	$skinData = $skinData->toArray();
			    	$currentSkins[$file] = $skinData;
			    	$currentSkins[$file]['name'] = strtolower($currentSkins[$file]['name']);
			    	$currentSkins[$file]['path'] = $file;
			    	$currentSkins[$file]['fullpath'] = $file;
				}
			}
			
	        $dir = opendir(ADMIN_PATH);
			while (false !== ($file = readdir($dir)))
			if (filetype(ADMIN_PATH.'/'.$file) === 'dir' && $file != "." && $file != ".." && $file != ".svn")
			{
				// check if package xml exists
				if (@filetype(ADMIN_PATH.'/'.$file."/skin.xml") === 'file')
				{
			    	$skinData = new Zend_Config_Xml(ADMIN_PATH.'/'.$file."/skin.xml");
			    	$skinData = $skinData->toArray();
			    	$currentSkins[$file] = $skinData;
			    	$currentSkins[$file]['name'] = strtolower($currentSkins[$file]['name']);  
			    	$currentSkins[$file]['path'] = $file;
				}
			}
			
		
			if(count($currentSkins) > 0) {
				$this->view->skins = $currentSkins;
				$this->view->defaultSkinFront = $defaultSkinFront;
				$this->view->defaultSkinBack = $defaultSkinBack;
			}
			else
			{
				$this->view->skins = null;
			}
			
			
			$this->view->adminUrl = ADMIN_URL;
			
			
			if($backAcl->hasPermission("mod_skins", "manage"))
			{
				
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_skins");
				$formAcl->setAction($this->_helper->route->short('updateAcl'));
				$formAcl->addSubmit(_t("Submit"));
	
		    	$this->view->formAcl = $formAcl;
			}
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
			if($backAcl->updatePermissionsFromAclForm("mod_skins", $_POST['ACL']))
				_message(_t("Rights updated"));
			else 
				_error(_t("Insufficient rights"));
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
    public function setdefaultfrontAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_skins", "change"))
		{
	    	$skinName = $this->_request->getParam('name');
	    	
	    	$config = CMS_Application_Config::getInstance();
	    	$config->set("skinfront",$skinName);

			_message(_t('front skin changed'));
			
			return $this->_redirect( $this->_helper->route->short('index'));
    	}
    	else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
    public function resetLevels($config) {
        
        if (!is_array($config)) return null;
    	
    	$root = current($config);
        $rootKey = key($config);
        $key = @key($root);
        
        if (!is_int($key)) {
            $config = array();
            $config[$rootKey][0] = $root;
        }
        
        return $config;
    }
    
    
    public function setdefaultbackAction()
    {
    	$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_skins", "change"))
		{
	    	$skinName = $this->_request->getParam('name');
	    	
			//$skinList = new Skins_Model_DbTable_Skins();
			//$selectedSkin = $skinList->getSkin($id);
	    	
	    	$config = CMS_Application_Config::getInstance();
	    	$config->set("skinback",$skinName);
	    	
			_message(_t('back skin changed'));
			
			return $this->_redirect( $this->_helper->route->short('index'));
    	}
    	else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
}
	



