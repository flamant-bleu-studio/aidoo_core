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

class CMS_Layout_Controller_Plugin_Layout extends Zend_Layout_Controller_Plugin_Layout
{	
	
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
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
    	$cache_id = 'layout-' . $url .'-' . CURRENT_LANG_CODE;
    	
    	// Réglage
    	$smarty = Zend_Layout::getMvcInstance()->getView()->getEngine();
    	$smarty->compile_id = $cache_id;
    	$smarty->cache_id 	= $cache_id;
    	
    	// Regénération permanente du layout, la page entière ne doit pas être mise en cache !
    	Zend_Layout::getMvcInstance()->getView()->getEngine()->cache_lifetime = 0;
    	
    	parent::postDispatch($request);
    }
}