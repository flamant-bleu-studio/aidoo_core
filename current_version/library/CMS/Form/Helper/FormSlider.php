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

class CMS_Form_Helper_FormSlider extends Zend_View_Helper_FormElement {
    
    public function formSlider($name, $value, $attribs = null) {
		
    	$info = $this->_getInfo($name, $value, $attribs, $options);
    	extract($info); // name, value, attribs, options, listsep, disable
    	
    	$limit_min = $attribs["limit_min"];
    	$limit_max = $attribs["limit_max"];

		$html = '
		<div class="slider-label">De <span id="'.$name.'_min_aff">'.$value["min"].'</span> à <span id="'.$name.'_max_aff">'.$value["max"].'</span></div>
		<div class="slider" id="slider-'.$name.'"></div>
		<input type="hidden" id="'.$name.'_min" name="'.$name.'[min]" value="'.$value["min"].'" />
		<input type="hidden" id="'.$name.'_max" name="'.$name.'[max]" value="'.$value["max"].'" />
		<input type="hidden" id="'.$name.'_min_limit" name="'.$name.'[limit_min]" value="'.$limit_min.'" />
		<input type="hidden" id="'.$name.'_max_limit" name="'.$name.'[limit_max]" value="'.$limit_max.'" />';
		
		return $html;

    }
    
}