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

class GalerieImage_Form_Galerie extends CMS_Form_Default
{
	private $_type;
	
	public function __construct($type)
	{
		if(!$type)
			throw new Zend_Exception("Impossible to load this form whitout 'type'");
		
		$this->_type = $type;
		parent::__construct();
	}
	
    public function init()
    {
		$datas = new Zend_Form_Element_Hidden("datas");
		$this->addElement($datas);
    	
		$title = new Zend_Form_Element_Text("title");
		$title->setLabel(_t('Title'));
		
		if($this->_type == "galerie")
			$title->setDescription(_t('Title of Galerie Photo'));
		elseif($this->_type == "diaporama")
			$title->setDescription(_t('Title of Diaporama'));
			
		$title->setRequired(true);
		$this->addElement($title);
		

		$diaporama_size = json_decode(DIAPORAMA_SIZE, true);

		$options = array();
		foreach ($diaporama_size as $key => $values)
			$options[$key] = $values["width"] . " x " . $values["height"];
			
		$item = new Zend_Form_Element_Select("size");
		$item->setLabel(_t("Size"));
		$item->setDescription(_t("width x height (px)"));
		$item->addMultiOptions($options);
		$this->addElement($item);
		
		$bg_color = new Zend_Form_Element_Text("bg_color");
		$bg_color->setLabel(_t('Background Color'));
		$bg_color->setDescription(_t('Choose Background Color'));
		$this->addElement($bg_color);
		
		$item = new Zend_Form_Element_Select("controls_position");
		$item->setLabel(_t('Position of the controls'));
		$item->addMultiOptions(array(
			"top" => _t("At top"),
			"bottom" => _t("At bottom")
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("controls_style");
		$item->setLabel(_t('Style of the controls'));
		$item->addMultiOptions(array(
			"0" => _t("Prev/Next"),
			"1" => _t("Thumbnails")
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("style");
		$item->setLabel(_t('Style'));
		$item->addMultiOptions(array(
			"0" => "Diaporama",
			"1" => "Mozaïque"
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("transition");
		$item->setLabel(_t('Transition style'));
		$item->addMultiOptions(array(
			"fade" => "Fading",
			"slide" => "Sliding"
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("autostart");
		$item->setLabel(_t('Auto start'));
		$item->setDescription(_t('Check to start automatically the slideshow'));
		$this->addElement($item);
		
		$ordre_image = new Zend_Form_Element_Hidden("ordre_image");
		$this->addElement($ordre_image);
		
		$save = new CMS_Form_Element_SubmitCustom('save');
		$save->setValue(_t('Save'));
		$save->setLabel(_t('Save'));
		$this->addElement($save);
		
		$savequit = new CMS_Form_Element_SubmitCustom("savequit");
		$savequit->setValue(_t('Save & Quit'));
		$savequit->setLabel(_t('Save & Quit'));
		$this->addElement($savequit);
    }
}