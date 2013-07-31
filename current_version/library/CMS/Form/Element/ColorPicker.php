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

class CMS_Form_Element_ColorPicker extends Zend_Form_Element_Text
{
	public $helper = 'formColorPicker';
	private $_options;
	
    public function __construct($field_name, $attributes = null) {

    	$this->setAttrib("maxlength", "6");
    	$this->setAttrib("size", "6");
    	$this->addValidator(new CMS_Validate_ColorHexa());
    	
    	parent::__construct($field_name, $attributes);

    }
    
    public function render(Zend_View_Interface $view = null)
    {
    	Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
    		    	
    	$processLayout = CMS_Application_ProcessLayout::getInstance();

    	$processLayout->appendJsFile(COMMON_LIB_PATH. "/lib/colorPicker/js/colorpicker.js");
    	$processLayout->appendCssFile(COMMON_LIB_PATH. "/lib/colorPicker/css/colorpicker.css");
    	$processLayout->appendJsScript("
		
			$('#".$this->_name."').ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					$(el).ColorPickerHide().css('border-color', '#'+hex);
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				},
				onChange: function (hsb, hex, rgb, el) {
					$('#".$this->_name."').val(hex).css('border-color', '#' + hex);
				}
			})
			.on('keyup', function(){
				$(this).ColorPickerSetColor(this.value).css('border-color', '#'+this.value);
			});
		
		");
    	
    	return parent::render($view);
    }
       
}