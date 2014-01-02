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

class Menu_Form_Folder extends CMS_Form_Default
{
	public function init() {

		$el = new CMS_Form_Element_Text('label');
		$el->setLabel(_t('Title'));
		$el->setRequired(true);
		$el->setTranslatable(true);
		$this->addElement($el);
		
		$el = new CMS_Acl_Form_ElementViewAccessSelect("access");
		$el->setLabel(_t('Access'));
		$el->setRequired(true);
		$this->addElement($el);
		
		$el = new Zend_Form_Element_Checkbox("hidetitle");
		$el->setLabel(_t("Hide item title"));
		$this->addElement($el);
		
		$item = new CMS_Form_Element_ImageSelect("image");
		$item->setLabel(_t("Icon"));
		$this->addElement($item);
		
		/*** TYPE_PAGE ***/
		
		//////////////// Checkbox new document or existing link  
		
   		$item = new Zend_Form_Element_Radio('linkType');
    	$item->setRequired(true);
        $item->setLabel(_t('Select the element Type:'));
        $item->setMultiOptions(array("1" => _t('Link first child'), "2" => _t('Link existing page') , "3" => _t('External link'), "4" => _t("No link")));
        $item->setValue(1);
		$this->addElement($item);
		
		////////////////// GROUP Existing page
		
		$item = new Zend_Form_Element_Select('existingpage');
		$item->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'existingpage'));
		$item->setLabel(_t("Choose page to link"));
		
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
		if( $pages ) {
			foreach($pages as $page) {
				if(!$types[$page->type])
					continue;
				else
					$types[$page->type]["pages"][] = $page;
			}
		}
		
		if( $types ) {
			foreach ($types as $type)
			{
				if($type["pages"]){
					$item->addMultiOptions(array($type["type"] => array()));
					foreach ($type["pages"] as $page) 
					{
						$item->addMultiOption($page->id_page, ' - '.$page->title);
					}
				}
			}
		}
		$this->addElement($item);
		
		
		$this->addDisplayGroup(array(
                    'existingpage'
            ),'existinggroup');
        $linkgroup = $this->getDisplayGroup('existinggroup');
        $linkgroup->setAttrib('class', 'existinggroup');
        $linkgroup->setDecorators(array(
                    'FormElements',
                    'Fieldset',
                    array('HtmlTag',array('tag'=>'div', 'class' => 'existinggroup'))
        ));
		
		/////////////////// GROUP External page 
		$item = new Zend_Form_Element_Text('externalpage');
		$item->setLabel(_t('Enter external adress to link'));
		$item->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'externalpage'));
		$item->setAttrib('size',50);
		$this->addElement($item);

		$this->addDisplayGroup(array(
                    'externalpage'
            ),'externalgroup');
        $newgroup = $this->getDisplayGroup('externalgroup');
        $newgroup->setAttrib('class', 'externalgroup');
        $newgroup->setDecorators(array(
                    'FormElements',
                    'Fieldset',
                    array('HtmlTag',array('tag'=>'div', 'class' => 'externalgroup'))
        ));
		
		/*****************/
        
        $item = new Zend_Form_Element_Text("cssClass");
		$item->setLabel(_t("Class CSS"));
		$this->addElement($item);
		
        $item = new Zend_Form_Element_Submit("submit");
        $item->setValue(_t('Submit'));        
        $this->addElement($item);
	}
}