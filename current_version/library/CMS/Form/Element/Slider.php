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

class CMS_Form_Element_Slider extends Zend_Form_Element
{
	public $helper = 'formSlider';
	
	private $_options;
	
    public function __construct($field_name, $attributes = null) {
    	
    	Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
  
		// Defaults
    	$this->setStep(1);
    	$this->setLimits(0,2000);
    	
		parent::__construct($field_name, $attributes);
    }
    
	public function setLimits($min, $max){
    	$this->setAttrib("limit_min", $min);
    	$this->setAttrib("limit_max", $max);
	    $this->_options["limit_min"] = $min;	
	    $this->_options["limit_max"] = $max;
    }
    
    public function setValue($datas){
    	$this->setAttrib("min", $datas["min"]);
    	$this->setAttrib("max", $datas["max"]);

	   $this->_value = $datas;
    }
	public function setStep($step){
		$this->_options["step"] = $step;    
	}
	
	public function getValue() {
        return $this->_value;
    }
    
	public function isValid($value, $context = null) {
		
		if($value["min"] > $value["max"] || $value["min"] < $this->_options["limit_min"] || $value["max"] > $this->_options["limit_max"]){
			$this->_messages[] = _t("An error occurred : incoherent value");
			return false;
		}
		else
			$this->_value = array('min' => $value["min"], 'max' => $value["max"]);
       		return true;
     }
 
    
    public function render(Zend_View_Interface $view = null)
    {
    	$this->_value["min"] = ($this->_value["min"]) ? $this->_value["min"] : $this->_options["limit_min"] ;
    	$this->_value["max"] = ($this->_value["max"]) ? $this->_value["max"] : $this->_options["limit_max"] ;
    	
    	$processLayout = CMS_Application_ProcessLayout::getInstance();
		$processLayout->appendJsScript('
		
			$( "#slider-'.$this->_name.'" ).slider({
				range: true,
				min: '.$this->_options["limit_min"].',
				max: '.$this->_options["limit_max"].',
				values: ['.$this->_value["min"].', '.$this->_value["max"].'],
				step: '.$this->_options["step"].',
				stop: function( event, ui ) {
					$( "#'.$this->_name.'_min" ).val(ui.values[ 0 ]);
					$( "#'.$this->_name.'_max" ).val(ui.values[ 1 ]);
				},
				slide: function( event, ui ) {
					$( "#'.$this->_name.'_min_aff" ).text(ui.values[ 0 ]);
					$( "#'.$this->_name.'_max_aff" ).text(ui.values[ 1 ]);
				}
			});
	
		');
    	
    	return parent::render($view);
    }
       
}