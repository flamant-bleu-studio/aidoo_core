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

class Seo_Form_Page extends CMS_Form_Default
{
	
    public function init() {

    	// Titre
		$item = new CMS_Form_Element_Text('title');
		$item->setLabel(_t('Page title'));
		$item->setTranslatable(true);
		$this->addElement($item);
		
		// Url rewrite
		$item = new CMS_Form_Element_Text('url_rewrite');
		$item->setLabel(_t('Rewrite Url'));
		$item->setTranslatable(true);
		$this->addElement($item);
		
		// Rewrite_var
		$item = new CMS_Form_Element_Text('rewrite_var');
		$item->setLabel(_t('Rewrite var'));
		$this->addElement($item);
		
		// Metas keywords
		$item = new CMS_Form_Element_Textarea('meta_keywords');
		$item->setLabel(_t('Keywords'));
		$item->setDescription(_t('Séparés par des virgules'));
		$item->setTranslatable(true);
		$this->addElement($item);

		// Metas description
		$item = new CMS_Form_Element_Textarea('meta_description');
		$item->setLabel(_t('Description'));
		$item->setTranslatable(true);
		$this->addElement($item);
		
		// Modèle de disposition de blocs
		$item = new CMS_Form_Element_AdvancedSelect("template");
    	$item->setLabel(_t('Template'));
    	
		$item->addMultiOption(null, "par défaut");
		
		$templates = Blocs_Object_Template::get(null, array('title'));
		if(count($templates)>0) 
			foreach($templates as $tpl)
				$item->addMultiOption($tpl->id_template, " - ".$tpl->title);
		
		$this->addElement($item);
		
		/** VAR ONLY FOR SUPER ADMIN **/
		
		// url system
		$item = new CMS_Form_Element_Text('url_system');
		$item->setLabel(_t('url system'));
		$this->addElement($item);
		
		// enable
		$item = new CMS_Form_Element_Checkbox('enable');
		$item->setLabel(_t('enable'));
		$this->addElement($item);

		// visible
		$item = new CMS_Form_Element_Checkbox('visible');
		$item->setLabel(_t('visible'));
		$this->addElement($item);
		
		// wildcard
		$item = new CMS_Form_Element_Checkbox('wildcard');
		$item->setLabel(_t('wildcard'));
		$this->addElement($item);

		// type
		$item = new CMS_Form_Element_Text('type');
		$item->setLabel(_t('type'));
		$this->addElement($item);		
		

		// Diaporama
		$item = new CMS_Form_Element_AdvancedSelect("diaporama");
    	$item->setLabel(_t('Diaporama'));
    	
		$item->addMultiOption("null", "Aucun");
		$item->addMultiOption("0", "par défaut");
		
		$diaporamas = GalerieImage_Object_Galerie::get(array("type" => GalerieImage_Object_Galerie::TYPE_DIAPORAMA));
		if(count($diaporamas)>0) 
			foreach ($diaporamas as $diaporama) 
				$item->addMultiOption($diaporama->id, " - ".$diaporama->title);
			
		$this->addElement($item);
		
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t('Submit'));
		$item->setLabel(_t('Submit'));
		$this->addElement($item);
    }
}
