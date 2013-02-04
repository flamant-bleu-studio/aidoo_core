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

class GalerieImage_Lib_Api extends CMS_Api_Abstract implements CMS_Api_Interface
{
	private $form;
	
	private $type_galerie;
	
	public function __construct($params = null) {
		
		$this->moduleNamePermission = "mod_galeriePhoto";
		$this->type_galerie = GalerieImage_Lib_Manager::getIdType("galerie");
		
		parent::__construct($params);
	}
	
	public function getHTML( $params = array() ) {
		
		if( $this->isEditMode === true )
		{
			$galerie = new GalerieImage_Object_Galerie($this->content_id);
			$datas = $galerie->toArray();
			
			if( $datas["nodes"] > 0 )
			{
				$images = array();
				
				foreach ( $datas["nodes"] as $key => $image )
				{
					$imageArr = array();

					$imageArr["path"] 			= $image->path;
					$imageArr["path_thumb"] 	= $image->path_thumb;
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
			}
			
			$this->view->datas = $images;
		}
		
		$this->getForm();
		
		$form_image = new GalerieImage_Form_Image();
		$form_image->setAttrib("id", "formImage");
		
		$this->view->form 			= $this->form;
		$this->view->formImage 		= $form_image;
		$this->view->type 			= GalerieImage_Lib_Manager::getNameType($this->_idTypeGalerie);
		$this->view->namePermission = $this->moduleNamePermission;
		
		$path = $this->view->getScriptPaths();
    	$this->view->setScriptPath(realpath(dirname(__FILE__)).'/../views/render/');
    	
    	if($this->isEditMode === true)
    		$content = $this->view->renderInnerTpl("api-edit.tpl");
    	else
    		$content = $this->view->renderInnerTpl("api-create.tpl");
    	
    	$this->view->setScriptPath($path);
    	
    	return $content;
	}
	
	public function isValid($datas) {
		$this->getForm();
		return $this->form->isValid($datas);
	}
	
	public function populate() {
		
		if($this->isEditMode !== true){
			die("impossible de populate en mode création");
		}
		
		$datas = new GalerieImage_Object_Galerie($this->content_id);
		$datas = $datas->toArray();

		if( $datas["nodes"] > 0 )
		{
			$ordre_image = array();
			$i = 0;
			foreach ( $datas["nodes"] as $image ){
				$ordre_image[] = $i;
				$i++;
			}
			
			$ordre_image = implode($ordre_image, ",");
		}
			
		$this->getForm();
		
		// Populate du Zend_Form
		$this->form->populate($datas);
		$this->form->ordre_image->setValue($ordre_image);
	}
	
	private function getForm() {

		if( !$this->form ) {
			$this->form = new GalerieImage_Form_Galerie(GalerieImage_Lib_Manager::getNameType($this->type_galerie));
			
			if( $this->isEditMode !== true && !empty($this->_externalDatas) )
				$this->form->getElement("title")->setValue($this->_externalDatas["title"]['1']); // ['1'] => Module monolang !
		}
	}
	
	public function create() {
		
		if($this->isEditMode === true)
    		die("impossible d'appeler create en mode édition");
    	
    	$this->getForm();
    	
    	$galerie = new GalerieImage_Object_Galerie();
		
		/** Galerie **/
		$datas["title"] 	= $this->form->getValue("title");
		$datas["type"]		= $this->type_galerie;
		$datas["nb_image"]	= count($this->form->getValue("datas"));
		$datas["bg_color"]	= $this->form->getValue("bg_color");
		$datas["size"]		= $this->form->getValue("size");
		
		$datas["style"] 				= $this->form->getValue("style");
		$datas["transition"] 			= $this->form->getValue("transition");
		$datas["controls_position"] 	= $this->form->getValue("controls_position");
		$datas["controls_style"] 		= $this->form->getValue("controls_style");
		$datas["autostart"] 			= $this->form->getValue("autostart");
					
		$datas["access"] 	= null;
		
		/** Images **/
		$order_image		= explode(",", $this->form->getValue("ordre_image"));
		$datas["nodes"] 	= GalerieImage_Lib_Manager::createArrayImages($order_image, $this->form->getValue("datas"));					
		
		/** Enregistrement **/
		$galerie->fromArray($datas);
		
		$id = $galerie->save();
		
		$backAcl = CMS_Acl_Back::getInstance();
		if($_POST['ACL'])
            $backAcl->addPermissionsFromAclForm($this->moduleNamePermission."-".$id, $_POST['ACL']);
		else 
			$backAcl->addPermissionsFromDefaultAcl($this->moduleNamePermission."-".$id, $this->moduleNamePermission."-default");
		
		$page = new CMS_Page_PersistentObject();
		
		$page->title 		= $this->form->getValue("title");
		$page->type 		= "galerieImage";
		$page->url_system 	=  $this->_helper->full('galeriePhoto', array("action" => "view", "id" => $id));
		$page->api 			= __CLASS__;
		$page->content_id 	= $id;
		$page->visible		= 1;
		$page->enable		= 1;
		$page->wildcard		= 0;
		
		$page->save();
		
		return $page;
	}
	
	public function update() {
		
		if($this->isEditMode !== true)
    		die("impossible d'appeler update sans mode édition");
		
		$galerie = new GalerieImage_Object_Galerie($this->content_id);
		$datas = $galerie->toArray();
		
		/** Galerie **/
		$datas["title"] 	= $this->form->getValue("title");
		$datas["type"]		= $this->type_galerie;
		$datas["nb_image"]	= count($this->form->getValue("datas"));
		$datas["bg_color"]	= $this->form->getValue("bg_color");
		$datas["size"]		= $this->form->getValue("size");
		
		$datas["style"] 				= $this->form->getValue("style");
		$datas["transition"] 			= $this->form->getValue("transition");
		$datas["controls_position"] 	= $this->form->getValue("controls_position");
		$datas["controls_style"] 		= $this->form->getValue("controls_style");
		$datas["autostart"] 			= $this->form->getValue("autostart");
		
		$datas["access"] 	= null;
		
		/** Images **/
		$order_image		= explode(",", $this->form->getValue("ordre_image"));
		$datas["nodes"] 	= GalerieImage_Lib_Manager::createArrayImages($order_image, $this->form->getValue("datas"));					
		
		/** Enregistrement **/
		$galerie->fromArray($datas);
		$galerie->save();
	}
	
	public function delete()
	{
		if($this->isEditMode !== true)
    		die("impossible d'appeler delete sans mode édition");
    	
    	/** Delete la page du module **/
    	$galerie = new GalerieImage_Object_Galerie($this->content_id);
    	$galerie->delete();
    	
		/** Delete la page du core_page **/
    	$page = CMS_Page_PersistentObject::get($this->_helper->full('galeriePhoto', array("action" => "view", "id" => $this->content_id)));
    	$page->delete();
    	
    	/** Delete des permissions de la page **/
    	$backAcl = CMS_Acl_Back::getInstance();
    	$backAcl->deletePermissions($this->moduleNamePermission."-".$this->content_id);
	}
}