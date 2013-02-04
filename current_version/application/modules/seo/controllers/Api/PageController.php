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

class Seo_Api_PageController extends CMS_Controller_Api{

	protected $_actionToken = array();
	protected static $_rights 	= array(	'delete' 	=> array('mod_seo' => 'delete'));
	
		
	public function indexAction() {}
	
	public function getAction() {}
	
	public function postAction() {}
	
	public function putAction() {}
	
	public function headAction() {}
	
	public function deleteAction()
	{
		$id = $this->_request->getParam('id');
		
		if (!$id)
			throw new Exception(_t('missing id'), 500);
		
		$page = new CMS_Page_PersistentObject((int)$id, 'all');
		
		if (!$page)
			throw new Exception(_t('No content'), 204);
		
 		$page->delete();
		
		$this->getResponse()->setHttpResponseCode(200); // Ok
		$this->view->id = $id;
	}
}
