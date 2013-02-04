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

class CMS_Form_Decorator_Standard extends Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Marker_File_Interface
{
    public function render($content)
    {
        $element = $this->getElement();
        
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        
        if (null === $element->getView()) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $input     = $this->buildInput();
        $errors    = $this->buildErrors();
        $desc      = $this->buildDescription();
        
        if(($element->getType() == "Zend_Form_Element_Submit"))
        {
        	$class = " submit";
        }
        else
        {
        	$label = $this->buildLabel();
        }
        
        if(($element->getType() == "Zend_Form_Element_File"))
        {
        	$content = '';	
        }
        
        $name = $element->getName();
		

        $output = '<div class="form_line'.$class.'" id="form_'.$name.'">'
	        		. '<div class="form_text">'
		                . $label
		                . $desc
	                . '</div>'
	                . '<div class="form_elem">' . $input .'</div>'
	                . $errors
	                . '<div class="clear"></div>'
                . '</div>';
        
        
        switch($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }
    
    public function buildLabel()
    {
        $element = $this->getElement();
        $label = $element->getLabel();

       if (empty($label)) {
            return '';
        }
        
        if ($element->isRequired()) {
            $label .= '*';
        }
        
        return '<div class="form_label">'. $element->getView()->formLabel($element->getName(), $label) . '</div>';
    }

    public function buildInput()
    {
        $element = $this->getElement();
        $validationClass = " validate[";
        $validateRequired = false;
        
        if($element->isRequired())
        {
        	$validationClass .= "required";
        	$validateRequired = true;
        }
        $validators = $element->getValidators();

        if($validators)
        {
	        foreach($validators as $v)
	        {
	        	$classTest = get_class($v);
	        	
	        	switch ($classTest) 
	        	{
	        		case "Zend_Validate_EmailAddress" :
						$validationClass .= ",custom[email]";$validateRequired = true;break;
					case "Zend_Validate_PostCode" :
						$validationClass .= ",custom[postcode]";$validateRequired = true;break;
					case "CMS_Validate_Phone" :
						$validationClass .= ",custom[phone]";$validateRequired = true;break;
					case "CMS_Validate_ColorHexa" :
						$validationClass .= ",custom[colorHexa]";$validateRequired = true;break;
					case "Zend_Validate_Digits" :
						$validationClass .= ",custom[integer],min[0]";$validateRequired = true;break;
					default: break;
	        	}
	        }
        }
        
        $validationClass .= "]";
        
        if($validateRequired)
      	  $element->setAttrib("class", $element->getAttrib("class").$validationClass);
        
        $helper  = $element->helper;
        return $element->getView()->$helper(
            $element->getName(),
            $element->getValue(),
            $element->getAttribs(),
            $element->options
        );
    }

    public function buildErrors()
    {
        $element  = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return '<div class="form_error">' . $element->getView()->formErrors($messages) . '</div>';
    }

    public function buildDescription()
    {
        $element = $this->getElement();
        $desc    = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<div class="form_desc">' . $desc . '</div>';
    }
}