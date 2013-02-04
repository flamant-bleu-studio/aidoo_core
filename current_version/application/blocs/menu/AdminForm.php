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

class Bloc_Menu_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$id_lst = Menu_Object_Menu::getAllMenuID();
		
		
		$displayGroup = array();
		$list_menus = array();
		
		if( count($id_lst) > 0 )
		{
			foreach ($id_lst as $menu_id) {
				
				$menu_object = new Menu_Object_Menu($menu_id);
				$menu_object->disableRenderNested();
				$menu_object->generate();
				
				$list_menus[$menu_id] = $menu_object->label;
				
				$el = new Zend_Form_Element_Select("folder_menu_".$menu_id);
				$el->setLabel(_t("Folder"));
				$el->setDescription(_t("From which folder"));
				$el->setRegisterInArrayValidator(false);
				$el->addMultiOption(0, _t("None"));
				
				foreach ($menu_object->items as $item) {
					if(in_array($item->type, Menu_Object_Item::$TYPE_FOLDER) ) {
						$el->addMultiOption($item->id_menu, str_repeat("-", $item->level). " " . $item->label);
					}
				}
				
				$this->addElement($el);
				$displayGroup[] = "folder_menu_".$menu_id;
			}
		}
		
		$this->addDisplayGroup($displayGroup, "test");
		
		$item = new Zend_Form_Element_Select("test_1");
		$item->setLabel("test");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("idMenu");
		$item->setLabel(_t("Menu"));
		$item->setDescription(_t("Select menu to display"));
		$item->addMultiOptions($list_menus);
		$this->addElement($item);
		
		$options = array();
		
		for( $i = 0 ; $i <= 10 ; $i++ )
		{
			if( $i != 0 )
				array_push($options, $i);
			else 
				array_push($options, _t("Unlimited"));
		}
		
		$item = new Zend_Form_Element_Checkbox("displayOnlyFolder");
		$item->setLabel(_t("Display only folder at level 1"));
		$item->setDescription(_t("Check to enable"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("levelDisplay");
		$item->setLabel(_t("Level"));
		$item->setDescription(_t("To which level"));
		$item->addMultiOptions($options);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("align");
		$item->setLabel(_t("Menu alignment"));
		$item->setDescription(_t("Select menu alignment"));
		$item->addMultiOptions(Bloc_Menu_Menu::$_align);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("desactiveDeroulant");
		$item->setLabel(_t("Disable drop down menu")); // Désactiver l'affichage par menu déroulant
		$item->setDescription(_t("Check to disable"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text("separator");
		$item->setLabel(_t("Separator"));
		$item->setLabel(_t("Separator character"));
		$this->addElement($item);
		
	}
}