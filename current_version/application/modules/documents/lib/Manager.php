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

class Documents_Lib_Manager
{
	
 	public static function createForm($type)
    {
    	// Creation du formulaire générique
    	$form = new Documents_Form_Common();
    	
    	// Set du type de document choisi à l'étape 1
    	$form->getElement('type')->setValue($type);

    	// Récupération du xml en vue de l'ajout des composants spécifiques
    	$typesPath 	= PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/documents';
    	$typeConfig = new Zend_Config_Xml($typesPath.'/'.$type.'/type.xml');
    	$config = $typeConfig->toArray();
    	
    	// Instantiation d'un formulaire temporaire contenant les champs renseignés dans le XML
    	$formNodes = new CMS_Form_Default(array("xml" => $config["nodes"]));
    	
    	// Traitement du choix du template
        if ($config['templates']) {
        	
    		// Conversion en tableau
    		$config['templates'] = CMS_Application_Tools::resetLevels($config['templates']);
    		
    		// Si nombre de template proposé supérieur à 1
    		if(count($config['templates']['template']) > 1)
    		{
    			// Création de la liste
	    		$template = new Zend_Form_Element_Select('template');
				$template->setLabel('Template');

				// Ajout des élements
	            foreach ($config['templates']['template'] as $param)
	            	$template->addMultiOption($param['file'], $param['name']);	             
    		}
    		else 
    		{
    			// Création d'un champ hidden pour stoquer la valeur de l'unique template
    			$template = new Zend_Form_Element_Hidden('template');
    			$template->setValue($config['templates']['template'][0]['file']);
    		}
    		// Ajout de l'element template au formulaire
    		$form->addElement($template);
    	}
    	
    	// Ajout des éléments du formulaire temporaire au formulaire général
        $form->addElements($formNodes->getElements());
             
        // On place les élements dans un displayGroup
		foreach($formNodes->getElements() as $e){
			$addtoDisplayGroup[] = $e->getName();
		}
             
		$form->addDisplayGroup($addtoDisplayGroup, 'typeElements');
             
		$submit = $form->getElement('submit');
		$submit->setOrder(9998);
		$submitAndQuit = $form->getElement('submitandquit');
		$submitAndQuit->setOrder(9999);
    	
    	
    	return $form;
    }    
}