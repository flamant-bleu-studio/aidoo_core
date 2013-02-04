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

class CMS_Form_Element_SubmitCustom extends Zend_Form_Element
{
	public $helper = 'formSubmitCustom';

    public function __construct($field_name, $attributes = null) {

    	Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
    	
		parent::__construct($field_name, $attributes);
    }
    
    
    public function setLabel( $label)
    {
    	$this->setAttrib("label", $label);
    }
    
    
}