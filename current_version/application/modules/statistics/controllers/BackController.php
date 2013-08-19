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

class Statistics_BackController extends CMS_Controller_Action
{
	public function indexAction()
	{
		$this->redirectIfNoRights('mod_statistics', 'view');
		
		pre_dump(Statistics_Lib_Manage::getToday());
		pre_dump(Statistics_Lib_Manage::getLastMonth());
		die;
	}
	
	public function permissionsAction()
	{
		$this->redirectIfNoRights('mod_statistics', 'manage');
		
		$backAcl = CMS_Acl_Back::getInstance();
		$formAcl = new CMS_Acl_Form_BackAclForm('mod_statistics');
		
		if ($this->getRequest()->isPost() && $formAcl->isValid($_POST)) {
			$backAcl->updatePermissionsFromAclForm('mod_statistics', $_POST['ACL']);
			$this->_redirectCurrentPage();
		}
		
		$this->view->formAcl = $formAcl;
	}
}