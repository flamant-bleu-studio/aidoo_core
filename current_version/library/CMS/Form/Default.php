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

class CMS_Form_Default extends Zend_Form
{
	
    public function __construct($options = null)
    {
    	$this->addElementPrefixPath('CMS_Form_Decorator','CMS/Form/Decorator','decorator');
    	$this->addPrefixPath('CMS_Form_Element','CMS/Form/Element','element');
		
    	$initValidation = (isset($options["initValidation"]) && $options["initValidation"] === false) ? false : true;
    	$startValidation = (isset($options["startValidation"]) && $options["startValidation"] === false) ? false : true;
    	
    	unset($options["initValidation"]);
    	unset($options["startValidation"]);
		
    	if( isset($options["xml"]) && $options["xml"] )
    		$this->addElementPrefixPath('CMS_Validate','CMS/Validate/','validate');
    	
    	$options = (isset($options["xml"]) && $options["xml"]) ? $options["xml"] : $options;
    	
    	parent::__construct($options);
    	
    	if($initValidation)
    	{
	    	$append = CMS_Application_ProcessLayout::getInstance();
	    	
			if($startValidation)
    		{
		    	$id = $this->getId();
				
				if(!$id)
				{
					$id = "form_".mt_rand();
					$this->setAttrib("id", $id);
				}
				
				$append->appendJsScript("$('#".$id."').validationEngine();");
    		}
    	}
	}
    
	public function addElement($el)
	{		
		if( is_string($el) ) {
			$arguments = func_get_args();
			if( $el == "file")
				$arguments[2]["decorators"] = array(
												'File',
												'Description',
												'Errors',
												array(array('data'=>'HtmlTag'), array('tag' => 'td')),
												array('Label', array('tag' => 'td')),
												array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
												);
			else
				$arguments[2]["decorators"] = array("Standard"); // Add decorators
			
			parent::addElement($el, $arguments[1], $arguments[2]);
		}
		else {
		    $el->clearDecorators();
		    
		    if($el instanceof Zend_Form_Element_Checkbox)
		    	$el->addDecorator('Checkbox');
		    else 
	       		$el->addDecorator('Translate');
	        
			parent::addElement($el);
		}
	} 
    
	public function setAction($action)
    {
		$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseAction = rtrim($baseurl ,'/') . $action;
        parent::setAction($baseAction);
    }
    
    public function isValid($data){
    	
    	// Récupération des données sur les langues du CMS
    	$config = CMS_Application_Config::getInstance();
    	$default_lang = $config->get("defaultFrontLang");
    	
    	foreach ($this->getElements() as $key => $element) {

    		// Si cet élément est traduisible et requis
    		if(method_exists($element, "isTranslatable") && $element->isTranslatable() == true && $element->isRequired()){
    			
    			$name = $element->getName();
    			
    			// Récupération des valeurs de cet élement
    			$values 	= $data[$name];
    			// Récupération de la valeur de la langue par défaut
    			$value 		= $values[$default_lang];
    			
    			// Si la valeur de la langue par défaut est valide
    			if($element->isValid($value)){
    				
    				// On assigne à toutes les valeurs vides des autres langues la valeur de la langue par défaut
    				foreach($values as $lang_id => $val){
    					
    					// Continue sur la langue par défaut
    					if($lang_id == $default_lang)
    						continue;
    					
    					// Si valeur vide
    					if(!$val || $val == ""){
    						$data[$name][$lang_id] = $value;
    					}
    				}
    			}
    		}
    	}
    	
    	return parent::isValid($data);
    }
    
    protected function addLinkElements()
    {
    	/* Type de lien */
   		$linkType= new CMS_Form_Element_Radio('link_type');
    	$linkType->setLabel(_t('Select your Link Type:'));
    	$linkType->setDescription(_t("Choose you link type"));
        $linkType->setMultiOptions(array( _t('No link'), _t('Internal link') , _t('External link')));
        $linkType->setValue(0);
		$this->addElement($linkType);
		
		/* Page à lier */
		$item = new Zend_Form_Element_Select('link_internal');
		$item->setLabel(_t("Choose page to link"));
		$item->setDescription(_t("Choose page to link"));
		
		$pages = CMS_Page_Object::get(array("enable" => "1", "visible" => "1"), array("title"));
		$types = (array)CMS_Page_Type::get();
		
		$item->addMultiOption(1, _t('Home'));
		
		// Génération d'un tableau associatif : type => array object
		$tmp = array();
		foreach($types as $type){
			$tmp[$type->type] = $type->toArray();
		}
		$types = $tmp;
		
		// Remplissage de chaque type avec leurs pages
		foreach($pages as $page) {
			if(!$types[$page->type])
				continue;
			else
				$types[$page->type]["pages"][] = $page;
		}
		
		foreach ($types as $type) {
			if (isset($type["pages"]) && $type["pages"]) {
				$item->addMultiOptions(array($type["type"] => array()));
				foreach ($type["pages"] as $page) {
					$item->addMultiOption($page->id_page, ' - '.$page->title);
				}
			}
		}
		
		$this->addElement($item);
		
		/* Page externe */
		$item = $this->createElement('text', 'link_external');
		$item->setLabel(_t('Enter external adress to link (add before link \'http://\' or \'https://\')'));
		$item->setDescription(_t(""));
		$item->setAttrib('size',50);
		$this->addElement($item);
		
		/* Nouvelle fenetre */
        $item = new Zend_Form_Element_Checkbox('link_target_blank');
        $item->setLabel(_t('Open in new window'));
        $item->setDescription(_t("Check the box"));
        $this->addElement($item);
    }
}
