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

class CMS_Acl_Back {

	private static $_instance;

	private $_acl;
	private $_user;
	
	private $_cacheAcl;

	/**
	 * Retrieve singleton instance
	 *
	 * @return CMS_Error_DisplayManager
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new CMS_Acl_Back();
		}
		return self::$_instance;
	}
	
	private function __construct(){

		$this->_acl = new Zend_Acl();
		$this->_cacheAcl = array();
		
		$groupModel = new Users_Model_DbTable_Group();
		$groups = $groupModel->getAllGroups();

		$this->_acl->addRole( new Zend_Acl_Role($groups[0]->id) );
		
		$parent = array();
		$parent[$groups[0]->level+1] = $groups[0]->id;
		
		unset($groups[0]);
		
		foreach($groups as $group)
		{
			$this->_acl->addRole( new Zend_Acl_Role($group->id), new Zend_Acl_Role($parent[($group->level)]) );
			$parent[($group->level + 1)] = $group->id;
		}
		
		$this->_user = Zend_Registry::get('user');

	}
	
	/**
	 * Check if loggued user have permissions on specific ressource
	 *
	 * @param string $permissionName Permission name to check
	 * @param string $mode Which mode loggued user want to access
	 * @return bool
	 */
	public function hasPermission($permissionName = null, $mode = null) {
		
		if(!$permissionName || !$mode)
			throw new Zend_Exception(_t("missing parameter"));
		
		$userGroupId = $this->_user->group->id;
		
		// SuperAdmin always ok
		if($userGroupId == 1)
			return true;
					
		$permissions = $this->getPermissions($permissionName);
		
		if(!$permissions)
			return false;
			
		if(!$this->_acl->has($permissionName))
			$this->_acl->addResource($permissionName);
		
		if(isset($permissions[$mode]))
		{
			foreach($permissions[$mode] as $group_id)
			{
				
				if($this->_acl->hasRole($group_id))
					$this->_acl->allow($group_id, $permissionName, $mode);
			}
		}
		
		return $this->_acl->hasRole($userGroupId) && 
						$this->_acl->has($permissionName) && 
						$this->_acl->isAllowed($userGroupId, $permissionName, $mode);

	}
	
	/**
	 * Update permissions from ACL post
	 *
	 * @param string $permission_name Permission name will be updated
	 * @param array $postAcl Acl post
	 * @throws Zend_Exception
	 */
	public function updatePermissionsFromAclForm($permission_name, $postAcl = null)
	{
		if($postAcl)
		{
			if($this->hasPermission($permission_name, "manage"))
			{
				$groupModel = new Users_Model_DbTable_Group();
				$groups = $groupModel->getAllGroups();
		
				$permissions = $this->getPermissions($permission_name);
				
				if(!$permissions)
					throw new Zend_Exception(_t("Permission name not match"));
					
				$parent = array();
				foreach ($permissions as $perm => $value)
				{
					$enfant = array();
		
					foreach($groups as $group)
					{
						if($postAcl[$perm.'-'.$group->id] == "on")
						{
							array_push($enfant, $group->id);
						}
					}
		
					$parent[$perm] = $enfant;
				}
		
				$this->updatePermissions($permission_name, json_encode($parent));
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Add permissions from ACL post
	 *
	 * @param string $permission_name Permission name will be created
	 * @param array $postAcl Acl post
	 * @throws Zend_Exception
	 */
	public function addPermissionsFromAclForm($permission_name, $postAcl)
	{
		$groupModel = new Users_Model_DbTable_Group();
		$groups = $groupModel->getAllGroups();

		$permissions = $this->getPermissions($postAcl["permission_name"]);

		if(!$permissions)
			throw new Zend_Exception(_t("Permission name posted not match"));
			
		$parent = array();
		foreach ($permissions as $perm => $value)
		{
			$enfant = array();

			foreach($groups as $group)
			{
				if($postAcl[$perm.'-'.$group->id] == "on")
				{
					array_push($enfant, $group->id);
				}
			}

			$parent[$perm] = $enfant;
		}

		$this->addPermissions($permission_name, json_encode($parent));
	}
	
	/**
	 * Add permissions from default ACL in database
	 *
	 * @param string $permission_name Permission name will be created
	 * @param string $defaultAclName Default ACL name to duplicate
	 * @throws Zend_Exception
	 */
	public function addPermissionsFromDefaultAcl($permission_name, $defaultAclName)
	{
		$permissions = $this->getPermissions($defaultAclName);

		if(!$permissions)
			throw new Zend_Exception(_t("Default ACL not exist"));
			
		$this->addPermissions($permission_name, json_encode($permissions));
	}
	
	/**
	 * Check if loggued user have power on specific group
	 *
	 * @param int $group_id Group id to check
	 * @return bool
	 */
	public function hasPowerOn($group_id)
	{
		// no power on SuperAdmin
		if($group_id == 1)
			return false;
			
		$userGroupId = $this->_user->group->id;

		if($this->_acl->inheritsRole($group_id, $userGroupId))
			return false;
		
		return true;
	}
	
	
	/**
	 * Get permissions of specific ressource
	 *
	 * @param string $resourceName Ressource name
	 * @return array Permission array
	 */
	public function getPermissions($resourceName){
		
		if(!array_key_exists($resourceName, $this->_cacheAcl))
		{
			global $multi_site_prefix;
			
			$db = Zend_Registry::get('db');
		    $results = $db->query("SELECT rights
									FROM ".$multi_site_prefix."permissions
									WHERE name = ?", array($resourceName));
		    
		    $result = $results->fetch();
		
		    if($result)
		    {
	    		$this->_cacheAcl[$resourceName] = $result['rights'];
	    		$rights = $result['rights'];
		    }
		    else
		    {
				// fixme: impossible poursuivre apres un simple test si renvoie une exception
		    	//throw new Zend_Exception(_t("Permission not found"));
				return false;
		    }
		}
		else 
		{
			$rights = $this->_cacheAcl[$resourceName];
		}
		
		return json_decode($rights, true);
	}
	
	/**
	 * Delete a permission
	 *
	 * @param string $name permission name
	 * @return void
	 */
	public function deletePermissions($name)
	{
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
	    $db->query("DELETE FROM ".$multi_site_prefix."permissions WHERE name = ?", array($name));
	}
	private function updatePermissions($resource_name, $privileges)
	{
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
	    $db->query("UPDATE ".$multi_site_prefix."permissions SET rights = ? WHERE name = ?", array($privileges, $resource_name));
	    
	    unset($this->_cacheAcl[$resource_name]);
	}
	private function addPermissions($resource_name, $privileges)
	{
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
	    $db->query("INSERT INTO ".$multi_site_prefix."permissions (name, rights) VALUES (?, ?)", array($resource_name, $privileges));
	}

}