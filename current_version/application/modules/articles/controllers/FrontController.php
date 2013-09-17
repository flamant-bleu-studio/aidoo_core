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

class Articles_FrontController extends CMS_Controller_Action
{
	protected $_options;
	
	public function init() {
		
		$actionName = $this->getRequest()->getActionName();
		
		/**
		 * Récupération des options articles et catégories
		 */
		if( $actionName == 'view' || $actionName == 'cat' ) {
			$config = CMS_Application_Config::getInstance();
			
			if( $actionName == 'view' ) {
	    		$this->_options = json_decode($config->get('mod_articles-options'), true);
	   		}
			else if( $actionName == 'cat' ) {
				$this->_options = json_decode($config->get('mod_categories-options'), true);
			}
		}
		
		parent::init();
	}
	
	public function indexAction() {	
		$this->view->articles = Articles_Object_Article::get(
			array( 'status' => Articles_Object_Article::STATUS_PUBLISH ),
			array( 'date_start' )
		);
	}
	
	public function catAction() {
		
		$id = (int)$this->_request->getParam('id');
		
		$paginator = new CMS_Application_Paginator();
		
		/* bloc de traitement si fragment HTML (chargement des pages en ajax */
		$config = CMS_Application_Config::getInstance();
		$configArticle = json_decode($config->get("mod_articles-options"),true);
		
		if ($configArticle['ajaxEnable']) {
			$paginator->addTagInLink(array(	'data-type' 		=> 'content', 
																	'data-animate' => $configArticle['ajaxEffect'],
																	'data-noscroll'	=> $configArticle['ajaxNoScrollTop']));
			$paginator->addClassInLink('fragment');
		}	
		
		$cat = new Articles_Object_Categorie($id);
		
		/* AFFICHAGE CLASSIC */
		if (!$cat->typeView) {
			
			$paginator->setRouteParams(array(
				'route' => 'articles',
				'module' => 'articles',
				'controller' => 'front',
				'action' => 'cat',
				'id' => $id
			));
			
			$where = array(
				'categories' => $id, 
				'status' 	=> Articles_Object_Article::STATUS_PUBLISH,
				array('isPermanent = ? AND date_start < ? OR date_start < ? AND date_end > ?', 1, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'))
			);	
			
			$paginator->nbItems = Articles_Object_Article::count($where);
			$paginator->byPage = ((int)$cat->countByPage) ? (int)$cat->countByPage : 5;
			
			$this->view->pagination = $paginator->paginate();
			
			$limit = array(
				'offset' 	=> $paginator->getFromLimit(),
				'limit' 	=> $paginator->byPage
			);
			
			$contents = Articles_Object_Article::get($where, array('date_start' => 'DESC'), $limit);
			
			$this->view->articles = $contents;
			
			if( $this->_options['authorInArticle'] ) {
				$author = array();
				if( $contents ) {
					foreach($contents as $c){
						if(!in_array($c->author, $author))
							$author[] = (int)$c->author;
					}
				}
				
				if(!empty($author)){
					$authors = array();
					$author = Users_Object_User::get(array('id' => $author));
					foreach($author as $a){
						$authors[$a->id] = $a;
					}
					
					$this->view->authors = $authors;
				}
			}
			
			if( $cat->fb_comments_number_show ) {
				if( $contents ) {
					$this->view->fb_comments_number_show = true;
				}
			}
			/* AFFICHAGE EN GRID */
		} else if ($cat->typeView == 1) {
			$catsGrid = Articles_Object_Categorie::get(array('parent_id' => $cat->id_categorie));
			
			// Si cette catégorie n'a pas de catégorie enfant on affichage en grid les articles
			if (!$catsGrid) {
				$where = array(
								'categories' => $cat->id_categorie, 
								'status' 	=> Articles_Object_Article::STATUS_PUBLISH,
								array('isPermanent = ? AND date_start < ? OR date_start < ? AND date_end > ?', 1, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s')));
				$articlesGrid = Articles_Object_Article::get($where, array('date_start' => 'DESC'));
				$this->view->articlesGrid = $articlesGrid;
			}
			
			$this->view->catParent = $cat;
			$this->view->catsGrid = $catsGrid;
			$this->_helper->viewRenderer->setRender('cat-grid');
		}
		
		$this->view->category = $cat;
		
		$this->view->size = ((isset($this->_options['imageFormat'])) ? $this->_options['imageFormat'] : '');
	}
	
	public function viewAction()
	{
		$id = (int) $this->_request->getParam('id');
		
		if( !$id ) 
			throw new Zend_Controller_Action_Exception(_t('Page not found'), 404);
		
		try {
			$doc = new Articles_Object_Article($id);
		}
		catch (Exception $e) {
			throw new Zend_Controller_Action_Exception(_t('Page not found'), 404);
		}
		
		if(!CMS_Acl_Front::getInstance()->hasPermission($doc->access) || $doc->status == Articles_Object_Article::STATUS_DRAFT)
	    	throw new Zend_Controller_Action_Exception(_t('Page not found'), 404);
		
	    /**
	     * Nombre de commentaire facebook + paramètres d'affichage (si activé)
	    */ 
	    if( $doc->fb_comments_active && $this->_options['fb_comments_active'] ) {
	    	$this->view->fb_comments_color 	= $this->_options['fb_comments_color'];
	    	$this->view->fb_comments_number = $this->_options['fb_comments_number'];
	    	$this->view->fb_comments_width 	= $this->_options['fb_comments_width'];
	    	$this->view->fb_comments_active = $this->_options['fb_comments_active'];
	    }
	    
		
		$path = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/articles/'.$doc->type.'/';    	
		$this->view->initViewAndOverride($path, null, $doc->template);
		
    	$this->_helper->viewRenderer->setRender($doc->template, null, true);
    	$this->view->doc = $doc;
  		
    	if($this->_options['authorInArticle']){
    		$this->view->author = new Users_Object_User($doc->author);
    	}
    	
    	$this->view->size = ((isset($this->_options['imageFormat'])) ? $this->_options['imageFormat'] : '');
    	
    	$config = CMS_Application_Config::getInstance();
    	$configSocial = json_decode($config->get('social'),true);
    	
    	// Ajout des métas FB
    	$append = CMS_Application_ProcessLayout::getInstance();
    	$append->appendHeadContent('
    		<meta property="og:type" content="article" />
    		<meta property="og:site_name" content="'.$configSocial['sitename'].'"/>
    		<meta property="og:title" content="'.$doc->title.'" />
			<meta property="og:description" content="'.$doc->chapeau.'" />
			<meta property="og:image" content="http://'.$_SERVER['SERVER_NAME'].CMS_Image::getLink('articles', $doc->image).'" />
       	');
    	
	}
	
	public function migrationAction() {
		$articles = Articles_Object_Article::get(null,null,null,'all');
		
		foreach ($articles as $article) {
			$article->categories;
			foreach ($article->chapeau as $key => $chapeau)
				$article->chapeau[$key] = strip_tags($chapeau);
			
			$article->save();
		}
	}
}

