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

class Faq_BackController extends CMS_Controller_Action
{
	
	public function indexAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if(!$backAcl->hasPermission("mod_faq", "view")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		// Récupération des faqs
		$faqs = Faq_Object_Faq::get();

		if($backAcl->hasPermission("mod_faq", "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_faq");
			$formAcl->setAction(BASE_URL.$this->_helper->route->short('updateAcl'));
			$formAcl->addSubmit(_t("Submit"));
		
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->c = $faqs;
	}
	
	public function createAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission("mod_faq", "view")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		/** FORM **/
		$form = new Faq_Form_Faq();
		
		/** POST **/
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {

				$faq = new Faq_Object_Faq();
				
				$faq->title 	= $form->getValue('title');
				$faq->access 	= $form->getValue('access');
				
				$id = $faq->save();
				
				/** DATAS - Permissions **/
				if($_POST['ACL'])
					$backAcl->addPermissionsFromAclForm("mod_faq-".$id, $_POST['ACL']);
				else
					$backAcl->addPermissionsFromDefaultAcl("mod_faq-".$id, "mod_faq-default");
				
				$page = new CMS_Page_PersistentObject();
				
				$page->title 		= $faq->title;
				$page->type 		= "faq";
				$page->content_id 	= $id;
				$page->url_system 	= $this->_helper->route->full('faq', array("id" => $id));
				$page->save();
								
				_message(_t('Faq created'));

				return $this->_redirect($this->_helper->route->short("edit", array('id' => $id)));
			}
		}
		
		$form->setAction($this->_helper->route->short('create') );
		
		// Affichage du gestionnaire de permission si droit de manage
		if($backAcl->hasPermission("mod_faq-default", "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_faq-default");
			$form->addSubForm($formAcl, "permissions");
			$this->view->formAcl = $formAcl;
		}
				
		/** View **/
		$this->view->form = $form;
		$this->view->backAcl = $backAcl;

	}
	public function editAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		
		$id = intval($this->_request->getParam('id'));
		
		if(!$backAcl->hasPermission("mod_faq-".$id, "edit")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		// Récupération des faqs
		$faq = new Faq_Object_Faq($id);
		
		$this->view->faq = $faq;
		
		if($backAcl->hasPermission("mod_faq-".$id, "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_faq-".$id);
			$formAcl->setAction(BASE_URL.$this->_helper->route->short('updateaclfaq', array('id' => $id)));
			$formAcl->addSubmit(_t("Submit"));
		
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->backAcl = $backAcl;
	}
	public function deleteAction ()
	{
		$id = (int) $this->_request->getParam('id');
		
		if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		// Check permissions
		if(!$backAcl->hasPermission('mod_faq-'.$id, 'view')) {
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		// Delete Article
		Faq_Object_Faq::deleteFromPrimaryKey($id);
		
		$backAcl->deletePermissions("mod_faq-".$id);
		
		$page = CMS_Page_PersistentObject::getOneFromDB(array('type' => 'faq', 'content_id' => $id), null, null, "all");
		
		if(!$page)
			_error(_t("Page object has not been deleted because it was not found"));
		else
			$page->delete();
					
		_message(_t('Faq deleted'));
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function createQuestionAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$faqId = intval($this->_request->getParam('id'));
		
		if(!$backAcl->hasPermission("mod_faq-".$faqId, "view")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		/** FORM **/
		$form = new Faq_Form_Question();
	
		/** POST **/
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {
	
				$question = new Faq_Object_Question();
	
				$question->question 	= $form->getValue('question');
				$question->answer 		= $form->getValue('answer');
				$question->parent_id 	= $faqId;
	
				$question->save();
	
				_message(_t('Faq created'));
	
				if ($_POST['submitandquit'])
					return $this->_redirect($this->_helper->route->short("index"));
				
				return $this->_redirect($this->_helper->route->short("edit", array('id' => $faqId)));
			}
		}
	
		$form->setAction($this->_helper->route->short('create-question', array('id' => $faqId)) );
	
		/** View **/
		$this->view->form = $form;
	
	}
	public function editQuestionAction()
	{
		$this->_helper->layout()->setLayout('lightbox');
		$id = intval($this->_request->getParam('id'));
	
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission('mod_faq', 'view')) {
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	
	
		/** EDIT **/
		$question = new Faq_Object_Question($id, 'all');
	
		/** FORM **/
		$form = new Faq_Form_Question();
	
		/** POST **/
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {
	
				$datas = array(
					"question"	=> $form->getValue("question"),
					"answer"	=> $form->getValue("answer")
				);
				
				// UPDATE
				$question->fromArray($datas);
				$id = $question->save();
	
				//$this->closeIframe();
	
				if ($_POST['submitandquit'])
					return $this->closeandredirect($this->_helper->route->short("edit", array('id' => $question->parent_id)));
	
				return $this->_redirect($this->_helper->route->current());
			}
		}
		else {
			$form->populate($question->toArray());
		}
	
		$form->setAction($this->_helper->route->short('edit-question', array('id' => $id)) );
	
		/** VIEW **/
		$this->view->form = $form;
	}
	public function deleteQuestionAction ()
	{
		$id = (int) $this->_request->getParam('id');
	
		if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
	
		$backAcl = CMS_Acl_Back::getInstance();
	
		// Check permissions
		if(!$backAcl->hasPermission('mod_faq', 'view')) {
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	
		$question = new Faq_Object_Question($id);
		
		$faqId = $question->parent_id;
		$question->delete();
			
		_message(_t('Faq deleted'));
		return $this->_redirect($this->_helper->route->short('edit', array('id' => $faqId)));
	}
	
	public function closeandredirect($url)
	{
		// reloading or updating the parent windows will force the popup to close  
		echo '
		<html><script language="javascript">
			parent.location.href="'.BASE_URL.$url.'";
		</script></html>';
	}
	public function closeIframe() {
		echo '<script language="javascript">parent.$.fancybox.close();</script>';
		
	}
	
	public function updateaclAction()
	{
		if($this->getRequest()->isPost())
		{
			$backAcl = CMS_Acl_Back::getInstance();
			if($backAcl->updatePermissionsFromAclForm("mod_faq", $_POST['ACL']))
			_message(_t("Rights updated"));
			else
			_error(_t("Insufficient rights"));
		}
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	public function updateaclfaqAction()
	{
		if($this->getRequest()->isPost())
		{
			$id = (int) $this->_request->getParam('id');
			
			if(!$id)
				throw new Zend_Exception(_t('Id is missing'));
			
			$backAcl = CMS_Acl_Back::getInstance();
			if($backAcl->updatePermissionsFromAclForm("mod_faq-".$id, $_POST['ACL']))
				_message(_t("Rights updated"));
			else
				_error(_t("Insufficient rights"));
		}
		return $this->_redirect($this->_helper->route->short('edit', array('id' => $id)));
	}
}

