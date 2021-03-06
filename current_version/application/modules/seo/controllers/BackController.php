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

class seo_BackController extends CMS_Controller_Action
{
	/*public function migrationMultiLangAction()
	{
		$model = new CMS_Page_Model_Pages();
		$model->migrationMultiLang();
		
		die("finish");
	}*/
	
	public function pagesAction()
	{
		$this->redirectIfNoRights('mod_seo', 'view');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		/* Liste des pages */
		$pages = CMS_Page_Object::getFromDB(array("visible" => 1));
		$this->view->pages = $pages;
		
		/* Liste des types de pages */
		$tmp = CMS_Page_Type::get();
		
		$types = array();
		foreach($tmp as $type){
			$types[$type->type]['type'] = $type;
			$types[$type->type]['title'] = _t("type.".$type->type.".title");
			$types[$type->type]['description'] = _t("type.".$type->type.".description");
		}
		
		uksort($types, 'strcasecmp');
		
		$this->view->types = $types;
		$this->_assignTemplates();
	}
	
	public function typesAction()
	{
		$this->redirectIfNoRights('mod_seo', 'view');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		/* Liste des types de pages */
		$tmp = CMS_Page_Type::get();
		
		$types = array();
		foreach($tmp as $type){
			$types[$type->type]['type'] = $type;
			$types[$type->type]['title'] = _t("type.".$type->type.".title");
			$types[$type->type]['description'] = _t("type.".$type->type.".description");
		}
		
		uksort($types, 'strcasecmp');
		
		$this->view->types = $types;
		$this->_assignTemplates();
	}
	
	public function socialsAction()
	{
		$this->redirectIfNoRights('mod_seo', 'view');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$socialForm  = new Seo_Form_seoSocialForm();
		$this->view->socialForm = $socialForm;
		
    	$config = CMS_Application_Config::getInstance();
		
		if ($this->getRequest()->isPost()) {
			if ($socialForm->isValid($_POST)) {
				$data['googleanalytics'] 	= $socialForm->getValue('googleanalytics');
				$data['googleaccount'] 		= $socialForm->getValue('googleaccount');
				$data['googlepassword'] 	= $socialForm->getValue('googlepassword');
				//$data['googleprofile'] 	    = $socialForm->getValue('googleprofile');
				$data['facebook'] 			= $socialForm->getValue('facebook');
				$data['twitter'] 			= $socialForm->getValue('twitter');
				$data['sitename'] 			= $socialForm->getValue('sitename');
				
				$config->set("social", json_encode($data));
				
				$socialForm->updateButtonLinks($config);
				
				_message(_t("Settings updated"));
				$this->_redirectCurrentPage();
			}
		}
		else {
			$data = json_decode($config->get("social"),true);
			if(is_array($data))
				$socialForm->populate($data);
		}
	}
	
	public function configAction()
	{
		$this->redirectIfNoRights('mod_seo', 'view');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$genericForm = new Seo_Form_seoGenericForm();
		$homeForm = new Seo_Form_HomePage();
		
    	$config = CMS_Application_Config::getInstance();
		
		if ($this->getRequest()->isPost()) {
			if (isset($_POST['generic'])) {
				if ($genericForm->isValid($_POST)) {
					$data['title'] = $genericForm->getValue('title');
					$data['keywords'] = $genericForm->getValue('keywords');
					$data['description'] = $genericForm->getValue('description');
					
					$config->set("seo",json_encode($data));
					
					_message(_t("SEO General settings updated"));
				}
			}
			elseif (isset($_POST['home'])) {
				if($homeForm->isValid($_POST)) {
					$homePage = new CMS_Page_PersistentObject(CMS_Page_Object::HOME_ID, "all");
					$homePage->url_system = null;
					
					$homePage->meta_description = $_POST["home_description"];
					$homePage->meta_keywords 	= $_POST["home_keywords"];
					$homePage->title 			= $_POST["home_title"];
					$homePage->template 		= $_POST["home_template"];
					$homePage->diaporama 		= $_POST["home_diaporama"];
					
					$homePage->save();
					
					_message(_t("Home page settings updated"));
				}
			}
			elseif (isset($_POST['404'])) {
				$config->set("tpl_404", (int)$_POST["template"]);
				_message(_t("Configuration updated"));
			}
			
			$this->_redirectCurrentPage();
		}
		else {
			$data = json_decode($config->get("seo"), true);
			if(is_array($data))
				$genericForm->populate($data);
			
			$homePage = CMS_Page_Object::get(CMS_Page_Object::HOME_ID);
			$homeForm->populate(array(
				"home_description" 	=> $homePage->meta_description,
				"home_keywords" 	=> $homePage->meta_keywords,
				"home_title" 		=> $homePage->title,
				"home_template" 	=> $homePage->template,
				"home_diaporama"	=> $homePage->diaporama
			));
		}
		
		$this->view->tpl_404 = $config->get("tpl_404");		
		$this->view->formGeneralConfig = $genericForm;
		$this->view->homeForm = $homeForm;
		
		$this->_assignTemplates();
	}
	
	public function createPageAction(){
		
		$this->_helper->layout()->setLayout('lightbox');
	
		$form = new Seo_Form_Page();
		$page = new CMS_Page_PersistentObject();
		
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)){
				$page->fromArray($form->getValues());
				$page->rewrite_var = str_replace(array(' ', '/'), '-', $page->rewrite_var );
				$page->save();
				
				echo '<script language="javascript">parent.updateFromIframe('.json_encode($page->toArray()).');</script>';
				$this->closeFancyboxAndRefresh();
			}
		}
		else {
			$form->populate($page->toArray());
		}
		
		$form->setAction($this->_helper->route->short("create-page"));
		$this->view->form = $form;
		$this->view->page = $page;	
	}
	
	public function editPageAction(){
		$id = (int) $this->_request->getParam("id");
		
		if(!$id)
			throw new Exception(_t("ID missing"));
		
		$this->_helper->layout()->setLayout('lightbox');
		
		$form = new Seo_Form_Page();
		$page = new CMS_Page_PersistentObject((int)$id, 'all');
		
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)){
				
				$page->title 						= $form->getValue('title');
				$page->url_rewrite 			= $form->getValue('url_rewrite');
				$page->meta_keywords 		= $form->getValue('meta_keywords');
				$page->meta_description 	= $form->getValue('meta_description');
				$page->template 				= $form->getValue('template');
				$page->diaporama 			= $form->getValue('diaporama');
				$page->rewrite_var 			= str_replace(array(' ', '/'), '-', $form->getValue('rewrite_var'));
				
// 				$page->fromArray($form->getValues());
// 				$page->rewrite_var = str_replace(array(' ', '/'), '-', $page->rewrite_var );
				
				$page->save();
				
				echo '<script language="javascript">parent.updateFromIframe('.json_encode($page->toArray()).');</script>';
				$this->closeFancybox();
			}
		}
		else {
			$form->populate($page->toArray());
		}
		
		// Mise en objet des variables de rewrite
		$rewrite_var_form = CMS_Application_Hook::getInstance()->apply_filters('Seo_BackController_SetRewriteVar_' . $page->type, null);
		
		$form->setAction($this->_helper->route->short("edit-page", array("id" => $id)));
		$this->view->form = $form;
		$this->view->page = $page;
		$this->view->rewrite_var_form = $rewrite_var_form; 
	}
	
	public function deletePageAction(){
		$id = (int) $this->_request->getParam("id");
		
		if(!$id)
			throw new Exception(_t("ID missing"));
		
		$page = new CMS_Page_PersistentObject((int)$id, 'all');
		
		if ($page)
			$page->delete();
		else 
			_error(_t('No page match with the id : '. $id));
		
		return $this->_redirect( $this->_helper->route->short('pages'));
	}
	
	public function permissionsAction()
	{
		$this->redirectIfNoRights('mod_seo', 'manage');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$formAcl = new CMS_Acl_Form_BackAclForm("mod_seo");
		
		if ($this->getRequest()->isPost()) {
			if ($formAcl->isValid($_POST)) {
				$backAcl->updatePermissionsFromAclForm("mod_seo", $_POST['ACL']);
				$this->_redirectCurrentPage();
			}
		}
		
		$this->view->formAcl = $formAcl;
	}
	
	private function _assignTemplates()
	{
		// ** Liste des Templates ** //
		$templates = Blocs_Object_Template::get();
		
		$retour = array("" => "par défaut");
		foreach($templates as $tpl){
			$retour[$tpl->id_template] = " - ".$tpl->title;
		}
		$this->view->templates = $retour;
	}
}