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

class CMS_Form_Element_Text extends Zend_Form_Element_Text {
	
	public 	$helper = 'formText';
	protected $_isTranslatable;
	private $_options;
		
	
	public function __construct($spec, $options = null, $attributes = null){
		Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
		
		$this->_isTranslatable = false;
		$this->_options = $attributes["params"];
		
		if(isset($options["translatable"]) && $options["translatable"])
			$this->setTranslatable($options["translatable"]);
			
		parent::__construct($spec, $options, $attributes);
	}
	
	public function render(Zend_View_Interface $view = null)
	{
		// Attribut pour la vue
		$this->setAttrib("options", $this->_options);
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
    
    public function setSuffix($flag = null){
    	$this->_options["suffix"] = $flag;
    }
    
    public function setPrefix($flag = null){
    	$this->_options["prefix"] = $flag;
    }
}