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

class sitemap_BackController extends Zend_Controller_Action
{
	
	public function indexAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_sitemap", "view"))
		{
			$this->view->backAcl = $backAcl;
			
			$form = new sitemap_Form_sitemapForm();
			$this->view->sitemapForm = $form;
		    
	    	$config = CMS_Application_Config::getInstance();
	
			if($this->getRequest()->isPost()) {
				if($form->isValid($_POST)) {
					
					$data = $form->encodeParams();
					$config->set("sitemap",$data);
					_message(_t("Sitemap updated"));
				}
			}
			
			$form->setAction($this->_helper->route->short('index'));
			
			$sitemapConfig = $config->get("sitemap");
			if ($sitemapConfig)
			{
				$formData = json_decode($config->get("sitemap"),true);
				$form->populate($formData);
			}
			
			if($backAcl->hasPermission("mod_sitemap", "manage"))
			{
				
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_sitemap");
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
			if($backAcl->updatePermissionsFromAclForm("mod_sitemap", $_POST['ACL']))
				_message(_t("Rights updated"));
			else 
				_error(_t("Insufficient rights"));
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}

}

