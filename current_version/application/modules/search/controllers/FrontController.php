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

class Search_FrontController extends Zend_Controller_Action
{
	public function searchAction()
	{
		/**
		 * Request
		 */
		if($this->_request->isPost())
			$this->_redirect($this->_helper->route->full('search', array('action' => 'search', 'query' => urlencode(htmlentities(strip_tags($_POST['search']))))));
		
		$keywords = urldecode($this->_request->getParam("query"));
		
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
		$result = CMS_Search_Front::getInstance()->search($keywords, $page, $perPage);
		
		$helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Route');
		
		$results = array();
		
		if (!empty($result['hits'])) {
			foreach($result['hits'] as $hit) {
				
				// Route front
				$route_front = json_decode($hit->{url_front}, true);
				$route_front['params']['lang'] = CURRENT_LANG_CODE;
				
				// Route back
				$route_back  = json_decode($hit->{url_back}, true);
				$route_back['params']['lang'] = CURRENT_LANG_CODE;
				
				$results[] = array(
					'type'				=> $hit->type,
					'title' 			=> $hit->{title.'_'.CURRENT_LANG_CODE},
					'url_front'			=> $helper->full($route_front['route'], $route_front['params']),
					'url_back'			=> $helper->full($route_back['route'], $route_back['params']),
					'description' 		=> $hit->{content.'_'.CURRENT_LANG_CODE},
					'picture' 			=> $hit->{picture.'_'.CURRENT_LANG_CODE} ? $hit->{picture.'_'.CURRENT_LANG_CODE} : null,
					'picture_folder' 	=> $hit->{picture_folder.'_'.CURRENT_LANG_CODE},
					'score' 			=> $hit->score // DEBUG
				);
			}
		}
		
		/**
		 * Pagination
		 */
		$paginator = new CMS_Application_Paginator();
		
		$paginator->setRouteParams(array(
			'route' 		=> 'search',
			'module' 		=> 'search',
			'controller' 	=> 'front',
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