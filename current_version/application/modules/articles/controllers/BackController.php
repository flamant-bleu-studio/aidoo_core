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

class Articles_BackController extends Zend_Controller_Action
{

	public function migrationMultiLangAction() {
		
		$model = new Articles_Model_DbTable_Articles();
		$model->migrationMultiLang();
		
		die("finish");
	}
	
	public function indexAction()
	{		
		$backAcl = CMS_Acl_Back::getInstance();
		$this->view->backAcl = $backAcl;
		
		if(!$backAcl->hasPermission("mod_articles", "view")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		// Récupération des articles
		$articles = Articles_Object_Article::get(array("isSubmitted" => 0));
		$this->view->c = $articles;
		
		$articles = Articles_Object_Article::get(array("isSubmitted" => 1));
		$this->view->submittedArticles = $articles;
		
		// Récupération des catégories
		$categories = Articles_Object_Categorie::get();
		$this->view->categories = $categories;
	}
	
	public function createAction()
	{
		// Check des droits de création d'un article
		if(!CMS_Acl_Back::getInstance()->hasPermission("mod_articles", "create")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
        $typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/articles';
        
        try{
        	$dir = new DirectoryIterator($typesPath);
        }
        catch(Exception $e){}
        
        if($dir){
        	
	        foreach ($dir as $fileinfo) {
	        	
	        	if ($fileinfo->isDir() && !$fileinfo->isDot() && file_exists($fileinfo->getPathname().'/type.xml')){
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
		// Check des droits de création d'un article
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission("mod_articles", "create")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		$doc_type 	= $this->_request->getParam('id');
		
		
		// Si un type de document à été posté
		if(!isset($doc_type))
			throw new Zend_Exception(_t("No article type"));
			
		// Si le XML du type existe
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/articles';
					
		if (!file_exists($typesPath . '/' . $doc_type))
			throw new Zend_Exception(_t("Invalid article type"));
		
		// Création du formulaire
		$form = Articles_Lib_Manager::createForm($doc_type);
		
		// Affichage du gestionnaire de permission
		if($backAcl->hasPermission("mod_articles-default", "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_articles-default");
			$form->addSubForm($formAcl, "permissions");	
		
			$this->view->formAcl = $formAcl;
		} 
		
		// Si le formulaire est posté et valide (si param type, on souhaite enregistrer le document (fin étape 2))
		if($this->getRequest()->isPost() && $form->isValid($_POST)) {
			
			// Pour chaque node
			if( $nodesElements ) {
				foreach ($nodesElements as $name => $el) {
						            	
					$value = (is_array($el->getValue())) ? json_encode($el->getValue()) : $el->getValue() ;
							            	
					// Ajout des données récupérées
					$nodes[] = array(	
						'type' => 'A GERCLER',
						'name' => $name,
						'value' => $value
					);
				}
			}
            $document = new Articles_Object_Article();
            
            $document->type 		= $doc_type;
            $document->author 		= Zend_Registry::get('user')->id;
            
            // Set des données génériques
			if($form->getValue('category'))
            	$document->category = $form->getValue('category');
            else
            	$document->category = 2;	// (catégorie "Uncategorized")
            	
            $document->status 		= $form->getValue('status');
            $document->access 		= $form->getValue('access');
            $document->template 	= $form->getValue('template');
            
            $document->date_start 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_start'));
            $document->date_end 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_end'));
            $document->isPermanent 	= $form->getValue('isPermanent');
            
            $document->title 		= $form->getValue('title');
            $document->chapeau 		= $form->getValue('chapeau');
            $document->readmore 	= $form->getValue('readmore');
            
            // Une seule image => reset
            if( $form->getValue('image') )
	            $document->image 	= reset($form->getValue('image'));
            
            $document->setCategories($form->getValue('category'));
            
            // Facebook comments
            $document->fb_comments_active 	= $form->getValue('fb_comments_active');
            
			// Traitement des nodes
			$nodesElements = $form->getDisplayGroup("readmore_elements")->getElements();

			if ($nodesElements){
	    		
	    		$nodes = array();
				
	    		// Pour chaque node
			    foreach ($nodesElements as $name => $el){
			    	$tmp_values = $el->getValue();
					
			    	if ($el instanceof  CMS_Form_Element_MultiUpload)
			   			 	$tmp_values = json_encode($tmp_values);
			    	
					if (!is_array($tmp_values ))
						$nodes[$name] = array(DEFAULT_LANG_ID => $tmp_values );
					else 
						$nodes[$name] = $tmp_values ;
				}
				
				$document->nodes =  $nodes;
	    	}
	    	
            $id = $document->save();
            
            /*
             * Permissions
             */
            if($_POST['ACL'])
            	$backAcl->addPermissionsFromAclForm("mod_articles-".$id, $_POST['ACL']);
			else 
				$backAcl->addPermissionsFromDefaultAcl("mod_articles-".$id, "mod_articles-default");
			
			/*
			 * Page
			 */
			$sanitize = new CMS_Filter_Sanitize();
			$langs = json_decode(CMS_Application_Config::getInstance()->get("availableFrontLang"), true);
			
			$page = new CMS_Page_PersistentObject();
				
			$page->title 		= $document->title;
			$page->type 		= "articles-article";
			$page->content_id 	= $id;
			$page->url_system 	= $this->_helper->route->full('articles', array("module"=>"articles", "controller"=>"front", "action"=>"view", "id"=>$id));
			$page->enable 		= $document->status;
			foreach ($page->title as $lang_id => $title){
				$page->url_rewrite[$lang_id] = ($lang_id != DEFAULT_LANG_ID ? $langs[$lang_id].'/' : null).'article/'.$sanitize->filter($title);
			}
			$page->save();

			_message(_t('Article created'));

			// IF Save & Quit
			if ($_POST['submitandquit'])
				return $this->_redirect($this->_helper->route->short("index"));
            
            // Redirection vers l'accueil des documents
            return $this->_redirect($this->_helper->route->short("edit", array('id'=>$id)));
		}
		else {
			// Set des values facebook comments
			$options = json_decode(CMS_Application_Config::getInstance()->get("mod_articles-options"), true);
			$form->populate(array(
				"fb_comments_active" => $options["fb_comments_active_default"]
			));
		}
 
		// Si on affiche le form, set de l'action et le label du bouton submit
		$form->setAction($this->_helper->route->short('createdocument', array("id" => $doc_type)));

		$form->getElement('status')->setValue("publish");
		$form->getElement('isPermanent')->setValue("1");
		
		$date_start = date('Y-m-d H:i:s');
		$date_end  = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_start)) . " + 1 months"));
		
		$form->getElement("date_start")->setValue($date_start);
		$form->getElement("date_end")->setValue($date_end);
		
		$this->view->form = $form;
		        
        $this->view->backAcl = $backAcl;
	}
	
	public function editAction()
	{
		// Récupération de l'id et du document correspondant
		$id = (int) $this->_request->getParam('id');
		
		// Check des droits de création d'un article
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission("mod_articles-".$id, "edit")) {

			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}	
		
		$document = new Articles_Object_Article($id, 'all');
		
		// Si le xml du type du document existe
		$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/articles';
		if (file_exists($typesPath . '/' . $document->type))
		{
			// Création du formulaire lié au type
			$form = Articles_Lib_Manager::createForm($document->type);
			$form->setAction($this->_helper->route->short('edit', array('id'=>$id)));

            // Affichage du gestionnaire de permission si droit de manage
			if($backAcl->hasPermission("mod_articles-".$id, "manage"))
			{
				$formAcl = new CMS_Acl_Form_BackAclForm("mod_articles-".$id);
				$form->addSubForm($formAcl, "permissions");	
				$this->view->formAcl = $formAcl;
			}
            
            // S'il y a un POST
			if($this->getRequest()->isPost())
			{
				if($form->isValid($_POST)) {
					
					$nodesElements = $form->getDisplayGroup("readmore_elements")->getElements();
				    	
			    	// Traitement des nodes
					if ($nodesElements){
			    	
			    		$nodes = array();
			    		
			    		// Pour chaque node
			            foreach ($nodesElements as $name => $el){
			            	
			            	$value = (is_array($el->getValue())) ? json_encode($el->getValue()) : $el->getValue() ;
			            	
							// Ajout des données récupérées
						    $nodes[] = array(	
						        'type' => 'A GERCLER',
	          					'name' => $name,
	          					'value' => $value
							);
						}
			    	}
			    	
		            // Récupération des valeurs génériques à tous les documents
		            
		            $document->template = $form->getValue('template');
		            
		            // L'utilisateur qui modifie un article n'en prend pas sa possession
		            //$document->author 	= Zend_Registry::get('user')->id;
		            
		            $document->access 	= $form->getValue('access');
		            
		            $document->date_start 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_start'));
				    $document->date_end 	= CMS_Application_Tools::_convertDateTimePickerToUs($form->getValue('date_end'));
				    $document->isPermanent 	= $form->getValue('isPermanent');
				    
		          	$document->title 		= $form->getValue('title');
				    $document->chapeau 		= $form->getValue('chapeau');
				    $document->readmore 	= $form->getValue('readmore');
					
				    if( $form->getValue('image') )
						$document->image 	= reset($form->getValue('image'));
					else
						$document->image 	= null;
					
					$document->setCategories($form->getValue('category'));
			        
					// Facebook comments
		            $document->fb_comments_active 	= $form->getValue('fb_comments_active');
					
					// Traitement des nodes
					$nodesElements = $form->getDisplayGroup("readmore_elements")->getElements();
		
					if ($nodesElements){
			    		
			    		$nodes = array();
						
			    		// Pour chaque node
					    foreach ($nodesElements as $name => $el){
							$tmp_values = $el->getValue();
							
							if ($el instanceof  CMS_Form_Element_MultiUpload)
								$tmp_values = json_encode($tmp_values);
							
							if (!is_array($tmp_values ))
								$nodes[$name] = array(DEFAULT_LANG_ID => $tmp_values );
							else 
								$nodes[$name] = $tmp_values ;
						}
						
						$document->nodes =  $nodes;
			    	}
			    	
			    	$this->sendEmailArticleValidate($document, $form->getValue('status'));
					
					$document->status = $form->getValue('status');
					
		            // Enregistrement
		            $document->save();
		            
		            _message(_t("Article updated"));
		            
		            // Enregistrement des droits
					$backAcl->updatePermissionsFromAclForm("mod_articles-".$id, $_POST['ACL']);
					
					$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'articles-article', 'content_id' => $id), null, null, "all" );

					if(!$page)
						_error(_t("Page object has not been updated because it was not found"));
					else {
						$page->title = $document->title;
						$page->enable = $document->status;
						
						$page->save();
					}
					
					if ($_POST['submitandquit'])
						return $this->_redirect($this->_helper->route->short("index"));
					
					return $this->_redirect($this->_helper->route->short("edit", array('id'=>$id)));
				}
				else {
					_error(_t('Invalid form'));
				}
			}
			else {
				// Mise en tableau des valeurs récupérées depuis la BDD
				foreach ($document->chapeau as $key => $chapeau)
					$document->chapeau[$key] = strip_tags($document->chapeau[$key]);
				
				$valeurs = $document->toArray();
				
				unset($valeurs['nodes']['template']);
				
				foreach ( $valeurs['nodes'] as $key => $value){
					$tmpElem = $form->getElement($key);
					
					if ($tmpElem instanceof  CMS_Form_Element_MultiUpload)
						$valeurs[$key] = json_decode($value, true);
					
					if(method_exists($tmpElem, 'isTranslatable') && !$tmpElem->isTranslatable() && is_array($value))
						$valeurs[$key] = $value[DEFAULT_LANG_ID];
					else
						$valeurs[$key]= $value;
				}
				
				$categories = $document->getCategories();
				if( $categories ) {
					foreach ($categories as $categorie) {
						$valeurs['category'][] = $categorie->id_categorie;
					}
				}
				
				// MultiUpload s'attend à un tableau de valeurs
				$valeurs["image"] = array($valeurs["image"]);
				
				// Set des values facebook comments si vide
				$options = json_decode(CMS_Application_Config::getInstance()->get("mod_articles-options"), true);
				$valeurs["fb_comments_active"] = $valeurs["fb_comments_active"] != null ? $valeurs["fb_comments_active"] : $options["fb_comments_active_default"];
				
				$form->populate($valeurs);
			}
	  	
		}
		
		$this->view->form = $form;
		$this->view->backAcl = $backAcl;
	}
	
	public function deleteAction ()
	{
		$id = (int) $this->_request->getParam('id');
		
		if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		// Check permissions
		if(!$backAcl->hasPermission("mod_articles-".$id, "delete")) {
			_error(_t("Insufficient rights"));
			return $this->_redirect($this->_helper->route->full('admin'));
		}
		
		// Delete Article
		$document = Articles_Object_Article::getOne($id);
		$document->delete();
				
		// Delete Permissions
		$backAcl->deletePermissions("mod_articles-".$id);
		
		$page = CMS_Page_PersistentObject::getOneFromDB(array('type' => 'articles-article', 'content_id' => $id), null, null, "all");
		
		if(!$page) 
			_error(_t("Page object has not been deleted because it was not found"));
		else 
			$page->delete();
		
		_message(_t('Article deleted'));
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function enableAction ()
	{
		$id = (int) $this->_request->getParam('id');

		if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
			
		$article = new Articles_Object_Article($id, 'all');
		
		$this->sendEmailArticleValidate($article, Articles_Object_Article::STATUS_PUBLISH);
		
		$article->status = Articles_Object_Article::STATUS_PUBLISH;
		
		/**
		 * @todo: Les catégories doivent être appelées avant le save()
		 * Si ce n'est pas fait, "save()" considerera l'attribut comme vide ...
		 */
		$article->categories;
		$article->save();
		
		$page = CMS_Page_PersistentObject::getOneFromDB(array('type' => 'articles-article', 'content_id' => $id), null, null, "all");
				
		if(!$page)
			_error(_t("Page object has not been updated because it was not found"));
		else {
			$page->enable = 1;
			$page->save();
		}
	
		_message(_t('Article published'));
			
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function disableAction ()
	{
		$id = (int) $this->_request->getParam('id');
		
		if(!$id)
			throw new Zend_Exception(_t('Id is missing'));
		
		$article = new Articles_Object_Article($id, 'all');
		
		$article->status = Articles_Object_Article::STATUS_DRAFT;
		
		/**
		 * @todo: Les catégories doivent être appelées avant le save()
		 * Si ce n'est pas fait, "save()" considerera l'attribut comme vide ...
		 */
		$article->categories;
		$article->save();
		
		$page = CMS_Page_PersistentObject::getOneFromDB(array('type' => 'articles-article', 'content_id' => $id), null, null, "all");
	
		if(!$page)
			_error(_t("Page object has not been updated because it was not found"));
		else {
			$page->enable = 0;
			$page->save();
		}
		
		_message(_t('Article drafted'));

			
		return $this->_redirect($this->_helper->route->short('index'));
	}
		
	private function createCategory($redirect = true) {
		$this->_helper->layout()->setLayout('lightbox');
		
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission("mod_categories", "create")) {
			_error(_t("Insufficient rights"));
			$this->closeandredirect($this->_redirect($this->_helper->route->full('admin')));
		}
		$this->view->backAcl = $backAcl;
		
		$form = new Articles_Form_Categorie();
		
		if ($this->getRequest()->isPost() ) {
			if( $form->isValid($_POST) ) {
				
				$categorie = new Articles_Object_Categorie();
				
				$categorie->parent_id 								= ($form->getValue('parent')) ? $form->getValue('parent') : null;
				$categorie->title 										= $form->getValue('title');
				$categorie->level 										= 0;
				$categorie->countByPage 							= ((int)$form->getValue('countByPage')) ? $form->getValue('countByPage') : 5;
				$categorie->fb_comments_number_show = $form->getValue('fb_comments_number_show');
				$categorie->typeView 								= $form->getValue('typeView') ? $form->getValue('typeView') : null;
				$categorie->image 									= $form->getValue('image') ? reset($form->getValue('image')) : null;
				$categorie->description 								= $form->getValue('description');
				
				$id_categorie = $categorie->save();
				
				if($_POST['ACL'])
	            	$backAcl->addPermissionsFromAclForm("mod_categories-".$id_categorie, $_POST['ACL']);
				else 
					$backAcl->addPermissionsFromDefaultAcl("mod_categories-".$id_categorie, "mod_categories-default");
				
				/*
				 * Page
				 */
				$page = new CMS_Page_PersistentObject();
				
				$page->title 		= $categorie->title;
				$page->type 		= "articles-categorie";
				$page->content_id 	= $id_categorie;
				$page->url_system 	= $this->_helper->route->full('articles', array("module"=>"articles", "controller"=>"front", "action"=>"cat", "id"=>$id_categorie));
				$page->enable 		= 1;
				$page->save();
				
				if($redirect === true) {
					_message(_t('Category created'));
					$this->closeandredirect($this->_helper->route->short('index'));
				}
				else {
					$categorie->title = $categorie->title[CURRENT_LANG_ID];
					echo '<script language="javascript">parent.updateSelectCategories('.json_encode($categorie->toArray()).');</script>';
					$this->closeIframe();
				}
			}
			else {
				$form->populate($_POST);
			}
		}
		else {
			$options = json_decode(CMS_Application_Config::getInstance()->get("mod_categories-options"), true);
			$form->populate(array("fb_comments_number_show" => $options["fb_comments_number_show"]));
		}
		
		if( $redirect )
			$form->setAction($this->_helper->route->short('create-category'));
		else
			$form->setAction($this->_helper->route->short('create-category-in-article'));
		
		$this->view->form = $form;
		
		if($backAcl->hasPermission("mod_categories-default", "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_categories-default");
			$form->addSubForm($formAcl, "permissions");	
		
			$this->view->formAcl = $formAcl;
		}
	}
	
	public function createCategoryInArticleAction() {
		$this->createCategory(false);
		$this->view->content = $this->view->render('back/create-category.tpl');
	}
	
	public function createCategoryAction() {
		$this->createCategory(true);
	}
	
	public function editCategoryAction() {
		$this->_helper->layout()->setLayout('lightbox');
		
		$id = (int)$this->getRequest()->getParam('id');
		
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission("mod_categories-" . $id, "edit")) {
			_error(_t("Insufficient rights"));
			$this->closeandredirect($this->_redirect($this->_helper->route->full('admin')));
		}
		$this->view->backAcl = $backAcl;
		
		$form = new Articles_Form_Categorie();
		$categorie = new Articles_Object_Categorie($id, 'all');
		
		if ($this->getRequest()->isPost() ) {
			if( $form->isValid($_POST) ) {
				
				$categorie->parent_id 								= ($form->getValue('parent')) ? $form->getValue('parent') : null;
				$categorie->title 										= $form->getValue('title');
				$categorie->level 										= 0;
				$categorie->countByPage 							= ((int)$form->getValue('countByPage')) ? $form->getValue('countByPage') : 5;
				$categorie->fb_comments_number_show = $form->getValue('fb_comments_number_show');
				$categorie->typeView 								= $form->getValue('typeView') ? $form->getValue('typeView') : null;
				$categorie->image 									= $form->getValue('image') ? reset($form->getValue('image')) : null;
				$categorie->description 								= $form->getValue('description');
				 
				$id_categorie = $categorie->save();
				
				/*
				 * Page
				 */
				$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'articles-categorie', 'content_id' => $id_categorie), null, null, "all" );
				
				if(!$page)
					_error(_t("Page object has not been updated because it was not found"));
				else {
					$page->title = $categorie->title;
					$page->save();
				}
				
				/*
				 * Rights
				 */
				$backAcl->updatePermissionsFromAclForm("mod_categories-".$id, $_POST['ACL']);
				
				_message(_t('Category updated'));
				$this->closeandredirect($this->_helper->route->short('index'));
			}
			else {
				$form->populate($_POST);
			}
		}
		else {
			$form->populate(array(
				'parent' 	=> $categorie->parent_id,
				'title' 	=> $categorie->title,
				'countByPage' => $categorie->countByPage,
				'fb_comments_number_show' => $categorie->fb_comments_number_show,
				'typeView' => $categorie->typeView,
				'image' => $categorie->image,
				'description' => $categorie->description
			));
		}
		
		if($backAcl->hasPermission("mod_categories-" . $id, "manage"))
		{
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_categories-" . $id);
			$form->addSubForm($formAcl, "permissions");	
		
			$this->view->formAcl = $formAcl;
		}
		
		$form->setAction($this->_helper->route->short('edit-category', array('id' => $id)));
		$this->view->form = $form;
		$this->view->id_categorie = $id;
	}
	
	public function deleteCategoryAction() {
		$id = (int)$this->getRequest()->getParam('id');
		
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission("mod_categories-" . $id, "delete")) {
			_error(_t("Insufficient rights"));
			$this->closeandredirect($this->_redirect($this->_helper->route->full('admin')));
		}
		
		/*
		 * Category
		 */
		Articles_Object_Categorie::deleteFromPrimaryKey($id);
		
		/*
		 * Rights
		 */
		$backAcl = CMS_Acl_Back::getInstance();
		$backAcl->deletePermissions("mod_categories-".$id);
		
		/*
		 * Page
		 */
		$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'articles-categorie', 'content_id' => $id), null, null, "all" );
		
		if(!$page)
			_error(_t("Page object has not been deleted because it was not found"));
		else 
			$page->delete();
		
		_message(_t('Category deleted'));
		$this->_redirect($this->_helper->route->short('index'));
	}
	
	public function editOptionsArticleAction() {
		$this->_helper->layout()->setLayout('lightbox');
		
		$backAcl = CMS_Acl_Back::getInstance();
		if($backAcl->hasPermission("mod_articles", "manage")){
			
			$form = new Articles_Form_OptionsArticle();
			
			$config = CMS_Application_Config::getInstance();
			
			if($this->getRequest()->isPost()){
				if($form->isValid($_POST)){
						
					$config->set("mod_articles-options", json_encode($form->getValues()));
					
					$backAcl->updatePermissionsFromAclForm("mod_articles", $_POST['ACL']);
	
					_message(_t("Options updated"));
					
					return $this->closeandredirect($this->_helper->route->short('index'));
				}
			}
			else {
				$options = @json_decode($config->get("mod_articles-options"), true);
				
				if($options)
					$form->populate($options);
			
			}
			
			$form->setAction($this->_helper->route->short('edit-options-article'));
			$this->view->form = $form;
			
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_articles");
	    	$this->view->formAcl = $formAcl;
		}
		$this->view->backAcl = $backAcl;
	}
	
	public function editOptionsCategoryAction() {
		$this->_helper->layout()->setLayout('lightbox');
		
		$backAcl = CMS_Acl_Back::getInstance();
		if($backAcl->hasPermission("mod_categories", "manage")) {
			
			$form = new Articles_Form_OptionsCategory();
				
			$config = CMS_Application_Config::getInstance();
				
			if($this->getRequest()->isPost()){
				if($form->isValid($_POST)){
						
					$config->set("mod_categories-options", json_encode($form->getValues()));
						
					$backAcl->updatePermissionsFromAclForm("mod_categories", $_POST['ACL']);
			
					_message(_t("Options updated"));
						
					return $this->closeandredirect($this->_helper->route->short('index'));
				}
			}
			else {
				$options = json_decode($config->get("mod_categories-options"), true);
				
				if($options)
					$form->populate($options);
					
			}
				
			$form->setAction($this->_helper->route->short('edit-options-category'));
			$this->view->form = $form;
			
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_categories");
			$this->view->formAcl = $formAcl;
		}
	}
	
	/** Envoit d'email si membre du site qui à posté cet article **/
	private function sendEmailArticleValidate($document, $newStatus) {
		
		$author = new Users_Object_User((int)$document->author);
        
    	$options = @json_decode(CMS_Application_Config::getInstance()->get("mod_articles-options"), true);
	    
	    if( $options['notifyValidateArticle'] && $document->isSubmitted ) {
		    // Si middle office article présent, article créé par un membre, article passant d'un status inactif à actif
		    if( ($author->group->id == CMS_MIDDLE_USER_GROUPE) && ($document->status == 0) && ($newStatus == 1) ) {
		    	// alors on mail le créateur de l'article pour le prévenir de la publication de son article
		    	
		    	try {
					$mail = new Zend_Mail('UTF-8');
					
					// Récupération de Smarty
					$view = Zend_Layout::getMvcInstance()->getView();
					// Récupération du chemin actuel des templates
					$path = $view->getScriptPaths();
					// Changement de chemin des templates pour selectionner celui des mails
					$view->setScriptPath(realpath(dirname(__FILE__).'/../views/render/emails'));
					
					$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
					$url_view = BASE_URL.$helper->full('articles', array('controller' => 'front', 'action'=>"view", 'id' => $document->id_article));
					
					$view->assign('url', $url_view);
					$view->assign("article", $document);
				
					$content = $view->renderInnerTpl("notify-validate-article.tpl");
					
					$view->setScriptPath($path);
					
					$mail->setBodyHtml($content)
					->setFrom(EMAIL_FROM, EMAIL_SIGN)
					->addTo($author->email, $author->email)
					->setSubject('Validation de votre article ' . EMAIL_SIGN)
					->send();
			    }
    			catch(Exception $e){}
			}
		}
	}
	
	public function createRewriteAction() {
		$backAcl = CMS_Acl_Back::getInstance();
		if(!$backAcl->hasPermission("mod_categories", "manage"))
			throw new Zend_Exception(_t("Not enougt right"));
		
		$list_article = Articles_Object_Article::get(null, null, null, 'all');
		$sanitize = new CMS_Filter_Sanitize();
		
		foreach ($list_article as $article) {
			$modif = false;
			$page = CMS_Page_PersistentObject::getOneFromDB( array('content_id' => $article->id_article), null, null, "all" );
			foreach ($page->url_rewrite as $key => $rewrite) {
				if (!$rewrite) {
					$page->url_rewrite[$key] = 'article/'.$sanitize->filter($article->title[$key]);
					$modif = true;
				}
			}
			
			if ($modif)
				$page->save();
		}
		
		_message(_t("Rewrite générés."));
		$this->closeandredirect($this->_redirect($this->_helper->route->short('edit-options-article')));
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
}

