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

class CMS_Form_Helper_FormUnlimitedText extends Zend_View_Helper_FormText
{
	
	public function formUnlimitedText($name, $value = null, $attribs = null)
	{
		$html .= "<div class='unlimitedTextLineSample_".$name."' style='display:none;'>";
   		$html .= parent::formText($name, null, $attribs);
   		$html .= "</div>";
		
		if($value)
		{
    		$i = 0;
    		
	    	foreach($value as $v)
	    	{
		    	$html .= "<div class='unlimitedTextLine'>";
		    	$html .= parent::formText($name."[".$i."]", $v, $attribs);
		    	
		    	if($i != 0)
		    	{
		    		$html .= '<button class="remove_crit btn btn-danger btn-mini"> - </button>';
		    	}
		    	
		    	$html .=  "</div>";
		    	$i++;
	    	}
    	}
    	else
    	{
    		$html .= "<div class='unlimitedSelectLine'>";
	    	$html .= parent::formText($name."[0]", null, $attribs);
	    	$html .=  "</div>";
   		}
   		
   		
   		$html  .= '<button id="'.$name.'_new" class="btn btn-primary btn-mini"> + </button>';	
		
   		$processLayout = CMS_Application_ProcessLayout::getInstance();
    	$processLayout->appendJsScript('
			
			$("#'.$name.'_new").on("click", function(e){
				e.preventDefault();
				
				// Récupération du dernier input text "new"
				var last = $("#form_'.$name.' input:last");
				
				// Si il en existe un : on récupère var i et on incrémente
				if(last.length != 0){
					var i = last.attr("name").match(/\[([0-9]+)\]$/i);
					i = parseInt(i[1])+1;
				}
				else {
					var i = "0";
				}
				
				// Construction du nouvel input text
				var newText = $(".unlimitedTextLineSample_'.$name.' input").clone();
				
				newText.attr("name", "'.$name.'["+i+"]");
				
				// Ajout de cet input
				$("#'.$name.'_new").before("<div class=\'unlimitedTextLine\'>" + $("<div>").append(newText).html() + "<button class=\'remove_crit btn btn-danger btn-mini\'> - </button></div>");
				
			});
			
			$(".remove_crit").on("click", function(e){
				e.preventDefault();
				$(this).prev().remove();
				$(this).remove();
			}) 
		');   		
   		
		return $html;
	}
	
}