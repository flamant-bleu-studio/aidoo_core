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

class GalerieImage_Form_Image extends CMS_Form_Default
{
	public function init()
    {
    	$item = new CMS_Form_Element_ImageSelect("image");
		$item->setLabel(_t('Image'));
		$item->setDescription(_t("Select your image"));
		$item->setRequired(true);
		$this->addElement($item);
		
    	if(defined("DIAPO_SECOND_IMG") && DIAPO_SECOND_IMG == true) {
			$item = new CMS_TinyMCE_Forms_ImageSelect("image2");
			$item->setLabel(_t('Second image'));
			$item->setDescription(_t("Select your image"));
			$item->setRequired(false);
			$this->addElement($item);
		}
		else {
			$item = new CMS_Form_Element_Hidden("image2");
			$this->addElement($item);
		}
		
		$item = new Zend_Form_Element_Checkbox("addLink");
		$item->setLabel(_t('Add link to the image ?'));
		$item->setDescription(_t("Check to activate"));
		$item->setValue(0);
		$this->addElement($item);
		
		/* Type de lien */
   		$linkType= new Zend_Form_Element_Radio('link_type');
    	$linkType->setLabel(_t('Select your Link Type:'))
                ->setDescription(_t("Choose you link type"))
                ->setMultiOptions(array( _t('Internal link') , _t('External link')));
		$this->addElement($linkType);
		
		/* Page à lier */
		$item = new Zend_Form_Element_Select('page_link');
		$item->setLabel(_t("Choose page to link"));
		$item->setDescription(_t("Choose page to link"));
		
		$pages = CMS_Page_Object::get(array("enable" => "1", "visible" => "1"), array("title"));
		$types = (array)CMS_Page_Type::get();
		
		$item->addMultiOption(1, "Accueil");
		
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
		
		foreach ($types as $type)
		{
			if(isset($type["pages"]) && $type["pages"]){
				$item->addMultiOptions(array($type["type"] => array()));
				foreach ($type["pages"] as $page)
				{
					$item->addMultiOption($page->id_page, ' - '.$page->title);
				}
			}
		}
		
		$this->addElement($item);
		
		/* Page externe */
		$item = $this->createElement('text', 'external_page');
		$item->setLabel(_t('Enter external adress to link (without http://)'));
		$item->setDescription(_t(""));
		$item->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'externalpage', 'id' => 'externalpage'));
		$item->setAttrib('size',50);
		$this->addElement($item);
		
		/* Nouvelle fenetre */
        $item = new Zend_Form_Element_Checkbox('window');
        $item->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'id' => 'tblank'));
        $item->setLabel(_t('Open in new window'));
        $item->setDescription(_t("Check the box"));
        $this->addElement($item);
		
		$description = new CMS_Form_Element_TinyMCE('description');
		$description->setLabel(_t('Description'));
		$description->setDescription(_t("Image description"));
		$description->setAttrib('cols', '100');
		$description->setAttrib('rows', '5');
		$description->automaticallyAddControl(false);
		$this->addElement($description);
		
		$bg_color_image = new CMS_Form_Element_ColorPicker("bg_color_image");
		$bg_color_image->setLabel(_t('Background Color'));
		$bg_color_image->setDescription(_t('Background Color of the image'));
		$this->addElement($bg_color_image);
		
		$item = new Zend_Form_Element_Checkbox('isPermanent');
		$item->setLabel(_t("Permanent image?"));
		$item->setDescription(_t("Check to activate"));
		$item->setValue(1);
		$this->addElement($item);
		
		$date_start = new CMS_Form_Element_DatePicker("date_start");
		$date_start->setLabel(_t('Activation Date').' : ');
		$date_start->setDescription(_t("Choose activation date"));
		$date_start->setAttrib('size',20);
		$this->addElement($date_start);
		
		$date_end = new CMS_Form_Element_DatePicker('date_end');
		$date_end->setLabel(_t('Deactivation Date').' : ');
		$date_end->setDescription(_t("Choose deactivation date"));
		$date_end->setAttrib('size',20);
		$this->addElement($date_end);
    }
}