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

class galerieImage_BackController extends Zend_Controller_Action
{
	private $_idTypeGalerie;
	private $_namePermission;
	
	public function fixIdPageAction()
	{
		$galeries = GalerieImage_Object_Galerie::get();
		
		foreach ($galeries as $galerie) {
			$images = $galerie->nodes;

			if($images) {
				foreach ($images as $image) {
					
					$datas = json_decode($image->datas, true);
					
					if (is_numeric($datas['page_link']))
						continue;
					
					if($datas['page_link'] == '/' || $datas['page_link'] == '') {
						$datas['page_link'] = 1;
					}
					else {
						$page = CMS_Page_Object::getPageFromUri($datas['page_link']);
						
						if (!$page) {
							echo "Page non trouvé : " . $datas['page_link'] . " ( id image:".$image->id.")<br/>";
							continue;
						}
						
						$datas['page_link'] = $page->id_page;
					}
					
					$image->datas = json_encode($datas);
					$image->save();
				}
			}
		}
		
		echo "<br/>finish";
		die;
	}
	
	public function init()
	{
		if( Zend_Controller_Front::getInstance()->getRequest()->getParam('_isGaleriePhoto') == true )
		{
			$this->_idTypeGalerie = GalerieImage_Lib_Manager::getIdType("galerie");
			$this->_namePermission = GalerieImage_Lib_Manager::getPermissionType($this->_idTypeGalerie);
		}
		elseif( Zend_Controller_Front::getInstance()->getRequest()->getParam('_isDiaporama') == true )
		{
			$this->_idTypeGalerie = GalerieImage_Lib_Manager::getIdType("diaporama");
			$this->_namePermission = GalerieImage_Lib_Manager::getPermissionType($this->_idTypeGalerie);
		}
		
		$this->view->namePermission = $this->_namePermission;
	}
	
	public function indexAction()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if($backAcl->hasPermission($this->_namePermission, "view"))
		{
			/** Récupération des galeries suivant le type **/
			$obj = new GalerieImage_Object_Galerie();
			$galeries = $obj->get(array("type" => $this->_idTypeGalerie));
			
			/** Assigne le contenue récupéré **/
			$this->view->content = $galeries;
			
			/** Type de galerie et nom de persmission **/
			$this->view->type = GalerieImage_Lib_Manager::getNameType($this->_idTypeGalerie);
			$this->view->namePermission = $this->_namePermission;
			
			/** Permissions **/
			if($backAcl->hasPermission($this->_namePermission, "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm($this->_namePermission);
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
			if($backAcl->updatePermissionsFromAclForm($this->_namePermission, $_POST['ACL']))
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
		
		if($backAcl->hasPermission($this->_namePermission, "create"))
		{
			$form = new GalerieImage_Form_Galerie(GalerieImage_Lib_Manager::getNameType($this->_idTypeGalerie));
			$form->setAction($this->_helper->route->short('create'));
			
			$form_image = new GalerieImage_Form_Image(array("id" => "formImage"));
			
			if($this->getRequest()->isPost())
			{
				if($form->isValid($_POST))
				{
					$galerie = new GalerieImage_Object_Galerie();
					
					/** Galerie **/
					$datas["title"] 		= $_POST["title"];
					$datas["type"]			= $this->_idTypeGalerie;
					$datas["nb_image"]		= count($_POST["datas"]);
					$datas["bg_color"]		= $_POST["bg_color"];
					$datas["size"]			= $_POST["size"];
					$datas["style"] 				= $form->getValue("style");
					$datas["transition"] 			= $form->getValue("transition");
					$datas["controls_position"] 	= $form->getValue("controls_position");
					$datas["controls_style"] 		= $form->getValue("controls_style");
					$datas["autostart"] 			= $form->getValue("autostart");
					
					$datas["access"] 		= null;
					
					/** Images **/
					$order_image			= explode(",", $_POST["ordre_image"]);
					
					$datas["nodes"] 		= GalerieImage_Lib_Manager::createArrayImages($order_image, $_POST["datas"]);					
					
					
					/** Enregistrement **/
					$galerie->fromArray($datas);
					
					$id = $galerie->save();
					
					/** Permissions **/
					if($_POST['ACL'])
		            	$backAcl->addPermissionsFromAclForm($this->_namePermission."-".$id, $_POST['ACL']);
					else 
						$backAcl->addPermissionsFromDefaultAcl($this->_namePermission."-".$id, $this->_namePermission."-default");
					
					if( $this->_idTypeGalerie == GalerieImage_Lib_Manager::ID_TYPE_GALERIE )
					{
						
						$page = new CMS_Page_PersistentObject();
						
						$page->title 		= array( CURRENT_LANG_ID => $galerie->title);
						$page->type 		= "galerieImage";
						$page->content_id 	= $id;
						$page->url_system 	= $this->_helper->route->full('galeriePhoto', array("action"=>"view", "id"=>$id));
						$page->enable 		= 1;

						$page->save();

					}
					
					_message(_t('Galerie created'));
					
					
					if ($_POST['submitandquit'])
						return $this->_redirect($this->_helper->route->short("index"));

					return $this->_redirect($this->_helper->route->short("edit", array('id'=>$id)));					

				}
				else
					_error(_t('invalid form'));
			}
			
			if($backAcl->hasPermission($this->_namePermission."-default", "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm($this->_namePermission."-default");
				$form->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			}
			
			$this->view->form = $form;
			$this->view->formImage = $form_image;
			$this->view->type = GalerieImage_Lib_Manager::getNameType($this->_idTypeGalerie);
			$this->view->namePermission = $this->_namePermission;
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
		
		if($backAcl->hasPermission($this->_namePermission."-".$id, "edit"))
		{
			$galerie = new GalerieImage_Object_Galerie($id);
			$datas = $galerie->toArray();

			$form = new GalerieImage_Form_Galerie(GalerieImage_Lib_Manager::getNameType($this->_idTypeGalerie));
			
			$form_image = new GalerieImage_Form_Image(array("id" => "formImage"));
			
			if($this->getRequest()->isPost())
			{
				if($form->isValid($_POST))
				{
					/** Galerie **/
					$datas["title"] 		= $_POST["title"];
					$datas["nb_image"]		= count($_POST["datas"]);
					$datas["type"]			= $this->_idTypeGalerie;
					$datas["bg_color"]		= $_POST["bg_color"];
					$datas["size"]			= $_POST["size"];
					$datas["style"] 			= $form->getValue("style");
					$datas["transition"] 			= $form->getValue("transition");
					$datas["controls_position"] 	= $form->getValue("controls_position");
					$datas["controls_style"] 		= $form->getValue("controls_style");
					$datas["autostart"] 			= $form->getValue("autostart");
					
					$datas["access"] 		= null;
					
					/** Images **/
					$order_image			= explode(",", $_POST["ordre_image"]);
					$datas["nodes"] 		= GalerieImage_Lib_Manager::createArrayImages($order_image, $_POST["datas"]);	

					/** Enregistrement **/
					$galerie->fromArray($datas);
					
					$galerie->save();
					
					$backAcl->updatePermissionsFromAclForm($this->_namePermission."-".$id, $_POST['ACL']);
					
					if( $this->_idTypeGalerie == GalerieImage_Lib_Manager::ID_TYPE_GALERIE )
					{
						$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'galerieImage', 'content_id' => $id), null, null, "all" );
						
						if(!$page)
							_error(_t("Page object has not been updated because it was not found"));
						else {
							$page->title = array( CURRENT_LANG_ID => $galerie->title);
							$page->save();
						}
					}
					
					_message(_t('Galerie saved'));
					
					if ($_POST['submitandquit'])
						return $this->_redirect($this->_helper->route->short("index"));

					return $this->_redirect($this->_helper->route->short("edit", array('id'=>$id)));	
				}
				else
					_error(_t('invalid form'));
			}

			$nodes = $galerie->nodes;
			
			if( count($nodes) > 0 )
			{
				$images = array();
				$ordre_image = array();
						
				foreach ( $nodes as $key => $image )
				{		
					$ordre_image[] = $key;
								
					$imageArr = array();

					$imageArr["path"] 			= $image->path;
					$imageArr["path_thumb"] 	= $image->path_thumb;
					$imageArr["path2"] 			= $image->path2;
					$imageArr["path_thumb2"] 	= $image->path_thumb2;
					$imageArr["description"] 	= $image->description;
					$imageArr["bg_color_image"] = $image->bg_color;
					$imageArr["isPermanent"] 	= ($image->isPermanent) ? "true" : "false";
					
					if( $image->date_start)
						$imageArr["date_start"] = CMS_Application_Tools::_convertDateTimeUsToPicker($image->date_start);
					else
						$imageArr["date_start"] = null;
						
					if( $image->date_end)
						$imageArr["date_end"] = CMS_Application_Tools::_convertDateTimeUsToPicker($image->date_end);
					else 
						$imageArr["date_end"] = null;
					
					$datasImage = json_decode($image->datas, true);
					
					$imageArr["width"] 			= $datasImage["width"];
					$imageArr["height"] 		= $datasImage["height"];
					$imageArr["thumb_width"] 	= $datasImage["thumb_width"];
					$imageArr["thumb_height"]	= $datasImage["thumb_height"];
					
					$imageArr["window"]		 	= ($datasImage["window"]) ? "true" : "false";
					$imageArr["addLink"]		= ($datasImage["addLink"]) ? "true" : "false";
					$imageArr["link_type"] 		= $datasImage["link_type"];
					$imageArr["external_page"] 	= $datasImage["external_page"];
					$imageArr["page_link"] 		= $datasImage["page_link"];
					
					$images[] = $imageArr;
				}

				unset($datas["datas"]);
				
				$datas["ordre_image"] = implode($ordre_image,",");
			}

			$form->setAction($this->_helper->route->short('edit', array('id'=>$id)));
			$form->populate((array)$datas);
			
			if($backAcl->hasPermission($this->_namePermission."-".$id, "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm($this->_namePermission."-".$id);
				$form->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			}
			
			$this->view->datas = $images;
			
			$this->view->form = $form;
			$this->view->formImage = $form_image;
			
			$this->view->type = GalerieImage_Lib_Manager::getNameType($this->_idTypeGalerie);
			$this->view->namePermission = $this->_namePermission;
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
	
	public function deleteAction()
	{
		$id = intval($this->_request->getParam('id'));
		
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if($backAcl->hasPermission($this->_namePermission."-".$id, "delete"))
		{			
			/** Delete Galerie **/
			$galerie = new GalerieImage_Object_Galerie($id);
			$galerie->delete();
			
			/** Delete Permission **/
			$backAcl->deletePermissions($this->_namePermission."-".$id);
			
			/** Delete diaporama in page **/
			$pages = CMS_Page_Object::get(array("diaporama" => $id));
			if( count($pages) > 0 )
			{
				foreach ($pages as $page)
				{
					$page->diaporama = null;
					$page->save();
				}
			}
			
			if ($this->_idTypeGalerie == GalerieImage_Lib_Manager::ID_TYPE_GALERIE) {
				
				$page = CMS_Page_PersistentObject::getOneFromDB(array('type' => 'galerieImage', 'content_id' => $id), null, null, "all");
				
				if (!$page)
					_error(_t("Page object has not been deleted because it was not found"));
				else
					$page->delete();
				
			}
			
			_message(_t('Galerie deleted'));
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
	}
}