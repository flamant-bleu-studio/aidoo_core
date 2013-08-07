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

class Search_BackController extends CMS_Controller_Action
{
	public function indexAction()
	{
		$this->redirectIfNoRights('mod_searchengine', 'view');
		
		$backAcl = CMS_Acl_Back::getInstance();
		
		if($backAcl->hasPermission("mod_searchengine", "manage")) {
			$formAcl = new CMS_Acl_Form_BackAclForm("mod_searchengine");
			$formAcl->setAction(BASE_URL.$this->_helper->route->short('updateacl'));
			$formAcl->addSubmit(_t("Submit"));
			
			$this->view->formAcl = $formAcl;
		}
		
		$this->view->backAcl = $backAcl;
	}
	
	public function buildAction()
	{
		$this->redirectIfNoRights('mod_searchengine', 'view');
		
		// Vérification du support de l'UTF-8 sur le serveur
		if (@preg_match('/\pL/u', 'a') != 1) {
			_error('support UTF-8 pour PCRE désactivé');
			$this->_redirect($this->_helper->route->short('index'));
		}
		
		set_time_limit(0);
		
		/**
		 * Front
		 */
		$searchFront = CMS_Search_Front::getInstance();
		$searchFront->generateIndexTemp();
		$itemsFront = CMS_Application_Hook::getInstance()->apply_filters("regenerateSearchableContent", array(), 'front');
		$searchFront->addItems($itemsFront);
		$searchFront->finishIndexTemp();
		
		/**
		 * Back
		 */
		$searchBack = CMS_Search_Back::getInstance();
		$searchBack->generateIndexTemp();
		$itemsBack = CMS_Application_Hook::getInstance()->apply_filters("regenerateSearchableContent", array(), 'back');
		$searchBack->addItems($itemsBack);
		$searchBack->finishIndexTemp();
		
		_message(_t("Index updated"));
		
		return $this->_redirect( $this->_helper->route->short('index'));
	}
	
	public function updateaclAction()
	{
		if ($this->getRequest()->isPost()) {
			$backAcl = CMS_Acl_Back::getInstance();
			
			if($backAcl->updatePermissionsFromAclForm("mod_searchengine", $_POST['ACL']))
				_message(_t("Rights updated"));
			else
				_error(_t("Insufficient rights"));
		}
		
		return $this->_redirect($this->_helper->route->short('index'));
	}
	
	public function editOptionsAction() {
		$this->_helper->layout()->setLayout('lightbox');
		
		$this->redirectIfNoRights('mod_search', 'manage');
		
		$form = new Search_Form_Options();
		
		$config = CMS_Application_Config::getInstance();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$config->set('mod_search-options', json_encode($form->getValues()));
				_message(_t('Options updated'));
				return $this->closeFancybox($this->_helper->route->short('index'));
			}
		}
		else {
			$options = @json_decode($config->get('mod_search-options'), true);
			if($options)
				$form->populate($options);
		}
		
		$form->setAction($this->_helper->route->short('edit-options'));
		$this->view->form = $form;
		
		$formAcl = new CMS_Acl_Form_BackAclForm('mod_search');
	    $this->view->formAcl = $formAcl;
	}
	
	public function searchAction()
	{
		/**
		 * Request
		 */
		if($this->_request->isPost())
			$this->_redirect($this->_helper->route->full('search_query_back', array('module' => 'search', 'controller' => 'back', 'action' => 'search', 'query' => urlencode(htmlentities(strip_tags($_POST['search']))))));
		
		$keywords = html_entity_decode((urldecode($this->_request->getParam("query"))));
		
		if (!$keywords)
			throw new Exception(_t('Page not found'), 404);
		
		/**
		 * Config
		 */
		$config = CMS_Application_Config::getInstance();
		$options = json_decode($config->get('mod_search-options'), true);
		
		$perPage = (int)$options['resultByPage'] ? (int)$options['resultByPage'] : 10;
		$page = $this->_request->getParam('page');
		
		/**
		 * Result
		 */
		$result = CMS_Search_Back::getInstance()->search($keywords, $page, $perPage);
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		
		$results = array();
		
		if (!empty($result['hits'])) {
			foreach($result['hits'] as $hit) {
				
				$document = $hit->getDocument();
				$fields = $document->getFieldNames();
				
				$temp = array(
					'type'				=> $hit->type,
					'title' 			=> $hit->{title.'_'.CURRENT_LANG_CODE},
					'description' 		=> $hit->{content.'_'.CURRENT_LANG_CODE},
					'picture' 			=> $hit->{picture.'_'.CURRENT_LANG_CODE} ? $hit->{picture.'_'.CURRENT_LANG_CODE} : null,
					'picture_folder' 	=> $hit->{picture_folder.'_'.CURRENT_LANG_CODE},
					'isVisible' 		=> $hit->{isVisible.'_'.CURRENT_LANG_CODE},
					'typeName' 		=> $hit->{typeName.'_'.CURRENT_LANG_CODE},
					'score' 			=> $hit->score // DEBUG
				);
				
				// Route front
				if (in_array('url_front', $fields)) {
					$route_front = json_decode($hit->{url_front}, true);
					$route_front['params']['lang'] = CURRENT_LANG_CODE;
					
					$temp['url_front'] = $helper->full($route_front['route'], $route_front['params']);
				}
				
				// Route back
				if (in_array('url_back', $fields)) {
					$route_back  = json_decode($hit->{url_back}, true);
					$route_back['params']['lang'] = CURRENT_LANG_CODE;
					
					$temp['url_back'] = $helper->full($route_back['route'], $route_back['params']);
				}
				
				$results[] = $temp;
			}
		}
		
		/**
		 * Pagination
		 */
		$paginator = new CMS_Application_Paginator();
		$paginator->setAlignment('centered');
		$paginator->setSize('small');
		
		$paginator->setRouteParams(array(
			'route' 		=> 'search_query_back',
			'module' 		=> 'search',
			'controller' 	=> 'back',
			'action' 		=> 'search',
			'query'			=> $this->_request->getParam('query')
		));
		
		$paginator->nbItems = $result['total_count'];
		$paginator->byPage = $perPage;
		
		$this->view->pagination = $paginator->paginate();
		$this->view->results 	= $results;
		$this->view->keywords 	= $keywords;
		$this->view->options 	= $options;
	}
}

