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

class CMS_Form_Helper_FormImageSelect extends Zend_View_Helper_FormElement {
    
    public function formImageSelect($name, $value = null, $attribs = null) {
    	
   	 	if( count($attribs["extensions"]) > 0 )
    	{
    		$extensions = "";
    		foreach ($attribs["extensions"] as $extension)
    		{
    			$extensions .= $extension.",";
    		}
    	}
    	
		$baseUrl = Zend_Layout::getMvcInstance()->getView()->baseUrl();
        $html = '<input type="text" value="'.$value.'" name="'.$name.'" id="'.$name.'" readonly="true" '. $this->_htmlAttribs($attribs).' />
        		<input class="orange" type="button" value="Parcourir" onclick="mcImageManager.browse({extensions:\''.$extensions.'\', fields : \''.$name.'\', no_host : true, insert_filter : '.$name.'Function});" />
        		';
        
        $html .= '
				<span id="'.$name.'SelectImg" style="display:none;z-index:1000;" onMouseOver="document.getElementById(\''.$name.'CancelBtn\').style.display = \'inline\';" onMouseOut="document.getElementById(\''.$name.'CancelBtn\').style.display = \'none\';"><br />
					<img src="" id="'.$name.'_preview" />
					<input type="button" id="'.$name.'CancelBtn" style="display:none;width:14px; height:13px; position:absolute; margin-left:-16px; border:none; background: transparent url('.$baseUrl.'/images/del.gif) no-repeat center center; cursor:pointer;" onclick="'.$name.'CancelImage();" />
				</span>';
        
        $html .= '
        <script language="javascript">

        	if(document.getElementById("'.$name.'").value != ""){
        		var file = {url:document.getElementById("'.$name.'").value};
        		'.$name.'SetImage(file);
        	}
    		function '.$name.'Function(file){   		
				'.$name.'SetImage(file);
			}
			function '.$name.'SetImage(file){
        
		document.getElementById("'.$name.'").value = file.url;';
				if(!isset($attribs["disable_preview"]) || !$attribs["disable_preview"]){
					$html .= 'document.getElementById("'.$name.'_preview").src = file.url;
					document.getElementById("'.$name.'SelectImg").style.display = "inline";';
				}
				$html .= '$("#'.$name.'").triggerHandler("'.$name.'_set", [file]);
			}
			function '.$name.'CancelImage(){
				document.getElementById("'.$name.'").value = "";';
    			if(!isset($attribs["disable_preview"]) || !$attribs["disable_preview"]){
    				$html .= 'document.getElementById("'.$name.'_preview").src = "";';
    			}
				$html .= 'document.getElementById("'.$name.'SelectImg").style.display = "none";
    		}
		</script>
			';
        
        return $html;
    }
}