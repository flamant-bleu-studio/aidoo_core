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

class CMS_Form_Decorator_Checkbox extends CMS_Form_Decorator_Standard {
	
    public function render($content)
    {
        $element = $this->getElement();
        
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        
        if (null === $element->getView()) {
            return $content;
        }

        $separator 		= $this->getSeparator();
        $placement 		= $this->getPlacement();
        $input     		= $this->buildInput();
        $errors    		= $this->buildErrors();
        $label 			= $this->buildLabel();
        $description 	= $element->getDescription();
        $name 			= $element->getName();
		
        	
        $output = '<div class="form_line" id="form_'.$name.'">'
		        	. '<div class="form_text_checkbox">'
			        	. $label
		            . '</div>'
		            . '<div class="form_elem_checkbox">'
		                . $input
		                .'<label class="form_label_checkbox" for="'.$name.'">'.$description.'</label>'
		            .'</div>'
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
    
}