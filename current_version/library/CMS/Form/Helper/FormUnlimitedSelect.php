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

class CMS_Form_Helper_FormUnlimitedSelect extends Zend_View_Helper_FormSelect {
    
    public function formUnlimitedSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
	    
		// Ne pas créer un multi selec (uniquement pour utiliser sa mécanique de validation)
    	unset($attribs['multiple']);
    	
    	$params = $attribs["params"];
    	unset($attribs["params"]);
    	
    	// Séparation des données nouvelles
    	$newValue = $value["new"];
   		unset($value["new"]);
    	
    	$html = "";
    	
    	$html .= parent::formSelect($name."[".$i."]", $v, $attribs, $options, $listsep);
    	
    	/*if($value){
    		$i = 0;
    		
	    	foreach($value as $v){
		    	$html .= "<div class='unlimitedSelectLine'>";
		    	$html .= parent::formSelect($name."[".$i."]", $v, $attribs, $options, $listsep);
		    	
		    	if($i != 0){
		    		$html .= "<button class='remove_crit'>".$params["textBtnDel"]."</button>";
		    	}
		    	
		    	$html .=  "</div>";
		    	$i++;
	    	}
    	}
    	else {
    		$html .= "<div class='unlimitedSelectLine'>";
	    	$html .= parent::formSelect($name."[0]", null, $attribs, $options, $listsep);
	    	$html .=  "</div>";
 	   		
   		}*/
    	
	    
    	if($newValue){
    		$i = 0;
	    	foreach($newValue as $v){
		    	$html .= "<div class='unlimitedSelectLine'>";
		    	$html .= "<input type='text' name='".$name."[new][".$i."]' value='".$v."' />";
		    	$html .= "<button class='remove_crit'>".$params["textBtnDel"]."</button>";
		    	$html .=  "</div>";
		    	$i++;
	    	}
    	}
		
		if($params["allowAdd"]){
			
			$html  .= '<br/><button id="'.$name.'_new">'.$params["textBtnNew"].'</button>';	
		}
	    
		//$html = parent::formSelect($name, $v, $attribs, $options, $listsep);
 		
 		return $html;
    }
    
}