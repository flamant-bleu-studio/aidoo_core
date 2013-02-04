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

class Blocs_Form_New extends CMS_Form_Default
{
	public function init()
	{
		$this->setAttrib("id", "new_form");

		$config = CMS_Application_Config::getInstance();
		$activeBloc = $config->get("activeBloc");
		$activeBloc = json_decode($activeBloc,true);
		
		$item = new Zend_Form_Element_Select('type_new');
		
		$item->addMultiOption("", "Choisir un type"); 
		$item->setRequired(true);
		
		$types = Blocs_Lib_Manager::getAllBlocXml();
		foreach ($types as $type) {
			if (in_array($type["name"], $activeBloc))
				$item->addMultiOption($type["type"], "- ".$type["name"]);
		} 
			
		$item->setLabel('');
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Submit("submit_new");
		$item->setValue(_t("Create"));
		$item->setLabel("");
		$this->addElement($item);

	}
}