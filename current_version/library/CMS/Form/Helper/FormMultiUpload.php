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

class CMS_Form_Helper_FormMultiUpload extends Zend_View_Helper_FormElement
{
	
	public function formMultiUpload($name, $value = null, $attribs = null)
	{

		if($attribs["options"]["uploadPath"][0] != "/")
				$attribs["options"]["uploadPath"] = "/".$attribs["options"]["uploadPath"];
				
		$upload_path 	= PUBLIC_PATH . MULTIUPLOAD_FOLDER . $attribs["options"]["uploadPath"];
		$upload_url 	= BASE_URL . MULTIUPLOAD_FOLDER . $attribs["options"]["uploadPath"];
		
		$html = ' <div class="multiUpload">
		
		<div id="'.$name.'_hidden_data" style="display:none;">';

		$countItem = 0;
		if($value){
			$countItem = 0;
			foreach($value as $file){
				if($file){
					$countItem++;
					$html .= '<input type="hidden" name="'.$name.'['.$countItem.']" value="'.$file.'" id="'.$name.'_'.$countItem.'" />';
				}
			}
		}
			
		$html .= '</div>
		
		<div class="fileupload-buttonbar">
            <div>

            	<span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>'._t("Add files").'</span>
                    
                    <input id="'.$name.'" type="file" name="'.$name.'[]" multiple>
                    
                </span>
                
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>'._t("Start upload").'</span>
                </button>
                
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>'._t("Cancel").'</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>'._t("Delete").'</span>
                </button>
               <input type="checkbox" class="toggle">
            </div>
            
            <div>
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active fade">
                    <div class="bar" style="width:0%;"></div>
                </div>
            </div>
        </div>
        
        <!-- The loading indicator is shown during image processing -->
        <div class="fileupload-loading"></div>
        
        <!-- The table listing the files available for upload/download -->
        <table class="table table-striped fileupload-files">
        	<tbody class="files '. (($attribs["options"]["sortable"]) ? "sortable" : "") .'" data-toggle="modal-gallery" data-target="#modal-gallery">
        	';
        	
			if($value){
				$countItem = 0;
				foreach($value as $file){
					if($file){
						
						if(file_exists($upload_path."/".$file)){
							$size = CMS_Application_Tools::formatSize(filesize($upload_path."/".$file));
						}
						else {
							$size = '';
						}
						
						$countItem++;
						$html .= '<tr class="template-download" data-index="'.$countItem.'" >
			                        <td class="preview"><a href="'. CMS_Image::getLink($attribs["options"]["uploadPath"], $file) .'" rel="img-group"><img src="' . CMS_Image::getLink($attribs["options"]["uploadPath"], $file, 'default') . '" /></a></td>
			                        <td class="name"><a href="'. CMS_Image::getLink($attribs["options"]["uploadPath"], $file) .'">'.$file.'</a></td>
			                        <td class="size">'.$size.'</td>
			                        <td colspan="2"></td>
			                        <td class="delete"><button type="button" class="btn btn-danger"><i class="icon-trash icon-white"></i><span>'._t("Delete").'</span></button>
			                        <input type="checkbox" name="delete" value="1"></td>
		                        </tr>';
					}
				}
			}
		
		$html .='
        	</tbody>
        </table>




<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="'.BASE_URL.'/lib/jqueryFileUpload/js/cors/jquery.xdr-transport.js"></script><![endif]-->

<script>

var '.$name.'Opts = {
	countItems : '.$countItem.',';
	
	if($attribs["options"]["autoUpload"]){
		$html .='autoUpload : true,';
	}
	
	if($attribs["options"]["maxNumberOfFiles"]){
		$html .='maxNumberOfFiles : '.$attribs["options"]["maxNumberOfFiles"].',';
	}
	
	if (!empty($attribs["options"]["allowedExtensions"]))
		$html .= 'allowedExtensions : "'. implode("|", $attribs["options"]["allowedExtensions"]) .'",
}

runJqueryFilesUpload("'.$name.'");
</script>

	</div>	';
	
	$html .= "<script>
					$( '.sortable' ).sortable({
						placeholder: 'ui-state-highlight',
						update: function() {  // callback quand l'ordre de la liste est changé	
						
							$(this).find('tr').map(function(index, obj) {
					        	var input = $(obj);
					        	$('#".$name."_'+input[0].getAttribute('data-index')).attr('name', '".$name."['+(index + 1)+']');
					        });					        
						}
					});
					</script>";
		
		return $html;
	}
	
}
