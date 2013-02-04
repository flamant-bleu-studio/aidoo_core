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

class Bloc_Contactv3_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$listContact = Contact_Object_Contact::getEnable();
		foreach ($listContact as $contact){
			$contactType[$contact->type] = $contact->type;
		}	
		
		$item = new Zend_Form_Element_Select("type_form");
		$item->setLabel(_t("Choose type form contact"));
		$item->setDescription(_t("List type form contact available to bloc"));
		$item->setRequired(true);
		$item->addMultiOptions($contactType);
		$this->addElement($item);
	}
}