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

class Bloc_Facebookv2_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new Zend_Form_Element_Text("width");
		$item->setLabel(_t("Width"));
		$item->setDescription(_t("Block width (px)"));
		$item->setValue("292");
		$item->setAttrib("size", "3");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text("height");
		$item->setLabel(_t("Height"));
		$item->setDescription(_t("Blcok height (px)"));
		$item->setValue("558");
		$item->setAttrib("size", "3");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("color");
		$item->setLabel(_t("Color"));
		$item->setDescription(_t("Block color"));
		$item->addMultiOptions(array("light" => "light", "dark" => "dark"));
		$item->setValue("light");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("faces");
		$item->setLabel(_t("Profile pictures ?"));
		$item->setDescription(_t("Display profile pictures ?"));
		$item->setValue(1);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_ColorPicker("colorBorder");
		$item->setlabel(_t("Border color"));
		$item->setDescription(_t("Block border color"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("stream");
		$item->setLabel(_t("Stream ?"));
		$item->setDescription(_t("Display profile stream"));
		$item->setValue(0);
		$this->addElement($item);
	}
}