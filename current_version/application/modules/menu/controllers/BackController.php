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

class Menu_BackController extends CMS_Controller_Action {

	/*public function migrationMultiLangAction()
	{
		$model = new Menu_Model_DbTable_Menu();
		$model->migrationMultiLang();
		       
		die("finish");
	}*/
	
	public function indexAction()
	{
		// Unset session liaison module externe
		$session = new Zend_Session_Namespace('createFromMenu');
		$session->unsetAll();
		
		$this->redirectIfNoRights('mod_menu', 'view');
		
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		$id_lst = Menu_Object_Menu::getAllMenuID();
		
		$menus = array();
		foreach ($id_lst as $id) {
			$object = new Menu_Object_Menu((int)$id);
			$object->generate();
			
			$menus[] = $object;
		}
		
		$this->view->menus = $menus;
		$this->view->type_folder = Menu_Object_Item::$TYPE_FOLDER;
	}
	
	public function permissionsAction()
	{
		$this->redirectIfNoRights('mod_menu', 'manage');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$formAcl = new CMS_Acl_Form_BackAclForm('mod_menu');
		$formAcl->addSubmit(_t('Submit'));
		
		if ($this->getRequest()->isPost() && $formAcl->isValid($_POST)) {
			$backAcl->updatePermissionsFromAclForm('mod_menu', $_POST['ACL']);
			$this->_redirectCurrentPage();
		}
		
    	$this->view->formAcl = $formAcl;
	}
	
	public function addMenuAction()
	{
		$this->setLayoutIframe();
		
		$this->redirectIfNoRights('mod_menu', 'menu');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$form = new Menu_Form_Menu();
		
		if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
			$menu = new Menu_Object_Menu();
			
			$menu->label = $_POST["label"];
			$menu->subtitle = $_POST["subtitle"];
			$menu->type = Menu_Object_Item::$TYPE_SYSTEM;
			
			$id = $menu->save();
			
			if($_POST['ACL'])
	            $backAcl->addPermissionsFromAclForm("mod_menu-menu-".$id, $_POST['ACL']);
			else 
				$backAcl->addPermissionsFromDefaultAcl("mod_menu-menu-".$id, "mod_menu-menu-default");
			
			_message(_t('Menu created'));
			
			$this->closeFancyboxAndRefresh();
		}
		
		/** Permissions **/
		if ($backAcl->hasPermission("mod_menu-menu-default", "manage")) {
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_menu-menu-default");
			$form->addSubForm($formAcl, "permissions");	
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->backAcl = $backAcl;
		$this->view->form = $form;
	}
	
	public function addItemAction()
	{
		$this->setLayoutIframe();
		
		$menu_id 	= (int)$this->_request->getParam('id');
		$parent_id 	= (int)$this->_request->getParam('elem') ? (int)$this->_request->getParam('elem') : $menu_id;
		
		$this->redirectIfNoRights('mod_menu-menu-'.$menu_id, 'insert');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$form = new Menu_Form_Item();

		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {
				
				$model = new Menu_Model_DbTable_Menu();
				
				$linkType = $form->getValue("linkType");
				
				if($linkType == 1) // Nouvel élement souhaité 
				{
					$datas = array(						
						'menu_id'		=> $menu_id,
						'parent_id' 	=> $parent_id,
						'label'			=> $form->getValue('label'),
						'access'		=> $form->getValue('access'),
						'subtitle'		=> $form->getValue('subtitle'),
						'tblank'		=> $form->getValue('tblank'),
						'cssClass'		=> $form->getValue('cssClass'),
						'image'			=> $form->getValue('image'),
						'hidetitle'		=> $form->getValue('hidetitle'),
						'loadAjax'		=> $form->getValue('loadAjax'),
						'chooseType'	=> $form->getValue('chooseType')
					);
					
					$session = new Zend_Session_Namespace("createContentFromMenu");
					$session->datas = $datas;
					$session->acl	= $_POST['ACL'];
					
					$url = $this->_helper->route->short('create-content');
				}
				elseif($linkType == 2 || $linkType == "3") 
				{
					$item = new Menu_Object_Item();
					
					$item->menu_id		= $menu_id;
					$item->parent_id 	= $parent_id;
					$item->label		= $form->getValue("label");
					$item->access		= $form->getValue("access");
					$item->subtitle		= $form->getValue("subtitle");
					$item->tblank		= $form->getValue("tblank");
					$item->cssClass		= $form->getValue("cssClass");
					$item->image		= $form->getValue("image");
					$item->hidetitle	= $form->getValue("hidetitle");
					$item->loadAjax	= $form->getValue('loadAjax');
					
					if($linkType == 2){
						$item->link = $form->getValue("existingpage");
						$item->type = Menu_Object_Item::$TYPE_PAGE;
						}
						else {
							$item->type = Menu_Object_Item::$TYPE_EXTERNAL_LINK;
							$item->link = $form->getValue("externalpage");
						}
						$item_id = $item->save();
						
						$url = $this->_helper->route->short('index');
					}
					
					if ($item_id != 0)
					{	
						/** PERSMISSIONS **/		
						if($_POST['ACL'])
			            	$backAcl->addPermissionsFromAclForm("mod_menu-item-".$item_id, $_POST['ACL']);
						else 
							$backAcl->addPermissionsFromDefaultAcl("mod_menu-item-".$item_id, "mod_menu-item-default");
						
						_message(_t('menu item created'));
					}

					$this->closeandredirect($url);
				}
			}
		
			$form->setAction( $this->_helper->route->short('additem', array('id' => $menu_id, 'elem' => $parent_id)) );
			
			if($backAcl->hasPermission("mod_menu-item-default", "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_menu-item-default");
				$form->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			}
			
			/** VIEW **/
			$this->view->backAcl = $backAcl;
			$this->view->form = $form;
	}
	
	public function addFolderAction()
	{
		$this->setLayoutIframe();
		
		$menu_id 	= (int)$this->_request->getParam('id');
		$parent_id 	= (int)$this->_request->getParam('elem');
		
		$this->redirectIfNoRights('mod_menu-menu-'.$menu_id, 'insert');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		/** FROM **/
		$form = new Menu_Form_Folder();
		
		/** POST **/
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {

				$item = new Menu_Object_Item();
						
				$item->menu_id		= $menu_id;
				$item->parent_id 	= $parent_id;
				$item->label		= $form->getValue("label");
				$item->access		= $form->getValue("access");
				$item->image		= $form->getValue("image");
				$item->hidetitle	= $form->getValue("hidetitle");
				
				$linkType = $form->getValue("linkType");
				
				switch ($linkType) {
					case 1:
						$item->type = Menu_Object_Item::$TYPE_FOLDER_LINK_CHILDREN;
						break;
					case 2:
						$item->type = Menu_Object_Item::$TYPE_FOLDER_PAGE;
						$item->link = $datas["existingpage"];
						break;
					case 3:
						$item->type = Menu_Object_Item::$TYPE_EXTERNAL_LINK;
						$item->link = $datas["externalpage"];
						break;
					case 4:
						$item->type = Menu_Object_Item::$TYPE_FOLDER_NO_LINK;
						break;
				}
				
				$item_id = $item->save();
				
				/** PERSMISSIONS **/
				if ($item_id != 0) {					
					if($_POST['ACL'])
		            	$backAcl->addPermissionsFromAclForm("mod_menu-item-".$item_id, $_POST['ACL']);
					else 
						$backAcl->addPermissionsFromDefaultAcl("mod_menu-item-".$item_id, "mod_menu-item-default");
				}
				_message(_t('menu item created'));
				
				$this->closeFancyboxAndRefresh();
			}
		}
		
		/** PERSMISSIONS **/
		$form->setAction($this->_helper->route->short('add-folder', array('id' => $menu_id, 'elem' => $parent_id)));
		if ($backAcl->hasPermission("mod_menu-item-default", "manage")) {
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_menu-item-default");
			$form->addSubForm($formAcl, "permissions");
			$this->view->formAcl = $formAcl;
		}
		
		/** VIEW **/
		$this->view->backAcl = $backAcl;
		$this->view->form = $form;
	}
	
	public function editMenuAction()
	{
		$this->setLayoutIframe();
		
		$id = (int)$this->_request->getParam('id');
		
		$this->redirectIfNoRights('mod_menu-menu-'.$id, 'edit');
		
		$backAcl = CMS_Acl_Back::getInstance();
		$item = new Menu_Object_Item($id, 'all');
		
		/** FROM **/
		$form = new Menu_Form_Menu();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				/** DATAS **/
				$datas = array();
				$datas["label"] 	= $_POST["label"];
				$datas["subtitle"] 	= $_POST["subtitle"];
				
				/** UPDATE **/
				$item->fromArray($datas);
				$item->save();
				
				/** PERMISSIONS **/
				$backAcl->updatePermissionsFromAclForm("mod_menu-menu-".$id, $_POST['ACL']);
				
				$this->closeFancyboxAndRefresh();
			}
		}
		else {
			$form->populate($item->toArray());
		}
		
		$form->setAction($this->_helper->route->short('edit-menu', array('id' => $id)));
		
		// Affichage du gestionnaire de permission si droit de manage
		if ($backAcl->hasPermission("mod_menu-menu-".$id, "manage")) {
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_menu-menu-".$id);
			$form->addSubForm($formAcl, "permissions");	
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->form = $form;
		$this->view->backAcl = $backAcl;
	}
	
	public function editItemAction()
	{
		$this->setLayoutIframe();
		
		$id = intval($this->_request->getParam('id'));
		
		$this->redirectIfNoRights('mod_menu-item-'.$id, 'edit');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$item = new Menu_Object_Item($id, "all");
		
		$form = new Menu_Form_Item();
		
		if($this->getRequest()->isPost()) {
			if($form->isValid($_POST)) {
				
				$datas = array(
					"label"			=> $form->getValue("label"),
					"access"		=> $form->getValue("access"),
					"subtitle"		=> $form->getValue("subtitle"),
					"tblank"		=> $form->getValue("tblank"),
					"cssClass"		=> $form->getValue("cssClass"),
					"image"			=> $form->getValue("image"),
					"hidetitle"		=> $form->getValue("hidetitle"),
					'loadAjax'		=> $form->getValue('loadAjax')
				);
				
				$linkType = $form->getValue("linkType");
				
				if($linkType == 1) // Nouvel élement souhaité 
				{
					$datas['chooseType'] = $form->getValue("chooseType");
					
					$session = new Zend_Session_Namespace("createContentFromMenu");
					$session->datas = $datas;
					$session->datas["item_id"] = $id;
					$session->acl	= $_POST['ACL'];
					
					$url = $this->_helper->route->short('create-content');
				}
				elseif($linkType == 2) // Page existante choisie
				{
					$datas['link'] = $form->getValue("existingpage");
					$datas['type'] = Menu_Object_Item::$TYPE_PAGE;
					
					$item->fromArray($datas);
					$item->save();
					
					$url = $this->_helper->route->short('index');
				}
				elseif($linkType == "3") // Lien externe choisi
				{
					$datas['link'] = $form->getValue("externalpage");
					$datas['type'] = Menu_Object_Item::$TYPE_EXTERNAL_LINK;
					
					$item->fromArray($datas);
					$item->save();
					
					$url = $this->_helper->route->short('index');
				}
				
				/** PERMISSIONS **/
				$backAcl->updatePermissionsFromAclForm("mod_menu-item-".$id, $_POST['ACL']);
				
				$this->closeandredirect($url);
			}
		}
		else {
			$form->populate($item->toArray());
		}
		
		
		/** SET VALUE TYPE PAGE **/
		if( $item->type == Menu_Object_Item::$TYPE_EXTERNAL_LINK )
		{
			$form->getElement("externalpage")->setValue($item->link);
			$form->getElement("linkType")->setValue(3);
		}
		else if ( $item->type == Menu_Object_Item::$TYPE_PAGE )
		{
			$form->getElement("existingpage")->setValue($item->link);
			$form->getElement("linkType")->setValue(2);
		}
		
		/** PERMISSIONS **/
		$form->setAction($this->_helper->route->short('edit-item', array('id' => $id)) );
		// Affichage du gestionnaire de permission si droit de manage
		if($backAcl->hasPermission("mod_menu-menu-".$item->menu_id, "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_menu-item-".$id);
			$form->addSubForm($formAcl, "permissions");	
			$this->view->formAcl = $formAcl;
		}
		
		/** VIEW **/
		$this->view->backAcl = $backAcl;
		$this->view->form = $form;
	}
	
	public function editFolderAction()
	{
		$this->setLayoutIframe();
		
		$id = intval($this->_request->getParam('id'));
		
		$this->redirectIfNoRights('mod_menu-item-'.$id, 'edit');
		
		$backAcl = CMS_Acl_Back::getInstance();
		/** EDIT **/
		$item = new Menu_Object_Item($id, "all");
		
		/** FORM **/
		$form = new Menu_Form_Folder();
		
		/** POST **/
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				/** DATAS **/
				$datas = array();
				$datas["label"] 	= $_POST["label"];
				$datas["access"] 	= $_POST["access"];
				$datas["hidetitle"] = $_POST["hidetitle"];
				$datas["image"]		= $form->getValue("image");
				
				$linkType = $form->getValue("linkType");
				
				if($linkType == 1)
				{
					$datas["type"] = Menu_Object_Item::$TYPE_FOLDER_LINK_CHILDREN;
				}
				else if($linkType == 2)
				{
					$datas['type'] = Menu_Object_Item::$TYPE_FOLDER_PAGE;
					$datas['link'] = $form->getValue("existingpage");
				}
				else if($linkType == 3)
				{
					$datas['type'] = Menu_Object_Item::$TYPE_FOLDER_EXTERNAL;
					$datas['link'] = $form->getValue("externalpage");
				}
				else if($linkType == 4)
				{
					$datas['type'] = Menu_Object_Item::$TYPE_FOLDER_NO_LINK;
				}
				
				$item->fromArray($datas);
				$item->save();
				
				/** PERSMISSIONS **/
				$backAcl->updatePermissionsFromAclForm("mod_menu-item-".$id, $_POST['ACL']);
				
				$this->closeandredirect($this->_helper->route->short('index'));
			}
		}
		else {
			$form->populate($item->toArray());
		}
		
		/** SET VALUE TYPE PAGE **/
		if( $item->type == Menu_Object_Item::$TYPE_FOLDER_LINK_CHILDREN )
		{
			$form->getElement("linkType")->setValue(1);
		}
		else if ( $item->type == Menu_Object_Item::$TYPE_FOLDER_PAGE )
		{
			$form->getElement("existingpage")->setValue($item->link);
			$form->getElement("linkType")->setValue(2);
		}
		else if( $item->type == Menu_Object_Item::$TYPE_FOLDER_EXTERNAL)
		{
			$form->getElement("externalpage")->setValue($item->link);
			$form->getElement("linkType")->setValue(3);
		}
		else if ( $item->type == Menu_Object_Item::$TYPE_FOLDER_NO_LINK )
		{
			$form->getElement("linkType")->setValue(4);
		}
		
		/** PERSMISSIONS **/
		$form->setAction($this->_helper->route->short('edit-folder', array('id' => $id)));
		if($backAcl->hasPermission("mod_menu-item-default", "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_menu-item-default");
			$form->addSubForm($formAcl, "permissions");
			$this->view->formAcl = $formAcl;
		}
		
		/** VIEW **/
		$this->view->backAcl = $backAcl;
		$this->view->form = $form;
	}
	
	public function createContentAction()
	{
		if(!Zend_Session::namespaceIsset("createContentFromMenu"))
			throw new Zend_Exception(_t("Missing datas from menu"));
		
		/*
		 * Récupération des données stockées en session
		 */
		$session 	= new Zend_Session_Namespace("createContentFromMenu");
		$datas 		= $session->datas;
		$acl		= $session->acl;
		
		/*
		 * Recherche de l'api à utiliser
		 */
		$hooks 		= CMS_Application_Hook::getInstance();
		$allPages 	= $hooks->apply_filters("listCreateApi");
		
		foreach ($allPages as $key => $type) {
			if ($datas["chooseType"] == $key) {
				$apiInfo = $type;
				break;
			}
		}
		
		/*
		 * Création de l'instance de l'api
		 */
		if(isset($apiInfo["api_params"]))
			$api = new $apiInfo["api_name"]($apiInfo["api_params"]);
		else
			$api = new $apiInfo["api_name"]();
		
		if(!$api->hasPermission("create"))
			die("pas les permissions");
		
		// Formulaire relatif aux informations SEO de la future page
		$seoForm = new Menu_Form_Seo();
		
		if($this->getRequest()->isPost()) 
		{
			if($api->isValid($_POST) && $seoForm->isValid($_POST)){
				
				$page = $api->create();
				
				if(!$page)
					throw new Zend_Exception("Error when creating the content");
				
				/*
				 * Test si l'URL rewrite n'existe pas déjà (sinon on concatène "-n")
				 */
				$raw_url_rewrite_langs = $seoForm->getValue("seo_url_rewrite");
				
				foreach ($raw_url_rewrite_langs as $key => $raw_url_rewrite) { // Gestion du multi langue
					
					if( empty($raw_url_rewrite) )
						continue;
					
					$url_rewrite[$key] = $raw_url_rewrite;
					
					// Si l'url rewrite a changé
					if($page->url_rewrite != $raw_url_rewrite){
						
						//On test si le nouveau rewrite existe déjà
						$hasRewriteExist = CMS_Page_Object::get($raw_url_rewrite);
						if($hasRewriteExist !== null){
							$i = 1;
							do {
								$i++;
								$url_rewrite[$key] = $raw_url_rewrite."-".$i;
								
								$hasRewriteExist = CMS_Page_Object::get($url_rewrite[$key]);
							} while ($hasRewriteExist !== null);
						}
					}
				}
				
				/*
				 * Enregistrement des informations de la page associée au contenu
				 */
				$page->title 				= $seoForm->getValue("seo_title");
				$page->url_rewrite 			= $url_rewrite ? $url_rewrite : null;
				$page->meta_keywords 		= $seoForm->getValue("seo_meta_keywords");
				$page->meta_description 	= $seoForm->getValue("seo_meta_description");
				$page->template 			= (int)$_POST["select_template_page"] != 0 ? $_POST["select_template_page"] : null;
				$page->diaporama 			= $_POST["select_diaporama_page"] != "null" ? $_POST["select_diaporama_page"] : null;
				
				$page->save();
				
				/*
				 * Enregistrement de l'entrée du menu
				 */
				$datas['link'] = $page->id_page;
				$datas['type'] = Menu_Object_Item::$TYPE_PAGE;
				
				if( !$datas["item_id"] ) // Add item
				{
					$model = new Menu_Model_DbTable_Menu();
					$item_id  = $model->addItem($datas);
				}
				else // Edit item
				{
					$item = new Menu_Object_Item($datas["item_id"], 'all');
					$item->fromArray($datas);
					$item->save();
					$item_id = $datas["item_id"];
				}
				
				/*
				 * PERSMISSIONS 
				 */
				$backAcl = CMS_Acl_Back::getInstance();
				if( !$datas["item_id"] ) // Add item
				{
					if ($item_id != 0)
					{
						if($acl)
			            	$backAcl->addPermissionsFromAclForm("mod_menu-item-".$item_id, $acl);
						else 
							$backAcl->addPermissionsFromDefaultAcl("mod_menu-item-".$item_id, "mod_menu-item-default");
					}
				}
				else // Edit item
				{
					$backAcl->updatePermissionsFromAclForm("mod_menu-item-".$item_id, $acl);
				}
				
				Zend_Session::namespaceUnset("createContentFromMenu");
				
				if ($_POST['submitandquit'])
					return $this->_redirect($this->_helper->route->short("index"));
				
				return $this->_redirect($this->_helper->route->short("edit-content", array('id' => $item_id)));	
			}
		}
		else {
			/*
			 * Populate
			 */
			$seoForm->getElement("seo_title")->setValue($datas["label"]);
			
			$sanitize = new CMS_Filter_Sanitize();
			
			/*
			 * Test si l'URL rewrite n'existe pas déjà (sinon on concatène "-$i")
			 */
			foreach ($datas["label"] as $lang_id => $label) { // Patch multi-lang
				if( empty($label) )
					continue;
				
				$raw_url_rewrite = $sanitize->filter($label);
				$url_rewrite[$lang_id] = $raw_url_rewrite;
				
				$hasRewriteExist = CMS_Page_Object::get($raw_url_rewrite);
				$i = 1;
				
				while($hasRewriteExist !== null){
					
					$i++;
					$url_rewrite[$lang_id] = $raw_url_rewrite."-".$i;
					
					$hasRewriteExist = CMS_Page_Object::get($url_rewrite[$lang_id]);
				}
			}
			
			if( $url_rewrite )
				$seoForm->getElement("seo_url_rewrite")->setValue($url_rewrite);
		}
		
		$api->setExternalDatas(array(
			"title" => $datas["label"],
			"access" => $datas["access"]
		));
		
		// Récupération du html de création de la page
		$html = $api->getHTML();
		
		/*
		 * CVARS View
		 */
		$this->view->content = $html;
		$this->view->seoForm = $seoForm;
		$this->view->templatePreview = Blocs_Lib_Manager::getRenderTemplate();
	}
	
	public function editContentAction()
	{
		$this->setLayoutIframe();
		
		// ID de l'item de menu
		$id = $this->_request->getParam('id');
		
		if( !$id ) {
			_error(_t('Empty ID'));
			$this->_redirect($this->_helper->route->short('index'));
		}
		
		$item = Menu_Object_Item::getOne((int)$id);
		
		$page = CMS_Page_PersistentObject::getOneFromDB( array('A.id_page' => (int)$item->link), null, null, "all" );
		
		$api = new $page->api(array("content_id" => $page->content_id));
		
		if(!$api->hasPermission("edit"))
			die("pas les permissions");
		
		//Formulaire relatif aux informations SEO de la future page
		$seoForm = new Menu_Form_Seo();
		
		if($this->getRequest()->isPost()) 
		{
			if($api->isValid($_POST) && $seoForm->isValid($_POST)){
				
				/*
				 * Enregistrement des informations de la page associée au contenu
				 */
				$page->title 				= $seoForm->getValue("seo_title");
				$page->url_rewrite 			= $seoForm->getValue("seo_url_rewrite");
				$page->meta_keywords 		= $seoForm->getValue("seo_meta_keywords");
				$page->meta_description 	= $seoForm->getValue("seo_meta_description");
				$page->template 			= (int)$_POST["select_template_page"] != 0 ? $_POST["select_template_page"] : null;
				$page->diaporama 			= $_POST["select_diaporama_page"] != "null" ? $_POST["select_diaporama_page"] : null;
				
				$page->save();
				
				/*
				 * Enregistrement des informations du document
				 */
				$api->update();
				
				if ($_POST['submitandquit'])
					return $this->_redirect($this->_helper->route->short("index"));
				
				return $this->_redirect($this->_helper->route->short("edit-content", array('id'=>$id)));	
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
		
		// Récupération du html d'édition de la page
		$html = $api->getHTML();
		
		/*
		 * CVARS View
		 */
		$this->view->id = $id;
		$this->view->content = $html;
		$this->view->seoForm = $seoForm;
		$this->view->templatePreview = Blocs_Lib_Manager::getRenderTemplate($page->template, $page->diaporama);
	}
	
	/** DELETE **/
	
	public function deleteMenuAction()
	{		
		$id = $this->_request->getParam('id');
		
		$backAcl = CMS_Acl_Back::getInstance();
		if ($backAcl->hasPermission("mod_menu-menu-".$id, "delete"))
		{			
			$menu = new Menu_Object_Menu($id);
			$menu->disableRenderNested();
			$menu->generate();
			
			foreach ($menu->items as $item) {
				$backAcl->deletePermissions("mod_menu-item-".$item->id);
				$item->delete();
			}
			
			$menu->delete();
			
			$backAcl->deletePermissions("mod_menu-menu-".$id);
			
			_message(_t('menu deleted'));
			return $this->_redirect($this->_helper->route->short(''));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function deleteItemAction()
	{
		$id = intval($this->_request->getParam ('id'));
		$elem = $this->_request->getParam('elem');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if ($backAcl->hasPermission("mod_menu-item-".$id, "delete"))
		{
			$item = new Menu_Object_Item($id);
			
			/*
			 * Delete content
			 */
			if( $elem == "deletecontent" )
			{
				$page = CMS_Page_Object::get((int)$item->link);
				$api = new $page->api(array("content_id" => $page->content_id));
				$api->delete();
			}
			
			/*
			 * Delete item
			 */
			$item->delete();
			
			/*
			 * Delete permission menu item
			 */
			$backAcl->deletePermissions("mod_menu-item-".$id);
			
			_message(_t('menu item deleted'));
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function deleteFolderAction()
	{
		$id = (int)$this->_request->getParam ('id');
		$elem = $this->_request->getParam('elem');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if (!$backAcl->hasPermission("mod_menu-item-".$id, "delete")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		$model = new Menu_Model_DbTable_Menu();	
		
		if( $elem == "deletechildren" || $elem == "deletechildrenandcontent" ) {

			// Récupération de l'élement
			$item = new Menu_Object_Item($id);
			
			// S'il possède des enfants
			if($item->rgt != $item->lft +1){
				
				// Récupération des enfants
				$folder = new Menu_Object_Menu($item->menu_id);
				$folder->setRootFolder($id);
				$folder->disableRenderNested();
				$folder->generate();

				foreach ($folder->items as $item) {
					
					// Suppression des permissions de l'enfant
					$backAcl->deletePermissions("mod_menu-item-".$item->id_menu);
					
					// Suppression du contenu si voulu
					if( $elem == "deletechildrenandcontent" && $item->isDeletableContent() ) {
						$page = CMS_Page_Object::get((int)$item->link);
						$api = new $page->api(array("content_id" => $page->content_id));
						$api->delete();
					}
				}
			}

			// Suppression de l'élement et ses enfants
			$model->deleteFolder($id, true);
			// Suppression des permissions
			$backAcl->deletePermissions("mod_menu-item-".$id);
		}
		else
		{
			// Suppression du dossier seul
			$model->deleteFolder($id, false);
			// Suppression des permissions
			$backAcl->deletePermissions("mod_menu-item-".$id);
		}
		
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	/** Enable / Disable **/
	
	public function enableAction() // SET ENABLE
	{
		$id = (int)$this->_request->getParam('id');
		
		$menu = new Menu_Object_Item($id);
		$menu->active(true);
		
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function disableAction() // SET DISABLE
	{
		$id = (int)$this->_request->getParam('id');
		
		$menu = new Menu_Object_Item($id);
		$menu->active(false);
		
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function closeandredirect($url)
	{
		// reloading or updating the parent windows will force the popup to close  
		echo '
		<html><script language="javascript">
			parent.location.href="'.BASE_URL.$url.'";
		</script></html>';

	}
}