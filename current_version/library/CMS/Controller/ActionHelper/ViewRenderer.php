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

class CMS_Controller_ActionHelper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
	protected $_cacheIsDisabled = false;
	protected $_suffixCacheId= '';
	
	public function setSuffixCacheId($suffix)
	{
		$this->_suffixCacheId = $suffix;
	}
	
	public function disableCache()
	{
		$this->_cacheIsDisabled = true;
	}
	
	public function enableCache()
	{
		$this->_cacheIsDisabled = false;
	}
	
	public function postDispatch()
	{
		if ($this->_shouldRender()) {
			
			/*
	    	 * 	Récupération de l'URL de la page pour générer un identifiant de cache Smarty
	    	 */
			
			// Si un core page existe, on récupère son url système pour éviter les URLs rewritées (à rallonge)
	    	try {
	    		$url = CMS_Page_Current::getInstance()->url_system;
	    	}
	    	catch (Exception $e){
	    		$url = $_SERVER['REQUEST_URI'];
	    	}
	    	
	    	// ID du cache du layout
	    	$cache_id = UNIQUE_ID . '-' . CURRENT_LANG_CODE . '-view-' . $url . ((!empty($this->_suffixCacheId)) ? '-' . $this->_suffixCacheId : '');
	    	
	    	// Réglage
	    	$smarty = Zend_Layout::getMvcInstance()->getView()->getEngine();
	    	
	    	if ($this->_cacheIsDisabled === false) {
	    		$smarty->compile_id = $cache_id;
	    		$smarty->cache_id 	= $cache_id;
	    	}
	    	else {
	    		$saveCaching 		= $smarty->getCaching();
	    		$saveCacheLifetime 	= $smarty->getCacheLifetime();
	    		
	    		$smarty->setCaching(Smarty::CACHING_OFF);
	    		$smarty->setCacheLifetime(0);
	    	}
	    	
			$this->render();
			
			if ($this->_cacheIsDisabled === false) {
	    		$smarty->setCaching($saveCaching);
	    		$smarty->setCacheLifetime($saveCacheLifetime);
	    	}
		}
	}
}
