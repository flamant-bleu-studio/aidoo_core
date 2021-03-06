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

class CMS_Form_Helper_FormText extends Zend_View_Helper_FormText
{
	
	public function formText($name, $value = null, $attribs = null)
	{
		$prefix = (isset($attribs["options"]["prefix"])) ? $attribs["options"]["prefix"] : null;
		$suffix = (isset($attribs["options"]["suffix"])) ? $attribs["options"]["suffix"] : null;
		
		$beforeInput 		= '';
		$afterInput 		= '';
		$input 				= parent::formText($name, $value, $attribs);
		
		if ($prefix || $suffix) {
			$beforeInput 	.= '<div class="input-prepend input-append">';
			$beforeInput 	.= $prefix ? '<span class="add-on">'.$prefix.'</span>' : '';
			
			$afterInput 	.= $suffix ? '<span class="add-on">'.$suffix.'</span>' : '';
			$afterInput		.= '</div>';
		} 
		
		$html = $beforeInput . $input . $afterInput;
		
		return $html;
	}
	
}