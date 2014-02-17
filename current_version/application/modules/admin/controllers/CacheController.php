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

class Admin_CacheController extends CMS_Controller_Action
{
	// INSERT INTO `1_permissions` (`id`, `name`, `rights`) VALUES (NULL, 'config_cache', '{"manage":["3"]}');
	public function indexAction()
	{
		$this->redirectIfNoRights('config_cache', 'manage');
	}
	
	public function clearAssetsAction()
    {
    	$this->redirectIfNoRights('config_cache', 'manage');
    	
    	if ($this->clearAssets()) {
    		_message(_t("Cache deleted successfully"));
    	}
    	else {
    		_error(_t("Error in delete cache"));
    	}
        
		return $this->_redirect($this->_helper->route->magic('index'));
    }
    
    public function clearTemplatesAction()
    {
    	$this->redirectIfNoRights('config_cache', 'manage');
    	
    	$this->clearTemplates();
    	
    	_message(_t("Cache deleted successfully"));
    	return $this->_redirect($this->_helper->route->magic('index'));
    }
    
	public function clearCmsAction()
    {
    	$this->redirectIfNoRights('config_cache', 'manage');
    	
    	$this->clearCms();
    	
    	_message(_t("Cache deleted successfully"));
    	return $this->_redirect($this->_helper->route->magic('index'));
    }
    
    public function clearAllAction()
    {
    	$this->redirectIfNoRights('config_cache', 'manage');
    	
    	$this->clearAssets();
    	$this->clearTemplates();
    	$this->clearApc();
    	
    	_message(_t("Cache deleted successfully"));
    	return $this->_redirect($this->_helper->route->magic('index'));
    }
    
    private function clearAssets()
    {
    	$config = CMS_Application_Config::getInstance();
		$defaultSkinFront = $config->get("skinfront");
		
        if (file_exists(PUBLIC_PATH.BASE_URL.'/skins/'.$defaultSkinFront.'/cache/min.css') && unlink(PUBLIC_PATH.BASE_URL.'/skins/'.$defaultSkinFront.'/cache/min.css')) {
        	if (file_exists(PUBLIC_PATH.BASE_URL.'/skins/'.$defaultSkinFront.'/cache/min.js') && unlink(PUBLIC_PATH.BASE_URL.'/skins/'.$defaultSkinFront.'/cache/min.js')) {
	        	return true;
	        }
        }
        else
        	return false;
    }
    
    private function clearTemplates()
    {
    	$smarty = Zend_Layout::getMvcInstance()->getView()->getEngine();
    	return $smarty->clearAllCache();
    }
    
    private function clearCms()
    {
    	return CMS_Cache::getInstance()->delete();
    }
}