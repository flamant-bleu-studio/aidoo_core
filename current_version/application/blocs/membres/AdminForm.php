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

class Bloc_Membres_AdminForm extends CMS_Bloc_ParentForm
{
	
	public function init()
	{
		parent::init();
		
		$alignment = array(
			Bloc_Articles_Main::$MODE_HORIZONTAL	=> _t("Horizontal"),
			Bloc_Articles_Main::$MODE_VERTICAL 	=> _t("Vertical")
		);
		
		$item = new Zend_Form_Element_Select('imageFormat');
		$item->setLabel(_t('Images format'));
		
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
		
		
		$item = new Zend_Form_Element_Checkbox("showArchive");
		$item->setLabel(_t("Archive link"));
		$item->setDescription(_t("Check to active"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text("textArchive");
		$item->setLabel(_t("Text"));
		$item->setDescription(_t("Achive link text"));
		$item->setTranslatable(true);
		$this->addElement($item);
						
		$item = new Zend_Form_Element_Text("nb_article");
		$item->setLabel(_t("Number of article"));
		$item->setDescription(_t("Number of article to display"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("alignment");
		$item->setLabel(_t("Alignment"));
		$item->setDescription(_t("Article alignment"));
		$item->addMultiOptions($alignment);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("fromMode");
		$item->setLabel(_t("Selection mode"));
		$item->addMultiOptions(array(
			'category' => _t('From group'),
			'selection' => _t('From selection')		
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("group");
		
		$model = new Users_Model_DbTable_Group();
		$categories = $model->getAllGroups();
		
		foreach($categories as $cat) {
			$item->addMultiOption($cat->id, $cat->name);
		}
		
		$item->setLabel(_t("Category"));
		$item->setDescription(_t("Category to display"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("displayMode");
		$item->setLabel(_t("Presentation"));
		$item->setDescription(_t("Display mode"));
		$item->addMultiOptions(array(
			"0" => "Slide unique, aucun défilement",
			"1" => "Multi-slides, défilement par slide",
			"2" => "Multi-slides, défilement doux"
		));
		$this->addElement($item);
		
		// DISPLAY MODE SLIDE
		$item = new Zend_Form_Element_Checkbox("showPagination");
		$item->setLabel(_t("Display pagination"));
		$item->setDescription(_t("Check to active"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("autoStart");
		$item->setLabel(_t("Autostart"));
		$item->setDescription(_t("Check to active"));
		$this->addElement($item);

		$item = new Zend_Form_Element_Select("pagerPosition");
		$item->setLabel(_t("Position"));
		$item->setDescription(_t("Pagination position"));
		$item->addMultiOptions(array(
			"top" => "top",
			"bottom" => "bottom"
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("showArrow");
		$item->setLabel(_t("Display arrows controls"));
		$item->setDescription(_t("Check to active"));
		$this->addElement($item);
		
		// DISPLAY MODE TICKER
		$item = new Zend_Form_Element_Text("tickerSpeed");
		$item->setLabel(_t("Scroll speed"));
		$item->setDescription(_t("1 : faster, 5000 : lower"));
		$item->addValidator(new Zend_Validate_Between(array('min' => 1, 'max' => 5000)));
		$this->addElement($item);
		
		
		// Commun TICKER et SLIDE
		
		$item = new Zend_Form_Element_Text("nb_page");
		$item->setLabel(_t("Number of page"));
		$item->setDescription(_t("Max number of page to display"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("scrolling");
		$item->setLabel(_t("Scrolling"));
		$item->setDescription(_t("Article scrolling"));
		$item->addMultiOptions($alignment);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("stopHover");
		$item->setLabel(_t("Stop autoscroll on mouse hover"));
		$item->setDescription(_t("Check to active"));
		$this->addElement($item);
	}
}
