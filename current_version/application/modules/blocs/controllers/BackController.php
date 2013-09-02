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

class Blocs_BackController extends CMS_Controller_Action
{
	/*public function migrationBlocsMultiLangAction()
	{
		$model = new Blocs_Model_DbTable_Items();
		$model->migrationBlocsMultiLang();
		
		die('finish');
	}*/
	
    public function indexAction()
    {
    	$this->redirectIfNoRights('mod_bloc', 'view');
    	
    	$templateType = $this->_request->getParam('id');
    	$this->view->templateType = (!empty($templateType) ? $templateType : 'classic');
    	
    	$backAcl = CMS_Acl_Back::getInstance();
    	
		// Récupération des templates
		$templates = Blocs_Object_Template::get(null, array('title' => 'ASC'));	

		// Récupération des blocs
		$blocs = CMS_Bloc_Abstract::get();
		$this->view->blocs = $blocs;
		
		/*
		 * Construction d'un tableau pour informer Javascript
		 */

		$info = array(); 
		foreach($templates as &$t) {
			
			$i = 1;
			
			$info[$t->id_template] = array();
			
			$info[$t->id_template]['infos'] = array(
				'title'		=> $t->title,
				'theme' 	=> $t->theme,
				'classCss' 	=> $t->classCss
			);
			
			$info[$t->id_template]['data'] = array();
			
			if($t->defaut == 1)
				$this->view->defaultTemplate = $t->id_template;
				
			if( $itemsPos = $t->getItemsPosition() ){
			
				foreach($itemsPos as $type => &$placeholders){
	
					$info[$t->id_template]['data'][$type] = array();
					
					foreach($placeholders as $ph => &$pos){
					
						$info[$t->id_template]['data'][$type][$ph] = array();
						
						foreach($pos as $key => $id){
							
							// Si ce bloc n'existe plus
							if(!isset($blocs[$id])){
								
								unset($itemsPos[$type][$ph][$key]);
								
								$t->setItemsPosition($itemsPos);
								$t->save();
								
								_warning('Le bloc '.$id.' n\'existait plus et a donc été supprimé des templates l\'utilisant...');
								continue;
							}
							
							$info[$t->id_template]['data'][$type][$ph][$i] = array(
								'id' 			=> $id,
								'designation' 	=> $blocs[$id]->designation,
								'type' 			=> $blocs[$id]->getType()
							);

							$i++;
						}
					}
				}
			}
			else {
				/*
				 * En JSON, un tableau associatif est un Object, un tableau vide est un Array.
				 * Notre mécanique JS de gestion des blocs n'est pas prévu pour du type Array. 
				 */
				$info[$t->id_template]['data'] = new stdClass();
			}
		}

		$this->view->templates = $templates;
		$this->view->templates_info = json_encode($info);
		

		/*
		 * Formulaire template
		 */
		
		$generalForm = new Blocs_Form_General();

		$item = $generalForm->getElement('select_template');
		foreach($templates as &$t) 
			$item->addMultiOption($t->id_template, $t->title);
			
		$generalForm->setAction($this->_helper->route->short('index'));
		
		$this->view->formGeneral = $generalForm;
		
		
		/*
		 * Informations des blocs
		 */

		$blocsInfos = Blocs_Lib_Manager::getAllBlocXml();
		$this->view->blocsInfos = $blocsInfos;
		
		
		// Trie des blocs par type
		$retour = array();
		if(!empty($blocs)) {
			foreach($blocs as $bloc)
				$retour[$bloc->getType()][] = $bloc;
		}
		
		// >> Tri des blocs dans chaque type
		function sortByTitle($a, $b){
		    return strcasecmp($a->designation, $b->designation);
		}

		foreach($retour as &$blocsLst){
			usort($blocsLst, 'sortByTitle');
		}
		
		// Trie des types de blocs
		ksort($retour);
		
		unset($blocsLst);
		// <<
		
		$this->view->blocsSortByType = $retour;
		
		/*
		 * Calcul d'un index sur la palette de couleur
		 */
		$totalTypes = count($retour);
		$incrementType = round(30 / $totalTypes)-1;
		$i = 0;
		
		$typeIndex = array();
		foreach ($retour as $k => $e) {
			$typeIndex[$k] = $i;
			$i += $incrementType;
		}
		
		$this->view->typeIndex = $typeIndex;
					
		/*
		 * Type de template
		 */
		$config = CMS_Application_Config::getInstance();
		$mobileConf = @json_decode($config->get('mobileConfig'), true);
		
		if(is_array($mobileConf) && !empty($mobileConf)) {
				$this->view->mobileEnabled = ($mobileConf['mobile']) ? true : false;
				$this->view->tabletEnabled = ($mobileConf['tablet']) ? true : false;
		}
		
		$newForm = new Blocs_Form_New();
		$newForm->setAction($this->_helper->route->short('create'));
		$this->view->formNew = $newForm;
		
		
		$formTemplate = new Blocs_Form_Template(array('id' => 'formTemplate'));
		$item = $formTemplate->getElement('select_template_duplicate');
		$templates = Blocs_Object_Template::get();
		foreach($templates as &$t) { $item->addMultiOption($t->id_template, $t->title); }
		$this->view->formTemplate = $formTemplate;
		
		// Get id template for home page (to not delete template of home page)
		$home = CMS_Page_Object::get(CMS_Page_Object::HOME_ID);
		$this->view->idTemplate_homePage = $home->template;
    }
    
    public function createAction()
    {
		$this->redirectIfNoRights('mod_bloc', 'createBlocs');
    	
		$backAcl = CMS_Acl_Back::getInstance();
    	
		$type = $this->_request->getParam('id');
			
		if(Blocs_Lib_Manager::isValidBlocType($type))
		{
			// Instanciation du bloc (vide)
			$bloc = new $type();
			
			$bloc->beforeRuntimeAdmin();
			
			// Récupération de son formulaire
			$form = $bloc->getAdminForm();
			
			// Si données valides
			if(isset($_POST['from']) && $_POST['from'] == 'bloc_form' && $form->isValid($_POST))
			{

				// Appel de la methode d'enregistrement
				$id = $bloc->save($form->getValues());
				
				/** Permissions **/
				if($_POST['ACL'])
	            	$backAcl->addPermissionsFromAclForm('mod_bloc-'.$id, $_POST['ACL']);
				else 
					$backAcl->addPermissionsFromDefaultAcl('mod_bloc-'.$id, 'mod_bloc-default');
				
				if ($_POST['submitandquit'])
					return $this->_redirect($this->_helper->route->short('index'));
					
	            return $this->_redirect($this->_helper->route->short('edit', array('id'=>$id)));
			}
				
			$form->setAction($this->_helper->route->short('create', array('id' => $type)));
			
			$this->view->form = $form;
			$this->view->blocAdmin = $bloc->renderAdmin();
		}
		else 
		{
			throw new Zend_Exception('Ce type de bloc n\'existe pas');
		}
		
		// Affichage du gestionnaire de permission si droit de manage
		if($backAcl->hasPermission('mod_bloc-default', 'manage'))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm('mod_bloc-default');
			$form->addSubForm($formAcl, 'permissions');
			$this->view->formAcl = $formAcl;
		}
    }
    
    public function editAction()
    {
    	$id = (int)$this->_request->getParam('id');
    	
    	$this->redirectIfNoRights('mod_bloc-'.$id, 'edit');
    	
		$backAcl = CMS_Acl_Back::getInstance();
		
		// Instanciation du bloc
		$bloc = CMS_Bloc_Abstract::getBlocInstance($id, 'all');
		
		// Récupération de son formulaire
		$form = $bloc->getAdminForm();
		
		// Si données valides
		if(isset($_POST['from']) && $_POST['from'] == 'bloc_form' && $form->isValid($_POST))
		{
			// Appelle de la methode d'enregistrement
			$bloc->save($form->getValues());
			
			$backAcl->updatePermissionsFromAclForm('mod_bloc-'.$id, $_POST['ACL']);
			
			if ($_POST['submitandquit'])
				return $this->_redirect($this->_helper->route->short('index'));

            return $this->_redirect($this->_helper->route->short('edit', array('id'=>$id)));
		}
		
		$form->setAction($this->_helper->route->short('edit', array('id' => $id)));
		$form->populate($bloc->toArray());
		
		$this->view->form = $form;
		$this->view->blocAdmin = $bloc->renderAdmin();
		
		// Affichage du gestionnaire de permission si droit de manage
		if($backAcl->hasPermission('mod_bloc-'.$id, 'manage'))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm('mod_bloc-'.$id);
			$form->addSubForm($formAcl, 'permissions');
			$this->view->formAcl = $formAcl;
		} 
    }
    
    public function deleteAction()
    {
    	$id = (int)$this->_request->getParam('id');
    	
    	if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
			
		$backAcl = CMS_Acl_Back::getInstance();
		if ($backAcl->hasPermission('mod_bloc-'.$id, 'delete'))
		{
			// Suppression du bloc
			CMS_Bloc_Abstract::deleteFromPrimaryKey($id);

			// Suppression permissions
			$backAcl->deletePermissions('mod_bloc-'.$id);

			// On vide les template du bloc supprimé
			$templates = Blocs_Object_Template::get();
			foreach ($templates as &$tpl){
				
				// Récupération des placeholders et leurs blocs
				if($itemsPos = $tpl->getItemsPosition()){
					foreach ($itemsPos as $ph => &$arrayBlocId){
						
						// Tous les blocs d'un placeholder
						foreach ($arrayBlocId as $key => $idBloc){
							if($idBloc == $id){
								unset($arrayBlocId[$key]);
							}
						}
					}
					
					$tpl->setItemsPosition($itemsPos);
					$tpl->save();
				}
			}
			
			_message(_t('Block deleted'));
			
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
    public function deletetemplateAction()
    {
    	$id = (int)$this->_request->getParam('id');
    	
    	if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
		
		// Get id template for home page (to not delete template of home page)
		$home = CMS_Page_Object::get((int) 1);
		$idTemplate_homePage = $home->template;
		
		if( $idTemplate_homePage == $id)
			throw new Zend_Exception(_t('Unable to delete home template'));
		
		$backAcl = CMS_Acl_Back::getInstance();
		if ($backAcl->hasPermission('mod_bloc', 'deleteTemplates'))
		{
			$tpl = new Blocs_Object_Template($id);
			$tpl->delete();
			
			CMS_Page_PersistentObject::setDefaultTplByTplID($id);
			
	
			$types = CMS_Page_Type::get(array('default_tpl' => $id));
			if( count($types) > 0 )
			{
				foreach ($types as $type)
				{
					$type->default_tpl = null;
					$type->save();
				}
			}
			
			_message(_t('Template deleted'));
			
			return $this->_redirect($this->_helper->route->short('index'));
		}
		else
		{
			_error(_t('Insufficient rights'));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
    }
    
    public function editTemplateOptionAction()
    {
    	$this->_helper->layout()->setLayout('lightbox');
    	
    	$this->redirectIfNoRights('mod_bloc', 'editTemplate');
    	
    	$id = (int)$this->_request->getParam('id');
    	
    	$template 	= new Blocs_Object_Template($id);
    	$form 		= new Blocs_Form_TemplateOptions();
    	$form->setAction($this->_helper->route->short('edit-template-option', array('id' => $id)));
    	$this->view->form = $form;
    	
    	if ($this->_request->isPost()) {
    		if ($form->isValid($_POST)) {
    
    			$template->title 	= htmlspecialchars($form->getValue('title'), ENT_NOQUOTES, 'UTF-8');
			    $template->theme 	= htmlspecialchars($form->getValue('theme'), ENT_NOQUOTES, 'UTF-8');
			    $template->classCss = htmlspecialchars($form->getValue('classCss'), ENT_NOQUOTES, 'UTF-8');
			    
			    $template->bgType 		= $form->getValue('bgType');
			    
			    $template->bgColor1 	= $form->getValue('bgColor1');
			    $template->bgColor2 	= $form->getValue('bgColor2');
			    $template->bgGradient 	= $form->getValue('bgGradient');
			    $template->bgPicture 	= reset($form->getValue('bgPicture'));
			    $template->bgRepeat 	= $form->getValue('bgRepeat');
			    
			    $template->getItemsPosition();
			    
			    $template->save();
			    
			    if($form->getValue('defaut') && $template->defaut != 1)
			    	Blocs_Object_Template::setDefault($id);
    			
    			return $this->closeFancybox();
    		}
    	}
    	else {
    		$form->populate($template->toArray());
    	}
    }
    
	public function permissionsAction()
	{
		$this->redirectIfNoRights('mod_bloc', 'manage');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		$formAcl = new CMS_Acl_Form_BackAclForm("mod_bloc");
		
		if ($this->getRequest()->isPost()) {
			if ($formAcl->isValid($_POST)) {
				$backAcl->updatePermissionsFromAclForm("mod_bloc", $_POST['ACL']);
				$this->_redirectCurrentPage();
			}
		}
		
		$this->view->formAcl = $formAcl;
	}
}
	