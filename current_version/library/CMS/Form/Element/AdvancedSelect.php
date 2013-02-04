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

class CMS_Form_Element_AdvancedSelect extends Zend_Form_Element_Select
{
	public $helper = 'formAdvancedSelect';
	private $_options;
	protected $_isTranslatable;
	
	public function __construct($field_name, $attributes = null, $options = null) {
	
		Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
		
		$attributes["params"]["text_no_results_match"] = 
			isset($options["text_no_results_match"]) 
				? $options["text_no_results_match"] 
				: _t("No results matched");
		
		$attributes["params"]["allow_single_deselect"] = 
			(isset($options["allow_single_deselect"]) && $options["allow_single_deselect"] === true)
				? true
				: false;
				
		$attributes["params"]["placeholder"] = 
			isset($options["placeholder"]) 
				? $options["placeholder"] 
				: _t("Choose");
				
				
		$attributes['data-placeholder'] = $attributes["params"]["placeholder"];
		
		$this->_options = $attributes["params"];
    	
		$this->_isTranslatable = false;
		
		if($options["translatable"])
		$this->setTranslatable($options["translatable"]);
		
		parent::__construct($field_name, $attributes);
	}
	
	public function render(Zend_View_Interface $view = null)
    {
		$processLayout = CMS_Application_ProcessLayout::getInstance();
		
		$processLayout->appendJsFile(COMMON_LIB_PATH."/lib/chosen/chosen.jquery.min.js");
    	$processLayout->appendCssFile(COMMON_LIB_PATH."/lib/chosen/chosen.css");
    	
    	$processLayout->appendJsScript("
    		
    		$('#".$this->_name."').chosen({
    			no_results_text: '" . $this->options["params"]["text_no_results_match"] . "',
    			allow_single_deselect: '" . $this->options["params"]["allow_single_deselect"] . "'
    		});
    		
    	");
    	
    	return parent::render($view);
	}
	
	public function setTranslatable($flag = true){
		$this->_isTranslatable = $flag;
		$this->setIsArray($flag);
			
		return $this;
	}
	
	public function isTranslatable(){
		return $this->_isTranslatable;
	}
	
	public function getValue($id_lang = null)
	{
		$value = parent::getValue();
	
		if($id_lang != null)
		return $value[$id_lang];
	
		return $value;
	}
}