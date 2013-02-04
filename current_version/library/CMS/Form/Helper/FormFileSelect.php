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

class CMS_Form_Helper_FormFileSelect extends Zend_View_Helper_FormElement {
    
    public function formFileSelect($name, $value = null, $attribs = null) {
    	
    	$extensions = "";
    	if( count($attribs["extensions"]) > 0 )
    	{
    		$extensions .= "";
    		foreach ($attribs["extensions"] as $extension)
    		{
    			$extensions .= $extension.",";
    		}
    	}

    	$baseUrl = Zend_Layout::getMvcInstance()->getView()->baseUrl();
        $html = '<input type="text" value="'.$value.'" name="'.$name.'" id="'.$name.'" readonly="true" '. $this->_htmlAttribs($attribs).' />
        		<input id="SelectFileBtnSelect" class="orange" type="button" value="Parcourir" onclick="mcFileManager.browse({extensions:\''.$extensions.'\', fields : \''.$name.'\', no_host : true, insert_filter : '.$name.'Function});" />
        		<input id="SelectFileBtnCancel" class="red" style="display:none;" type="button" value="Cancel" onclick="'.$name.'CancelFile();" />
        		';
        
        $html .= '
        <script language="javascript">

        	if(document.getElementById("'.$name.'").value != ""){
        		var file = {url:document.getElementById("'.$name.'").value};
        		'.$name.'SetFile(file);
        	}
    		function '.$name.'Function(file){
				'.$name.'SetFile(file);
			}
			function '.$name.'SetFile(file){
				document.getElementById("'.$name.'").value = file.url;
      			document.getElementById("SelectFileBtnSelect").style.display = "none";
    			document.getElementById("SelectFileBtnCancel").style.display = "inline";
				$("#'.$name.'").triggerHandler("'.$name.'_set", [file]);
			}
			function '.$name.'CancelFile(){
				document.getElementById("'.$name.'").value = "";
				document.getElementById("SelectFileBtnSelect").style.display = "inline";
				document.getElementById("SelectFileBtnCancel").style.display = "none";
    		}
		</script>
			';
        
        return $html;
    }
}