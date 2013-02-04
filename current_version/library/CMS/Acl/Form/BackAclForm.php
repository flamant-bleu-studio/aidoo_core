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

class CMS_Acl_Form_BackAclForm extends Zend_Form
{
	public $permission_name;
	public $groups;
	public $modes;
	public $submit;
	
	public $baliseFormOpen;
	public $baliseFormClose;
	
	public function __construct($permission_name = null)
	{
		$this->permission_name = $permission_name;
		parent::__construct();
	}
	
	public function init()
	{
		$this->initGroups();
		$this->initModes();
		
		$this->getView()->addScriptPath(BASE_PATH.'/library/CMS/Acl/Form/');
		$this->setDecorators(array(
		    array('ViewScript', array('viewScript' => 'BackAclForm.tpl'))
		));
	}

	protected function initGroups()
	{
		$groupModel = new Users_Model_DbTable_Group();
		$groups = $groupModel->getAllGroups();
		
		$parent = array();
		$parent[$groups[0]->level+1] = $groups[0]->id;
		$return = array();
		foreach($groups as $group)
		{
			if($group->id == 1 || $group->id == 2)
				continue;
			
			$group->parent = $parent[($group->level)];
			$parent[($group->level + 1)] = $group->id;
			
			$n = '';
			for($i=0; $i < $group->level; $i++)
				$n .= '- ';

			$group->level = $n;
			
			$return[] = $group;
			
		}
		
		$this->groups = $return;
	}
	
	protected function initModes()
	{
		$backAcl = CMS_Acl_Back::getInstance();
		$permissions = $backAcl->getPermissions($this->permission_name);
		
		if($permissions)
		{
			$this->modes = array();
			
			foreach ($permissions as $name => $value)
			{
				$tmp = array();
				
				foreach($value as $g)
				{
					$tmp['_'.$g] = 1;
				}
	
				$this->modes[$name] = $tmp;
			}
		}
	}	
	
	public function addSubmit($label = "Submit")
    {
		$this->submit = '<input type="submit" id="submitAcl" class="btn btn-success" value="'.$label.'" />';
    }
}
