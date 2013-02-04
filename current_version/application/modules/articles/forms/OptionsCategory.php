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

class Articles_Form_OptionsCategory extends CMS_Form_Default {
	
	public function init() {

		$item = new Zend_Form_Element_Select('imageFormat');
		$item->setLabel(_t('Images format'));
		$item->setDescription(_t("Images list articles"));

		$config = CMS_Application_Config::getInstance();
		$sizes = json_decode($config->get("configThumbSizes"), true);
		
		if($sizes && isset($sizes["default"])){
			foreach($sizes as $name => $size)
				$item->addMultiOption($name, " - " . $size["name"]. " (" . $size["width"] . "x" . $size["height"] . ")");
		}
		else {
			$item->addMultiOption("", _t("Default"));
		}
		
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('authorInArticle');
		$item->setLabel(_t('Show author'));
		$item->setDescription(_t("Display author in articles"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('fb_comments_number_show');
		$item->setLabel(_t('Show number comment'));
		$item->setDescription(_t('Display number comment to all articles'));
		$this->addElement($item);
	}
}