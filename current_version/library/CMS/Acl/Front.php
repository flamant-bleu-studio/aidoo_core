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

class CMS_Acl_Front {

	private static $_instance;
	private $_groupsViewAccess;
	private $_user;

	/**
	 * Retrieve singleton instance
	 *
	 * @return CMS_Error_DisplayManager
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new CMS_Acl_Front();
		}
		return self::$_instance;
	}
	
	private function __construct(){

		// TODO : mettre en cache la récupération en bdd
		$mdl = new CMS_Acl_DbTable_ViewAccess();
		$viewAccess = $mdl->getAllViewAccess();

		$groupsViewAccess = array();
		foreach($viewAccess as $e)
		{
			$groupsViewAccess[$e->id] = json_decode($e->groups);
		}

		$this->_groupsViewAccess = $groupsViewAccess;
		$this->_user = Zend_Registry::get('user');
		
	}
	
	public function hasPermission($viewAccess = null)
	{
		if($viewAccess === null)
			throw new Zend_Exception(_t('Missing parameter'));

		$group = $this->_user->group;

		if (@in_array($group->id, $this->_groupsViewAccess[$viewAccess]))
			return true;
		else
			return false;
	}
	
}