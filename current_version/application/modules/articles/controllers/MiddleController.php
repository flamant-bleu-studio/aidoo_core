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

class Articles_MiddleController extends Zend_Controller_Action
{
	
	public function myArticlesAction() {
		
		$user_id = Zend_Registry::get('user')->id;
		
		$articles = Articles_Object_Article::get(array('author' => $user_id));
		
		$this->view->articles = $articles;
	}
	
	public function addArticleAction() {
		
		$form = Articles_Lib_Manager::createForm('classic');
		$form->setAction($this->_helper->route->short('add-article'));
		
		if( $this->getRequest()->isPost() ) {
			/*
			 * Set values required !
			 */
			$values = array();		
			$values['type'] 		= 'classic';
			$values['access'] 		= 1;
			$values['date_start'] 	= date("Y-m-d H:i:s", time());
			$values['date_end'] 	= date("Y-m-d H:i:s", time());
			
			$values = array_merge($_POST, $values);
			
			if( $form->isValid($values) ) {
				
				/*
				 * Article
				 */
				$article = new Articles_Object_Article();
				
				$article->author 	= Zend_Registry::get('user')->id;;
				
				$article->status 		= Articles_Object_Article::STATUS_DRAFT;
				$article->isSubmitted 	= 1;
				$article->access		= 1;
				
				$article->title  	= $form->getValue('title');
				$article->chapeau 	= $form->getValue('chapeau');
				$article->categories = $form->getValue('category');
				
				$article->type 		= 'classic';
				$article->template 	= $form->getValue('template');
				$article->image 	= reset($form->getValue('image'));
				
				$article->isPermanent 	= 1;
				$article->date_start 	= date("Y-m-d H:i:s", time());
				$article->date_end		= date("Y-m-d H:i:s", time());
				
	            $article->readmore 	= 1;
	            
				/*
				 * Nodes
				 */
				$nodesElements = $form->getDisplayGroup("readmore_elements")->getElements();
				if ($nodesElements){
		    		$nodes = array();
		    		// Pour chaque node
				    foreach ($nodesElements as $name => $el){
						$nodes[$name] = $el->getValue();
					}
					$article->nodes =  $nodes;
		    	}
		    	
		    	$id = $article->save();
		    	
		    	/*
		    	 * Rights
		    	 */
		    	$backAcl = CMS_Acl_Back::getInstance();
		    	$backAcl->addPermissionsFromDefaultAcl("mod_articles-".$id, "mod_articles-default");
		    	
		    	/*
		    	 * Page
		    	 */
		    	$sanitize = new CMS_Filter_Sanitize();
		    	$langs = json_decode(CMS_Application_Config::getInstance()->get("availableFrontLang"), true);
		    	
		    	$page = new CMS_Page_PersistentObject();
				$page->title 		= $article->title;
				$page->type 		= "articles-article";
				$page->content_id 	= $id;
				$page->url_system 	= $this->_helper->route->full('articles', array("module"=>"articles", "controller"=>"front", "action"=>"view", "id"=>$id));
				$page->enable 		= $article->status;
				foreach ($page->title as $lang_id => $title){
					$page->url_rewrite[$lang_id] = ($lang_id != DEFAULT_LANG_ID ? $langs[$lang_id].'/' : null).'article/'.$sanitize->filter($title);
				}
				$page->save();
				
				try {
					$this->sendAdminMail($article->id_article);
				}
				catch(Exception $e){}
				
				_message(_t('Successful creation of the article. This article must be validated.'));
		    	
				if ($_POST['submitandquit'])
			    	$this->_redirect($this->_helper->route->short('my-articles'));
			    
			    return $this->_redirect($this->_helper->route->short("edit-article", array('id'=>$id)));
			}
			else {
				$form->populate($values);
			}
		}
		
		$form->getElement("content")->setAttrib("class", "mceEditorMiddle");
		
		$this->view->form = $form;
	}
	
	public function editArticleAction() {
		$id = (int) $this->_request->getParam('id');
		
		$article = new Articles_Object_Article($id, "all");
		
		if( $article->author != ($user_id = Zend_Registry::get('user')->id))
			throw new Exception(_t('You are not the owner of this article'));
		
		$form = Articles_Lib_Manager::createForm('classic');
		$form->setAction($this->_helper->route->short('edit-article', array('id' => $id)));
		
		$values = array();
		$values['type'] 		= $article->type;
		$values['access'] 		= $article->access;
		$values['date_start'] 	= $article->date_start;
		$values['date_end'] 	= $article->date_end;
		
		$values = array_merge($_POST, $values);
		
		if( $this->getRequest()->isPost() ) {
			if( $form->isValid($values) ) {
				
				$article->template 		= $form->getValue('template');
	            $article->status 		= Articles_Object_Article::STATUS_DRAFT;
	            $article->isSubmitted 	= 1;
	          	$article->title 		= $form->getValue('title');
			    $article->chapeau 		= $form->getValue('chapeau');				
				$article->image 		= reset($form->getValue('image'));
				$article->setCategories($form->getValue('category'));
				
				// Traitement des nodes
				$nodesElements = $form->getDisplayGroup("readmore_elements")->getElements();
	
				if ($nodesElements){
		    		
		    		$nodes = array();
					
		    		// Pour chaque node
				    foreach ($nodesElements as $name => $el){
						$nodes[$name] = $el->getValue();
					}
					
					$article->nodes =  $nodes;
		    	}
				
	            // Enregistrement
	            $article->save();
	            
	            _message(_t("Article updated"));
	            
	            /*
	             * Page
	             */
				$page = CMS_Page_PersistentObject::getOneFromDB( array('type' => 'articles-article', 'content_id' => $id), null, null, "all" );
				
				if(!$page)
					_error(_t("Page object has not been updated because it was not found"));
				else {
					$page->title = $article->title;
					$page->enable = $article->status;
					$page->save();
				}
				
				try {
					$this->sendAdminMail($article->id_article, false);
				}
				catch(Exception $e){}
				
				if ($_POST['submitandquit'])
			    	$this->_redirect($this->_helper->route->short('my-articles'));
			    
			    return $this->_redirect($this->_helper->route->short("edit-article", array('id'=>$id)));
			}
			else {
				$form->populate($_POST);
			}
		}
		else {
			$valeurs = $article->toArray();
			
			unset($valeurs['nodes']['template']);
			foreach ( $valeurs['nodes'] as $key => $value){
				$valeurs[$key] = $value;
			}
			
			$categories = $article->getCategories();
			if( $categories ) {
				foreach ($categories as $categorie) {
					$valeurs['category'][] = $categorie->id_categorie;
				}
			}
			
			// MultiUpload s'attend à un tableau de valeurs
			$valeurs["image"] = array($valeurs["image"]);
			
			$form->populate($valeurs);
		}
		
		$form->getElement("content")->setAttrib("class", "mceEditorMiddle");
		
		$this->view->form = $form;
	}
	
	public function sendAdminMail($id_article, $newArticle = true){
		
		$config = CMS_Application_Config::getInstance();
		$options = @json_decode($config->get("mod_articles-options"), true);
		
		$id_article = (int)$id_article;
		
		if(!$options['notifyNewArticle'] || !$options['emailNotifyNewArticle'] || !$id_article)
			return;
		
		$article = new Articles_Object_Article($id_article);
		
		$mail = new Zend_Mail('UTF-8');
		
		// Récupération de Smarty
		$view = Zend_Layout::getMvcInstance()->getView();
		// Récupération du chemin actuel des templates
		$path = $view->getScriptPaths();
		// Changement de chemin des templates pour selectionner celui des mails
		$view->setScriptPath(realpath(dirname(__FILE__).'/../views/render/emails'));
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		$url = $helper->full('articles_back', array('action'=>"edit", 'id' => $id_article));
		
		$view->assign("urlAdminArticle", $url);
		$view->assign("article", $article);
		
		// Génération du html
		
		if($newArticle === true){
			$content = $view->renderInnerTpl("notify-new-article.tpl");
			$subject = 'Nouvel article';
		}
		else {
			$content = $view->renderInnerTpl("notify-edit-article.tpl");
			$subject = 'Article modifié';
		}
		
		// Remise du chemin des templates d'origines
		$view->setScriptPath($path);
			
		$mail->setBodyHtml($content)
		->setFrom(EMAIL_FROM, EMAIL_SIGN)
		->addTo($options['emailNotifyNewArticle'], $options['emailNotifyNewArticle'])
		->setSubject($subject . ' ' . EMAIL_SIGN)
		->send();
		
	}
	
}