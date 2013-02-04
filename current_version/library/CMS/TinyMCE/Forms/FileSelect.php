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

class CMS_TinyMCE_Forms_FileSelect extends Zend_Form_Element 
{
    public $helper = 'formFileSelect';
	private $_options;	
	
    public function __construct($field_name, $attributes = null) {
    	
        if($attributes["extensions"])
        	$this->_options->extensions = $attributes["extensions"];
		
		Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/TinyMCE/ViewHelpers/', 'CMS_TinyMCE_ViewHelpers_');
		$processLayout = CMS_Application_ProcessLayout::getInstance();
		$processLayout->appendFileManager();
		
        parent::__construct($field_name, $attributes);
    }
    
    public function isValid($value, $context = null)
    {
        return parent::isValid($value, $context);
    }

}