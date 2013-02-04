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

class Documents_Lib_Api extends CMS_Api_Abstract implements CMS_Api_Interface
{
	private $type;
	
	private $form;
	private $typesPath;
	
	/**
	 * Retourne une instance de l'API du module Documents
	 * @param array $params tableau associatif des paramètres de l'API
	 * - content_id : id du contenu à piloter
	 * @return Documents_Lib_Api
	 */
	public function __construct($params = null) {
		
		if(isset($params["type"]))
			$this->type = $params["type"];
		
		$this->moduleNamePermission = "mod_documents";
		$this->typesPath = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/documents';
		
		parent::__construct($params);
	}
	
	public function setType($_type) {
		$this->type = $_type;
	}
		
	public function getHTML( $params = array() ) {
		
		if($this->isEditMode === true ) {
			$document = new Documents_Object_Document($this->content_id);
			$doc_type = $document->type;
		}
		else if($this->type) {
			$doc_type = $this->type;
		}
		
		// Si le xml du type du document existe
		if (!file_exists($this->typesPath . '/' . $doc_type))
			throw new Exception(_t("Unknown document type"));
		
		// Création du formulaire lié au type
		$this->getForm($doc_type);
			
        // Affichage du gestionnaire de permission si droit de manage
        $backAcl = CMS_Acl_Back::getInstance();
		if( $backAcl->hasPermission($this->moduleNamePermission."-".$this->content_id, "manage") )
		{
			$formAcl = new CMS_Acl_Form_BackAclForm($this->moduleNamePermission."-".$this->content_id);
			$this->form->addSubForm($formAcl, "permissions");	
			$this->view->formAcl = $formAcl;
		}
			
		$this->view->form 		= $this->form;
		$this->view->documentType = $doc_type; 
			
		$path = $this->view->getScriptPaths();
	    $this->view->setScriptPath(realpath(dirname(__FILE__)).'/../views/render/');
	    	
	    if($this->isEditMode === true)
	    	$content = $this->view->renderInnerTpl("api-edit.tpl");
	    else
	    	$content = $this->view->renderInnerTpl("api-create.tpl");
	    	
	    $this->view->setScriptPath($path);
	    	
	    return $content;
	}
	
	public function isValid($datas){
		
		if(!$this->form) {
			if($this->isEditMode === true) {
				$document = new Documents_Object_Document($this->content_id);
				$type = $document->type;
			}
			else {
				$type = $this->type;
			}
			
			$this->getForm($type);
		}
		
		return $this->form->isValid($_POST);
	}
	
	public function populate() {
		
		if($this->isEditMode !== true){
			die("impossible de populate en mode création");
		}
		
		$document = new Documents_Object_Document($this->content_id, 'all');
		
		$this->getForm($document->type);
		
		// Mise en tableau des valeurs récupérées depuis la BDD
		$valeurs = $document->toArray();

		foreach ( $valeurs['nodes'] as $key => $value){
			$valeurs[$key] = $value;
		}
		
		// Populate du Zend_Form
		$this->form->populate($valeurs);
	}
	
	private function getForm($type) {
		
		if( !$this->form ) {
			$this->form = Documents_Lib_Manager::createForm($type);
			
			if($this->isEditMode !== true) {
				$this->form->getElement("title")->setValue($this->_externalDatas["title"]);
				$this->form->getElement("access")->setValue($this->_externalDatas["access"]);
			}
		}
	}

	public function create() {

		if($this->isEditMode === true)
			die("impossible d'appeler create en mode édition");
		 
		// Si le type existe
		if(isset($this->type) && file_exists($this->typesPath.'/'.$this->type)) {

			$this->getForm($this->type);

			$document = new Documents_Object_Document();
			 
			$document->type 	= $this->type;
			
			// Récupération des valeurs génériques à tous les documents
			$document->template = $this->form->getValue('template');
			$document->status 	= $this->form->getValue('status');
			$document->title 	= $this->form->getValue('title');
			$document->author 	= Zend_Registry::get('user')->id;
			$document->access 	= $this->form->getValue('access');

			// Traitement des nodes
			$nodesElements = $this->form->getDisplayGroup("typeElements")->getElements();

			if ($nodesElements){
	    		
	    		$nodes = array();
				
	    		// Pour chaque node
			    foreach ($nodesElements as $name => $el){
					$nodes[$name] = $el->getValue();
				}
				
				$document->nodes =  $nodes;
	    	}

			// Enregistrement
			$id = $document->save();

			// Enregistrement des droits
			$backAcl = CMS_Acl_Back::getInstance();
			 
			if($_POST['ACL'])
				$backAcl->addPermissionsFromAclForm($this->moduleNamePermission."-".$id, $_POST['ACL']);
			else
				$backAcl->addPermissionsFromDefaultAcl($this->moduleNamePermission."-".$id, $this->moduleNamePermission."-default");
				
			$page = new CMS_Page_PersistentObject();
				
			$page->title 		= $document->title;
			$page->type 		= "document";
			$page->url_system 	= $this->_helper->full('doc', array('action'=>"view", 'id' => $id));
			$page->api 			= __CLASS__;
			$page->content_id 	= $id;
				
			$page->save();
				
			return $page;	
		}
		else 
			throw new Zend_Exception(_t("Invalid document type"));
	}
	
 	public function update() {
 		
    	if($this->isEditMode !== true)
    		die("impossible d'appeler update sans mode édition");
    	
    	$document = new Documents_Object_Document($this->content_id, 'all');
    	
    	// Récupération des valeurs génériques à tous les documents
    	$document->template = $this->form->getValue('template');
    	$document->status 	= $this->form->getValue('status');
    	$document->title 	= $this->form->getValue('title');
    	$document->author 	= Zend_Registry::get('user')->id;
    	$document->access 	= $this->form->getValue('access');
    	
    	// Traitement des nodes
    	$nodesElements = $this->form->getDisplayGroup("typeElements")->getElements();

    	
    	if ($nodesElements){
    		
    		$nodes = array();
			
    		// Pour chaque node
		    foreach ($nodesElements as $name => $el){
				$nodes[$name] = $el->getValue();
			}
			
			$document->nodes =  $nodes;
    	}

    	// Enregistrement
    	$id = $document->save();
    	
    	// Enregistrement des droits
    	$backAcl = CMS_Acl_Back::getInstance();
    	//$backAcl->updatePermissionsFromAclForm($this->moduleNamePermission."-".$id, $_POST['ACL']);
		
    	return;
    }
    
    public function delete()
    {
    	if($this->isEditMode !== true)
    		die("impossible d'appeler delete sans mode édition");
    	
    	$id = (int)$this->content_id;
    	
    	/** Delete la page du module **/
    	Documents_Object_Document::deleteFromPrimaryKey($id);
		
    	/** Delete la page du core_page **/
    	$page = CMS_Page_PersistentObject::getOneFromDB(array("url_system" => $this->_helper->full('doc', array("action" => "view", "id" => $id))));
    	
    	if(!$page)
			_error(_t("Page object has not been deleted because it was not found"));
		else
			$page->delete();
    	
		/** Delete des permissions de la page **/
		$backAcl = CMS_Acl_Back::getInstance();
		$backAcl->deletePermissions($this->moduleNamePermission."-".$this->content_id);
    }
    
}