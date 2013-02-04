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

class CMS_Form_Helper_FormDualListBox extends Zend_View_Helper_FormElement {
    
	public function formDualListBox($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
    	
    	$html = '   <div>
						    <table>
						            <tr>
						                <td>
						                		<div class="dualSelect_add_on" >Tout les elements</div>
						                        <select id="box1View" multiple="multiple" style="height:150px;width:300px;"> ';
    	
    	if ($options) {
	    	foreach ($options as $key => $option) {
	    		if (!$value || !in_array($key , $value))
	    			$html .= '<option value="'.$key.'">'.$option.'</option>';
	    	}
    	}
													
		$html .=	                        '</select>  
						                </td>
						                <td style="text-align:center;">
						                    <button id="to2" type="button">&nbsp;>&nbsp;</button><br/><br/>
						                    <button id="allTo2" type="button">&nbsp;>>&nbsp;</button><br/>
						                    <button id="allTo1" type="button">&nbsp;<<&nbsp;</button><br/><br/>
						                    <button id="to1" type="button">&nbsp;<&nbsp;</button>
						                </td>
						                <td>
						                	<div class="dualSelect_add_on" >Elements selectionnés</div>
						                    <select id="box2View" name="'.$name.'[]" multiple="multiple" style="height:150px;width:300px;">';
		
		if ($value) {
			foreach ($value as $v) {
				if ($options && array_key_exists($v , $options))
				$html .= '<option value="'.$v.'">'.$options[$v].'</option>';
			}
		}
						                    
		$html .=	                    '</select>
						                </td>
						            </tr>
						        </table>
						    </div>
    	';
    	
 		return $html;
    }
}