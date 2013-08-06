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

class Documents_BackController extends CMS_Controller_Action
{
	/*public function migrationMultiLangAction() {
		$model = new Documents_Model_DbTable_Documents();
		$model->migrationMultiLang();
		die("finish");
	}*/
	
	public function indexAction()
	{
		$this->redirectIfNoRights('mod_documents', 'view');
		
		$this->view->docs = Documents_Object_Document::get();
	}
	
	public function createAction()
	{
		$this->redirectIfNoRights('mod_documents', 'create');
		
        $typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/documents';
        
		try {
        	$dir = new DirectoryIterator($typesPath);
        }
        catch(Exception $e){}
        
        if ($dir) {
        	$types = array();
	        foreach ($dir as $fileinfo) {
				
	        	if ($fileinfo->isDir() && !$fileinfo->isDot() && file_exists($fileinfo->getPathname().'/type.xml')) {
					$desc = new Zend_Config_Xml($fileinfo->getPathname().'/type.xml');
					
					$types[] = array(
						"type" 			=> $fileinfo->getFileName(),	
						"name" 			=> $desc->name,
						"description"	=> $desc->description
					);
				}
			}
			
			$this->view->types = $types;
        }
	}
	
	public function createdocumentAction()
	{
		$api = new Documents_Lib_Api();
		
		if( $api->hasPermission("create") ) {
			if($this->getRequest()->isPost() || $this->getRequest()->isGet()) {

				$type = $this->_request->getParam('id');
				// Type de contenu choisi
				$api->setType($type);
				
				$seoForm = new Menu_Form_Seo();
				$backAcl = CMS_Acl_Back::getInstance();
				
				if( (isset($_POST["submit"]) || isset($_POST["submitandquit"])) && $seoForm->isValid($_POST) && $api->isValid($_POST) ) {
					
					/*
					 * Enregistrement du document
					 */
					$page = $api->create();
					
					if(!$page)
						throw new Zend_Exception("Error when creating the content");
					
					/*
					 * Enregistrement des informations de la page associée au contenu
					 */
					if(!$page)
						_error(_t("Page object has not been updated because it was not found"));
					else {
						$page->title 				= $seoForm->getValue("seo_title");
						$page->url_rewrite 			= $seoForm->getValue("seo_url_rewrite");
						$page->meta_keywords 		= $seoForm->getValue("seo_meta_keywords");
						$page->meta_description 	= $seoForm->getValue("seo_meta_description");
						$page->template 			= (int)$_POST["select_template_page"] != 0 ? $_POST["select_template_page"] : null;
						$page->diaporama 			= $_POST["select_diaporama_page"] != "null" ? $_POST["select_diaporama_page"] : null;
						$page->save();
					}
					 _message(_t('Page created'));
					
					/*
					 * Redirection
					 */
					if ($_POST['submitandquit'])
						return $this->_redirect($this->_helper->route->short("index"));
					
					return $this->_redirect($this->_helper->route->short("edit", array('id' => $page->content_id)));
				}
				
				$this->view->documentType		= $type;
				$this->view->content 			= $api->getHTML();
				$this->view->seoForm 			= $seoForm;
				$this->view->templatePreview 	= Blocs_Lib_Manager::getRenderTemplate();
				$this->view->backAcl 			= $backAcl;
				
				/*
				 * Manage permissions
				 */
				if($backAcl->hasPermission("mod_documents-default", "manage"))
				{
					$formAcl = new CMS_Acl_Form_BackAclForm("mod_documents-default");
					$this->view->formAcl = $formAcl;
				}
			}
			else 
				return $this->_redirect($this->_helper->route->short('create'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}	  
	}
	
	public function editAction()
	{
		$id = (int) $this->_request->getParam('id');
		
		if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
		
		$document = new Documents_Object_Document($id);
		
		$api = new Documents_Lib_Api(
			array(	
				"content_id" 	=> $id,
				"type" 			=> $document->type
			)
		);
		
		if( $api->hasPermission("edit") ) {
			$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'document', 'content_id' => $id), null, null, "all" );
			
			$seoForm = new Menu_Form_Seo();
			$backAcl = CMS_Acl_Back::getInstance();
			
			if( $this->getRequest()->isPost() ) {
				if( $seoForm->isValid($_POST) && $api->isValid($_POST) ) {
					$api->update();
					
					/*
					 * Enregistrement des informations de la page associée au contenu
					 */
			
					if(!$page)
						_error(_t("Page object has not been updated because it was not found"));
					else {
						$page->title 				= $seoForm->getValue("seo_title");
						$page->url_rewrite 			= $seoForm->getValue("seo_url_rewrite");
						$page->meta_keywords 		= $seoForm->getValue("seo_meta_keywords");
						$page->meta_description 	= $seoForm->getValue("seo_meta_description");
						$page->template 			= (int)$_POST["select_template_page"] != 0 ? $_POST["select_template_page"] : null;
						$page->diaporama 			= $_POST["select_diaporama_page"] != "null" ? $_POST["select_diaporama_page"] : null;
						$page->save();
					}
					
					_message(_t('Page edited'));
					
					/*
					 * Redirection
					 */
					
					if ($_POST['submitandquit'])
						return $this->_redirect($this->_helper->route->short("index"));
					
					return $this->_redirect($this->_helper->route->short("edit", array('id' => $id)));
				}
			}
			else {
				/*
				 * Populate
				 */
				$api->populate();
				
				$seoForm->populate(
					array(
						"seo_title" 			=> $page->title,
						"seo_url_rewrite" 		=> $page->url_rewrite,
						"seo_meta_keywords" 	=> $page->meta_keywords,
						"seo_meta_description" 	=> $page->meta_description
					)
				);

			}
			
			$this->view->id_content 		= $id;
			$this->view->content 			= $api->getHTML();
			$this->view->seoForm 			= $seoForm;
			$this->view->templatePreview 	= Blocs_Lib_Manager::getRenderTemplate($page->template, $page->diaporama);
			$this->view->backAcl 			= $backAcl;
			
			/*
			 * Manage permissions
			 */
			if($api->hasPermission("manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_documents-".$id);
				$this->view->formAcl = $formAcl;
			}
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
		
		if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
		
		$api = new Documents_Lib_Api(array("content_id" => $id));
		
		if( $api->hasPermission("delete") )
		{
			$api->delete();
			
			_message(_t('Page deleted'));
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function permissionsAction()
	{
		$this->redirectIfNoRights('mod_documents', 'manage');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$formAcl = new CMS_Acl_Form_BackAclForm("mod_documents");
		
		if ($this->getRequest()->isPost()) {
			if ($formAcl->isValid($_POST)) {
				$backAcl->updatePermissionsFromAclForm("mod_documents", $_POST['ACL']);
				$this->_redirectCurrentPage();
			}
		}
		
		$this->view->formAcl = $formAcl;
	}
}