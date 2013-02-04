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

class Faq_FrontController extends Zend_Controller_Action
{
	public function indexAction() 
	{	
		$id = (int) $this->_request->getParam('id');
		$faq = new Faq_Object_Faq($id);
		
		if(!CMS_Acl_Front::getInstance()->hasPermission($faq->access)){
			
			if(defined('CMS_MIDDLE_LOGIN_PAGE'))
				$this->_redirect(CMS_MIDDLE_LOGIN_PAGE);
			else
				throw new Zend_Controller_Action_Exception(_t("Page not found"), 404);
		
		}
		if ($faq->nodes) {
			foreach ($faq->nodes as &$f) {
				$f->answer 		= nl2br($f->answer);
				$f->question 	= nl2br($f->question);
			}
		}
		
		$this->view->faqs = $faq;
	}
}

