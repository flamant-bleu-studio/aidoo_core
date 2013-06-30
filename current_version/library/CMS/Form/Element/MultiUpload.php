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

class CMS_Form_Element_MultiUpload extends Zend_Form_Element_Text
{
	public 	$helper = 'formMultiUpload';
	private $_options;
	
	protected $_isTranslatable;
	
	public function isTranslatable(){
		return $this->_isTranslatable;
	}
	
	/**
	 * Element de formulaire MultiUpload
	 * 
	 * Options (*obligatoire) :
	 * - *uploadPath : (string) chemin d'upload relatif au dossier public (www)
	 * - uploadThumbsPath : (string) chemin d'upload des vignettes (si images uploadées) relatif au dossier public (www)
	 * - adminOnly : (bool) uniquement les personnes administratrices
	 * - maxNumberOfFiles : (int) nombre de fichier maximum à uploader
	 * 
	 * @param string $field_name nom de l'élement
	 * @param array $attributes options
	 */
	public function __construct($field_name, $attributes = null)
	{
		Zend_Layout::getMvcInstance()->getView()->addHelperPath('CMS/Form/Helper/', 'CMS_Form_Helper_');
		
		$this->_options = $attributes["params"];
		
		parent::__construct($field_name, $attributes);
	}
	
	public function setAllowedExtensions($extensions = array()){
		$this->_options["allowedExtensions"] = $extensions;
	}
	
	public function setMaxNumberOfFiles($nb = null){
		$this->_options["maxNumberOfFiles"] = $nb;
	}
	
	public function setAdminOnly($flag = true){
		$this->_options["adminOnly"] = $flag;
	}
	
	public function setUploadPath($path){
		$this->_options["uploadPath"] = $path;
	}
	
	public function setAutoUpload($value = true) {
		$this->_options["autoUpload"] = $value;
	}
	
	private function setLibParams(){
	
		if(is_string($this->_options["allowedExtensions"]))
			$this->_options["allowedExtensions"] = explode(',', $this->_options["allowedExtensions"]);
		
		$this->_options["autoUpload"] 	= (isset($this->_options["autoUpload"]) && ($this->_options["autoUpload"] === true || $this->_options["autoUpload"] == 'true')) ? true : false;
		$this->_options["adminOnly"] 	= (isset($this->_options["adminOnly"]) && ($this->_options["adminOnly"] === true || $this->_options["adminOnly"] == 'true')) ? true : false;
		
		// Session pour que le controller d'upload puisse récupérer les options
		$session = new Zend_Session_Namespace("multiUpload-".$this->getName());
		$session->options = $this->_options;
		
		// Attribut pour la vue
		$this->setAttrib("options", $this->_options);
	}
	
	public function render(Zend_View_Interface $view = null)
    {
    	$this->setLibParams();
    	
    	$processLayout = CMS_Application_ProcessLayout::getInstance();
    	
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/js/load-image.minANDcanvas-to-blob.min.js");
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/js/jquery.iframe-transport.js");
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/js/jquery.fileupload.js");
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/js/jquery.fileupload-ip.js");
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/js/jquery.fileupload-ui.js");
    	$processLayout->appendJsFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/js/main.js");
    	
    	$processLayout->appendCssFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/css/bootstrap.css");
    	$processLayout->appendCssFile(COMMON_LIB_PATH . "/lib/jqueryFileUpload/css/jquery.fileupload-ui.css");
    	
    	return parent::render($view);
    }
    
    public function setValue($value){    	
		if($value){
			
			if (is_array($value))
				ksort($value);
			
			// IF JSON
			if($values = @json_decode($value, true))
				$value = array_values($values);
			
			if(is_array($value))
				$value = array_filter($value, 'strlen'); // Retrait des valeurs NULL
			else if(is_string($value))
				$value = array($value);
			
			
			parent::setValue($value);
		}
    }
    
}
