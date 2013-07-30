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

class CMS_Form_Element_AdvancedMultiSelect extends Zend_Form_Element_Multiselect
{
	public $helper = 'formAdvancedMultiSelect';
	private $_options;
	
	public function __construct($field_name, $attributes = array(), $options = null) {
	
		Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
		
		$attributes['params']['no_results_text'] 	= isset($options['no_results_text']) ? $options['no_results_text'] : _t('No results matched'); 
		$attributes['params']['placeholder'] 		= isset($options['placeholder']) ? $options['placeholder'] : _t('Choose');
				
		$attributes['data-placeholder'] = $attributes['params']['placeholder'];
		
		$this->_options = $attributes['params'];
    	
		parent::__construct($field_name, $attributes);
	}
	
	public function render(Zend_View_Interface $view = null)
    {
		$processLayout = CMS_Application_ProcessLayout::getInstance();
		
		$processLayout->appendJsFile(COMMON_LIB_PATH.'/lib/chosen/v0.14.0/chosen.jquery.min.js');
    	$processLayout->appendCssFile(COMMON_LIB_PATH.'/lib/chosen/v0.14.0/chosen.css');
    	
    	$processLayout->appendJsScript("
    		
    		$('#".$this->_name."').chosen({
    			no_results_text: '" . $this->_options['no_results_text'] . "'
    		});
    		
    	");
    	
    	return parent::render($view);
	}
}