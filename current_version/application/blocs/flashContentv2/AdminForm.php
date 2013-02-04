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

class Bloc_FlashContentv2_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new Zend_Form_Element_Text("version");
		$item->setLabel(_t("Adobe Flash version"));
		$item->setDescription(_t("Minimal version required"));
		$item->setRequired(true);
		$item->setValue("8");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text("width");
		$item->setLabel(_t("Width"));
		$item->setDescription(_t("swf width (px)"));
		$item->setRequired(true);
		$item->setValue("200");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text("height");
		$item->setLabel(_t("Height"));
		$item->setDescription(_t("swf height (px)"));
		$item->setRequired(true);
		$item->setValue("200");
		$this->addElement($item);
		
		$item = new CMS_Form_Element_FileSelect("swf", array("extensions" => array("swf")));
		$item->setLabel(_t("File"));
		$item->setDescription(_t("Select swf file"));
		$item->setRequired(true);
		$this->addElement($item);
		
		/*$item = new Zend_Form_Element_Textarea("content");
		$item->setLabel(_t("flashvars (javascript syntax)"));
		$item->setDescription(_t("Flash vars"));
		$item->setAttrib("rows", "8");
		$item->setValue("flashvars.xml=".BASE_URL."/public/upload/config.xml");
		$this->addElement($item);*/
	}
}