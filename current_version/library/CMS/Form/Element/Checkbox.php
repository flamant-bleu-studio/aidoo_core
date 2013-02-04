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

class CMS_Form_Element_Checkbox extends Zend_Form_Element_Checkbox
{
	protected $_isTranslatable;
	
	public function __construct($spec, $options = null){
	
		$this->_isTranslatable = false;
	
		if($options["translatable"])
		$this->setTranslatable($options["translatable"]);
			
		parent::__construct($spec, $options);
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