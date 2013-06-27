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

class CMS_Form_Decorator_Translate extends Zend_Form_Decorator_Abstract implements Zend_Form_Decorator_Marker_File_Interface
{
    public function render($content)
    {
        $element = $this->getElement();
        
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        
        if (null === $element->getView()) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $errors    = $this->buildErrors();
        $desc      = $this->buildDescription();
        $class     = '';
        $label	   = '';
        
        if ($element->getType() == 'Zend_Form_Element_Submit')
        	$class = ' submit';
        else
        	$label = $this->buildLabel();
        
        if ($element->getType() == 'Zend_Form_Element_File')
        	$content = '';	
        
        $name = $element->getName();
		
        $isTranslatable = (method_exists($element, 'isTranslatable') && $element->isTranslatable() == true) ? true : false ;
        
        $config = CMS_Application_Config::getInstance();
       	$langs = json_decode($config->get('availableFrontLang'), true);
		$countLangs = count($langs); // Nombre de langue active
       	
        $output = '<div class="form_line' . $class . '" id="form_' . $name . '">'
	        		. '<div class="form_text">'
		                . $label
		                . $desc
	                . '</div>'
	                . '<div class="form_elem">'; 
	                
	                $classInput 		= $element->getAttrib('class');
	                $classValidation 	= $this->buildValidationClass();
	                
	                if($isTranslatable){
	                	
		                foreach($langs as $id_lang => $code_lang){
		                	
		                	/*
		                	 * Gestion des classes de l'élément
		                	 */
		                	if( $id_lang == DEFAULT_LANG_ID )							// Si la langue est celle par defaut
		                		$class = $classInput . " " . $this->buildValidationClass();		// Récupération des classes
		                	else 														// Si la langue est différente de celle par defaut
		                		$class = $classInput . " " . $this->buildValidationClass(true); // Récupération des classes avec Breack Require
		                	
		                	$class = "element_lang_" . $id_lang . " " . $class;			// Ajout d'une class spécifiant la langue de l'élément (utilisé pour la validation JS)
		                	
		                	/*
		                	 * Cacher les inputs des langues différentes de celle par défaut du site
		                	 */
		                	if( $id_lang != DEFAULT_LANG_ID )
			                	$output .= '<div class="lang_'.$id_lang.'" style="display:none;">';
			                else
			                	$output .= '<div class="lang_'.$id_lang.'">';
			                
		               		$output .= $this->buildInput($id_lang, $class);				// Génération de l'élément
		               		
		               		$output .= '</div>';
		                }
		                
		                // Affichage du lang switcher seulement si plus d'une langue
		                if( $countLangs > 1 )
			                $output .= $this->buildLangSwitcher();
		                
	                }
	                else {
	                	$output .= $this->buildInput(null, $this->buildValidationClass()." ".$classInput);
	                }
	                
	    $output .= '</div>'
	                . $errors
	                . '<div class="clear"></div>'
                . '</div><div class="clear"></div>';
        
        
        switch($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }
    
    public function buildLabel()
    {
        $element = $this->getElement();
        $label = $element->getLabel();

       if (empty($label)) {
            return '';
        }
        
        if ($element->isRequired()) {
            $label .= '*';
        }
        
        return '<div class="form_label">'. $element->getView()->formLabel($element->getName(), $label) . '</div>';
    }

    public function buildInput($id_lang = null, $classInput = null)
    {
        $element = $this->getElement();
        
        $element->setAttrib("class", $classInput);
        	
        $helper  = $element->helper;

        $name = ($id_lang == null) ? $element->getName() : $element->getName()."[".$id_lang."]" ;
        
        $value = $element->getValue($id_lang);
        
        return $element->getView()->$helper(
            $name,
            $value,
            $element->getAttribs(),
            $element->options
        );
    }

    public function buildErrors()
    {
        $element  = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return '<div class="form_error">' . $element->getView()->formErrors($messages) . '</div>';
    }

    public function buildDescription()
    {
        $element = $this->getElement();
        $desc    = $element->getDescription();
        if (empty($desc)) {
            return '';
        }
        return '<div class="form_desc">' . $desc . '</div>';
    }

	public function buildValidationClass($breakRequire = false){
		
		$element = $this->getElement();
		
		$element_class = get_class($element);
		
		if( $element_class == "CMS_Form_Element_TinyMCE")
			return "";
		
        $validationClass = "validate[";
        $validateRequired = false;
        
        if($element->isRequired() && !$breakRequire)
        {
        	$validationClass .= "required";
        	$validateRequired = true;
        }
        
        $validators = $element->getValidators();

        if($validators)
        {
	        foreach($validators as $v)
	        {
	        	$classTest = get_class($v);
	        	
	        	switch ($classTest) 
	        	{
	        		case "Zend_Validate_EmailAddress" :
						$validationClass .= ",custom[email]";
						$validateRequired = true;
						break;
					case "Zend_Validate_PostCode" :
						$validationClass .= ",custom[postcode]";
						$validateRequired = true;
						break;
					case "CMS_Validate_Phone" :
						$validationClass .= ",custom[phone]";
						$validateRequired = true;
						break;
					case "CMS_Validate_ColorHexa" :
						$validationClass .= ",custom[colorHexa]";
						$validateRequired = true;
						break;
					case "Zend_Validate_Digits" :
						$validationClass .= ",custom[integer],min[0]";
						$validateRequired = true;
						break;
						
					default: 
						break;
	        	}
	        }
        }
        
        $validationClass .= "]";

      	return ($validateRequired) ? $validationClass : null;
	}

	public function buildLangSwitcher() {
		
		$config = CMS_Application_Config::getInstance();
		$langs = json_decode($config->get("availableFrontLang"), true);
		
		/*
		 * Génération du langue switcher
		*/
		$output  = "";
		$output .= '<div class="langSwitcher">';
		$output .= '<img class="langActive" src="'. BASE_URL .'/images/flags/'. DEFAULT_LANG_CODE .'.png" realid="'. DEFAULT_LANG_ID .'">';
		$output .= '<div class="others">';
		
		foreach($langs as $id_lang => $code_lang) {
			if( $id_lang == DEFAULT_LANG_ID )
				$output .= '<img src="'. BASE_URL .'/images/flags/'. $code_lang .'.png" realid="'. $id_lang .'" class="current flag">';
			else
				$output .= '<img src="'. BASE_URL .'/images/flags/'. $code_lang .'.png" realid="'. $id_lang .'" class="flag">';
		}
		
		$output .= '</div>';
		$output .= '</div>';
		
		return $output;
	}
}