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

class Users_Form_Export extends CMS_Form_Default {
	
	public function init() {
		
		$groups = Users_Lib_Manager::getAllGroups();

		$item = new CMS_Form_Element_AdvancedMultiSelect('groupList');
		$item->setLabel(_t('Groups'));
		$item->setDescription(_t('Select groups to export'));
		$item->addMultiOption(1, "SuperAdmin");
		
		foreach($groups as $group)
			if($group->name != "Public")
				$item->addMultiOption($group->id, $group->name);
		
		$this->addElement($item);
	}
}