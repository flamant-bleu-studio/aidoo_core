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

class Diaporama_BackController extends CMS_Controller_Action
{
	protected $namePermission = 'mod_diaporama';
	
	public function indexAction()
	{
		$this->redirectIfNoRights($this->namePermission, 'view');
		
		$diaporamas = Diaporama_Object_Diaporama::get();
		$this->view->diaporamas = $diaporamas;
	}
	
	public function createAction()
	{
		$this->redirectIfNoRights($this->namePermission, 'create');
		
		$form = new Diaporama_Form_Diaporama();
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$diaporama = new Diaporama_Object_Diaporama();
				
				$datas['title'] = $form->getValue('title');
				$datas['size']	= $form->getValue('size');
				
				$diaporama->fromArray($datas);
				
				$id = $diaporama->save();
				
				/** Permissions **/
				if($_POST['ACL'])
	            	$backAcl->addPermissionsFromAclForm($this->namePermission."-".$id, $_POST['ACL']);
				else 
					$backAcl->addPermissionsFromDefaultAcl($this->namePermission."-".$id, $this->namePermission."-default");
				
				return $this->_redirect($this->_helper->route->short('add-image', array('id' => $id)));
			}
		}
		
		if ($backAcl->hasPermission($this->namePermission.'-default', 'manage')) {
			$formAcl = new CMS_Acl_Form_BackAclForm($this->namePermission.'-default');
			$form->addSubForm($formAcl, 'permissions');
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->form = $form;
	}
	
	public function addImageAction()
	{
		$this->redirectIfNoRights($this->namePermission, 'create');
		
		$id = (int) $this->_request->getParam('id');
		$diaporama = new Diaporama_Object_Diaporama($id);
		
		$form = new Diaporama_Form_AddImage();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$datas = $diaporama->toArray();
				
				$images = $form->getValue('images');
				foreach ($images as $i) {
					$image = $diaporama->existByName($i);
					
					if ($image)
						$datas['nodes'][] = $image->toArray();
					else 
						$datas['nodes'][] = array('image' => $i);
				}
				
				$diaporama->fromArray($datas);
				$diaporama->save();
				
				$this->_redirect($this->_helper->route->short('config-image', array('id' => $id)));
			}
		}
		else {
			$datas = array();
			$images = $diaporama->nodes;
			if (!empty($images)) {
				foreach ($images as $i) {
					$datas['images'][] = $i->image;
				}
			}
			$form->populate($datas);
		}
		
		$this->view->diaporama 	= $diaporama;
		$this->view->images 	= $diaporama->nodes;
		$this->view->form 		= $form;
	}
	
	public function configImageAction()
	{
		$this->redirectIfNoRights($this->namePermission, 'create');
		
		$id = (int) $this->_request->getParam('id');
		$diaporama = new Diaporama_Object_Diaporama($id);
		
		$this->view->images = $diaporama->nodes;
		$this->view->diaporama = $diaporama;
	}
	
	public function editImageAction()
	{
		$this->redirectIfNoRights($this->namePermission, 'edit');
		
		$this->setLayoutIframe();
		
		$id = (int) $this->_request->getParam('id');
		$image = new Diaporama_Object_Image($id);
		
		$form = new Diaporama_Form_Image();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$image->link_type 			= $form->getValue('link_type');
				$image->link_internal 		= $form->getValue('link_internal');
				$image->link_external 		= $form->getValue('link_external');
				$image->link_target_blank 	= $form->getValue('link_target_blank');
				
				$image->text 				= $form->getValue('text');
				$image->background_color 	= $form->getValue('background_color');
				
				$image->save();
				
				$this->closeFancyboxAndRefresh();
			}
		}
		else {
			$form->populate($image->toArray());
		}
		
		$this->view->image 	= $image;
		$this->view->form 	= $form;
	}
	
	public function editAction()
	{
		$id = (int) $this->_request->getParam('id');
		
		$this->redirectIfNoRights($this->namePermission.'-'.$id, 'edit');
		
		$diaporama = new Diaporama_Object_Diaporama($id);
		
		$form = new Diaporama_Form_Diaporama();
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				
				$datas['title'] = $form->getValue('title');
				$datas['size']	= $form->getValue('size');
				
				$images = $diaporama->nodes;
				
				if (!empty($images)) {
					foreach ($images as $i) {
						$datas['nodes'][] = $i->toArray();
					}
				}
				
				$diaporama->fromArray($datas);
				
				$id = $diaporama->save();
				
				/** Permissions **/
				$backAcl->updatePermissionsFromAclForm($this->namePermission.'-'.$id, $_POST['ACL']);
				
				return $this->_redirect($this->_helper->route->short('add-image', array('id' => $id)));
			}
		}
		else {
			$form->populate($diaporama->toArray());
		}
		
		if ($backAcl->hasPermission($this->namePermission.'-'.$id, 'manage')) {
			$formAcl = new CMS_Acl_Form_BackAclForm($this->namePermission.'-'.$id);
			$form->addSubForm($formAcl, 'permissions');
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->diaporama 	= $diaporama;
		$this->view->images 	= $diaporama->nodes;
		$this->view->form 		= $form;
	}
	
	public function deleteAction()
	{
		$id = (int)$this->_request->getParam('id');
		
		$this->redirectIfNoRights($this->namePermission.'-'.$id, 'delete');
		
		$diaporama = new Diaporama_Object_Diaporama($id);
		$diaporama->delete();
		
		CMS_Acl_Back::getInstance()->deletePermissions($this->namePermission.'-'.$id);
		
		_message(_t('Diaporama deleted'));
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function permissionsAction()
	{
		$this->redirectIfNoRights($this->namePermission, 'manage');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$formAcl = new CMS_Acl_Form_BackAclForm($this->namePermission);
		
		if ($this->getRequest()->isPost()) {
			if ($formAcl->isValid($_POST)) {
				$backAcl->updatePermissionsFromAclForm($this->namePermission, $_POST['ACL']);
				$this->_redirectCurrentPage();
			}
		}
		
		$this->view->formAcl = $formAcl;
	}
	
	public function migrateOldAction()
	{
		$model = new Diaporama_Model_DbTable_Diaporama();
		$model->migrate();
		
		die('finish');
	}
}