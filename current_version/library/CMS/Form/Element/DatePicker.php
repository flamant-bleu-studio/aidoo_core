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

class CMS_Form_Element_DatePicker extends Zend_Form_Element_Text
{

	private $_options;
	
    public function __construct($field_name, $attributes = null) {
    	
    	$this->_options['showSecond'] = false;
		
        parent::__construct($field_name, $attributes);

    }
    
    public function render(Zend_View_Interface $view = null)
    {
    	$processLayout = CMS_Application_ProcessLayout::getInstance();
    	
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryPlugins/jquery.ui.datepicker-fr.js");
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryPlugins/jquery.ui.timepicker-addon.js");
    	
    	if($this->_options['showSecond'])
    	{
			$show = "showSecond: true,";
			$format = "hh:mm:ss";
    	}
		else 
		{
			$show = "";
			$format = "hh:mm";
		}	
			
		$processLayout->appendJsScript("
		
			$.datepicker.setDefaults($.datepicker.regional['fr']);
			$('#".$this->_name."').datetimepicker({
				".$show."
				timeFormat: '".$format."',
				dateFormat: 'dd/mm/yy',
				separator: ' - ',
				changeYear: true,
				changeMonth: true,
				yearRange: '1900:2012',
				defaultDate: '".$this->_value."'
			});
		
		");
    	
    	return parent::render($view);
    }
    
	public function showSecond($value)
    {
    	$this->_options['showSecond'] = $value;
    	
    }
    
    public function setValue($value)
    {
	    if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})/", $value)) 
			parent::setValue($this->_convertDateTimeUsToPicker($value));
		else
			parent::setValue($value);    		
    }
    
    private function _convertDateTimeUsToPicker($dateTime)
	{
		if($this->_options['showSecond'])
			$format = "d/m/Y - H:i:s";
		else 
			$format = "d/m/Y - H:i";
			
		return date($format,strtotime ($dateTime));
	}
    
}