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

class Admin_BackController extends Zend_Controller_Action
{

    public function indexAction()
    {

		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
				
		$moduleExistActive = array("galerieImage" => 1, "advertising" => 1, "documents" => 1, "users" => 1, "blocs" => 1, "skins" => 1);
		
		foreach ($moduleExistActive as $key => $module)
		{
			$jobsModuleFolderExists = realpath(dirname(__FILE__).'/../../'.$key.'/');
			if ( !$jobsModuleFolderExists || !file_exists($jobsModuleFolderExists."/Bootstrap.php"))
			{
				$moduleExistActive[$key] = 0;
			}
		}
		$this->view->moduleExistActive = $moduleExistActive;
    }

    public function changemultiAction()
    {
    	$id = $this->_request->getParam('id');
    	
		global $multi_site_prefix;
		
		$multi_site_prefix_prev = $multi_site_prefix;
		$multi_site_prefix 	= $id."_";
		
		$backAcl = CMS_Acl_Back::getInstance();
		if($backAcl->hasPermission("admin", "login"))
		{		
			if($id == MULTI_SITE_ID)
			{
				setcookie("multi_site");
			}
			else 
			{
				setcookie("multi_site[change]"	, 1, 0, "/administration");
				setcookie("multi_site[id]"		, $id, 0, "/administration");
			}
		}
		else 
		{
			$multi_site_prefix = $multi_site_prefix_prev;
		}

		return $this->_redirect($this->_helper->route->full('admin'));
    }

}
	


