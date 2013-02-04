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

class Skins_FaviconController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	global $multi_site_prefix;

	    $backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_skins", "view"))
		{
			$this->view->backAcl = $backAcl;
				
	    	$config = CMS_Application_Config::getInstance();
	    	$skinFront = $config->get("skinfront");
	    	
	    	
	    	$this->view->activeSkin 	= $skinFront;
	    	$this->view->activeMulti	= $multi_site_prefix;     	 
	    	
	    	
	    	$favicon = $baseurl.'/skins/'.$skinFront.'/images/favicon.png';
	    	
			if (file_exists($favicon))
			{
				$this->view->favicon = $favicon;
			}
			
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
	
}
	



