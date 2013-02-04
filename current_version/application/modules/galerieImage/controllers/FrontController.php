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

class galerieImage_FrontController extends Zend_Controller_Action
{
	public function viewAction()
	{
		$id = (int) $this->_request->getParam('id');
		
		$galerie = new GalerieImage_Object_Galerie($id);
		
		if(!$galerie || $galerie->type != GalerieImage_Lib_Manager::ID_TYPE_GALERIE)
	    	throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
			
	    $maxHeight = 0;
	    $maxWidth = 0;
	    
	    if( count($galerie->nodes) > 0) {
		    foreach($galerie->nodes as &$image){
		    	
		    	$image->datas = json_decode($image->datas);
		    	
		    	if($image->datas->height > $maxHeight){
		    		$maxHeight = $image->datas->height;
		    	}
		    	if($image->datas->width > $maxWidth){
		    		$maxWidth = $image->datas->width;
		    	}
		    }
	    }
	    
	    $this->view->maxHeight = $maxHeight;
	    $this->view->maxWidth = $maxWidth;	    
		$this->view->galerie = $galerie;
		
		if($galerie->style == "0"){
			$tpl = "showbydiapo";
		}elseif($galerie->style == "1"){
			$tpl = "showbymoz";
		}
    	
    	Zend_Layout::getMvcInstance()->getView()->setScriptPath(realpath(dirname(__FILE__).'/../views/render'));
		Zend_Layout::getMvcInstance()->getView()->addScriptPath(PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/tpls_override/modules/galerieImage/render/');
		
		$this->_helper->viewRenderer->setRender($tpl, null, true);

	}	
}