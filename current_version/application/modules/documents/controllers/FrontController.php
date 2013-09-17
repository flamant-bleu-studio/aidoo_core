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

class Documents_FrontController extends CMS_Controller_Action
{
	public function viewAction(){		
		$id = (int) $this->_request->getParam('id');
		
		// Récupération du document correpondant
		try {
			$doc = new Documents_Object_Document($id);
			
			if(!CMS_Acl_Front::getInstance()->hasPermission($doc->access) || $doc->status == Documents_Object_Document::STATUS_DRAFT)
				throw new Exception("NULL");
		}
		catch(Exception $e){
			throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
		}
		
	    $this->view->doc = $doc;
	    
    	$path = PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/documents/'.$doc->type.'/';
    	$this->view->initViewAndOverride($path, null, $doc->template);
    	
    	// Changement de la vue par celle du document
    	$this->_helper->viewRenderer->setRender($doc->template, null, true);
	}
	
}

