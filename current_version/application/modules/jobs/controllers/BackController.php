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

class Jobs_BackController extends Zend_Controller_Action
{
	private $storageFolder="/tmp/upload";

	public function indexAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		$config = CMS_Application_Config::getInstance();
		
		/** Enregister adresse mail pour contact **/
		$formContact = new Jobs_Form_Contact();
		$formContact->setAction($this->_helper->route->short('index'));
		
		$values = json_decode($config->get("contactEmail"), true);
		
		if($this->getRequest()->isPost())
		{
			if( $formContact->isValid($_POST) )
			{	
				if($backAcl->hasPermission("mod_jobs", "edit"))
				{	
					$values["mod_jobs_contact"] = $_POST["mod_jobs_contact"];
					$config->set("contactEmail", json_encode($values));
					_message(_t("Email address have been changed"));
				}
				else
				{
					_error(_t("Insufficient rights"));
					return $this->_redirect($this->_helper->route->full('admin'));
				}
			}
			else
				_error(_t('invalid form'));
		}
		
		if( $values )
			$formContact->populate($values);
		
		$this->view->formContact = $formContact;
			
		$formConfig = json_decode($config->get("contactEmail"));
		$destinataire = $formConfig->mod_jobs_contact;
		if (!$destinataire)
			_error(_t("Default contact email is empty, unsolicited applications will not be received! "));
		
		if($backAcl->hasPermission("mod_jobs", "view"))
		{			
			$this->view->jobs = Jobs_Object_Jobs::get();
			
			if($backAcl->hasPermission("mod_jobs", "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_jobs");
				$formAcl->setAction($this->_helper->route->short('updateacl'));
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
			if($backAcl->updatePermissionsFromAclForm("mod_jobs", $_POST['ACL']))
				_message(_t("Rights updated"));
			else 
				_error(_t("Insufficient rights"));
		}
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	public function createAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if($backAcl->hasPermission("mod_jobs", "create"))
		{
		
			$form = new Jobs_Form_Jobs();
			
			if ($this->getRequest()->isPost()) {
				if ($form->isValid($_POST)) {

					// Job
					$job = new Jobs_Object_Jobs();
					$job->fromArray($form->getValues());
					$id = $job->save();
					
					$backAcl->addPermissionsFromDefaultAcl("mod_jobs-".$id, "mod_jobs-default");
					
					$page = new CMS_Page_PersistentObject();
					
					$page->title 		= $job->job_title;
					$page->type 		= "jobs-view";
					$page->content_id 	= $id;
					$page->url_system 	= $this->_helper->route->full('jobs', array("action"=>"view", "id"=>$id));
					$page->enable 		= 1;
					$page->save();
					
					$page = new CMS_Page_PersistentObject();
						
					$page->title 		= 'Postuler : '.$job->job_title;
					$page->type 		= "jobs-apply";
					$page->content_id 	= $id;
					$page->url_system 	= $this->_helper->route->full('jobs', array("action"=>"apply", "id"=>$id));
					$page->enable 		= 1;
					$page->save();
					
					if($_POST['save'])
						return $this->_redirect($this->_helper->route->short('edit', array('id' => $id)));
					
					_message(_t('new job created'));
					return $this->_redirect($this->_helper->route->short('index'));
					
				}
				else {
					_error(_t('invalid form'));
				}
			}
			
			$form->setAction($this->_helper->route->short('create'));
			
			// Affichage du gestionnaire de permission si droit de manage
			if ($backAcl->hasPermission("mod_jobs-default", "manage")) {
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_jobs-default");
				$form->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			} 
			
			$this->view->form = $form;
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function editAction()
	{
		$id = intval($this->_request->getParam('id'));

		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if ($backAcl->hasPermission("mod_jobs-".$id, "edit")) {
			$form = new Jobs_Form_Jobs();
			
			if ($this->getRequest()->isPost()) {
				if ($form->isValid($_POST)) {
					
					$job = new Jobs_Object_Jobs($id);
					$job->fromArray($form->getValues());
					$job->save();
					
		            $backAcl->updatePermissionsFromAclForm("mod_jobs-".$id, $_POST['ACL']);
					
					$hooks = CMS_Application_Hook::getInstance();
					
					$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'jobs-view', 'content_id' => $id), null, null, "all" );
					$page->title = $job->job_title;
					$page->save();
					
					$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'jobs-apply', 'content_id' => $id), null, null, "all" );
					$page->title = 'Postuler : '.$job->job_title;
					$page->save();
					
					_message(_t('Job updated'));

					if($_POST['save'])
						return $this->_redirect($this->_helper->route->short('edit', array('id' => $id)));
					
					return $this->_redirect($this->_helper->route->short('index'));
				}
				else{
					_error(_t('invalid form'));
				}
			}
			
			$form->setAction($this->_helper->route->short('edit', array('id'=>$id)));
			
			$job = new Jobs_Object_Jobs($id);

			$form->populate((array)$job);
			
			// Affichage du gestionnaire de permission si droit de manage
			if($backAcl->hasPermission("mod_jobs-".$id, "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_jobs-".$id);
				$form->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			}
			$this->view->form = $form;

		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function deleteAction()
	{
		$id = (int) $this->_request->getParam('id');
		
		$job = new Jobs_Object_Jobs($id);
		$job->delete();
		
		$backAcl = CMS_Acl_Back::getInstance();
		$backAcl->deletePermissions("mod_jobs-".$id);
		
		$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'jobs-view', 'content_id' => $id), null, null, "all" );
		if(!$page)
			_error(_t("Page object has not been deleted because it was not found"));
		else
			$page->delete();
		
		$page2 = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'jobs-apply', 'content_id' => $id), null, null, "all" );
		if(!$page2)
			_error(_t("Page object has not been deleted because it was not found"));
		else
			$page2->delete();
		
		
		_message(_t('Job deleted'));
		
		return $this->_redirect($this->_helper->route->short('index'));
	}   
}

