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

class Articles_Form_OptionsArticle extends CMS_Form_Default {
	
	public function init() {
		
		$item = new Zend_Form_Element_Select('imageFormat');
		$item->setLabel(_t('Image format'));
		$item->setDescription(_t("Image article"));
		
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
		
		$item = new Zend_Form_Element_Checkbox('notifyNewArticle');
		$item->setLabel(_t('Notification new article'));
		$item->setDescription(_t("Notify by email of creating new article"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text("emailNotifyNewArticle");
		$item->setLabel(_t('Email'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('notifyValidateArticle');
		$item->setLabel(_t('Notification user if article validated'));
		$item->setDescription(_t("Notify user by email if article validate"));
		$this->addElement($item);
		
		/** DEBUT : AJAX **/
		$item = new Zend_Form_Element_Checkbox('ajaxEnable');
		$item->setLabel(_t('Ajax enable'));
		$item->setDescription(_t("Enable ajax pagination"));
		$this->addElement($item);
				
		$item = new Zend_Form_Element_Checkbox('ajaxNoScrollTop');
		$item->setLabel(_t('No Scroll top'));
		$item->setDescription(_t("Don't go on top when ajax"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('ajaxEffect');
		$item->setLabel(_t('Type effect'));
		$item->setDescription(_t("Effect during loading"));
		$item->addMultiOption("", _t("None"));
		$item->addMultiOption("slide", _t("Slide"));
		$item->addMultiOption("fade", _t("Fade"));
		$item->addMultiOption("explode", _t("Explode"));
		
		$this->addElement($item);
		
		/** FIN : AJAX **/
		
		/** DEBUT : Facebook Comment **/
		
		$item = new Zend_Form_Element_Checkbox('fb_comments_active_default');
		$item->setLabel(_t('Show comments (default value)'));
		$item->setDescription(_t("Display facebook comments in article"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('fb_comments_active');
		$item->setLabel(_t('Show comments'));
		$item->setDescription(_t("Display facebook comments in article"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text("fb_comments_width");
		$item->setLabel(_t('Width'));
		$item->setDescription(_t("With of the plugin, in pixel (px)"));
		$item->setValue('270');
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text("fb_comments_number");
		$item->setLabel(_t('Number of posts'));
		$item->setDescription(_t("Number of posts to display by default"));
		$item->setValue('2');
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("fb_comments_color");
		$item->setLabel(_t('Color Scheme'));
		$item->setDescription(_t("Color scheme of the plugin"));
		$item->addMultiOptions(array("light" => "light", "dark" => "dark"));
		$this->addElement($item);
		
		/** FIN : Facebook Comment **/
	}
}