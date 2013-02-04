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

class Menu_Form_Item extends CMS_Form_Default
{
	public function init()
	{
		$el = new CMS_Form_Element_Text("label");
		$el->setLabel(_t("Title"));
		$el->setRequired(true);
		$el->setTranslatable(true);
		$this->addElement($el);
		
		$el = new CMS_Form_Element_Text("subtitle");
		$el->setLabel(_t("Subtitle"));
		$el->setTranslatable(true);
		$this->addElement($el);
		
		$item = new Zend_Form_Element_Checkbox("hidetitle");
		$item->setLabel(_t("hide title"));
		$item->setAttrib('size',40);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox("loadAjax");
		$item->setLabel(_t("Load in ajax"));
		$item->setAttrib('size',40);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_ImageSelect("image");
		$item->setLabel(_t("Icon"));
		$this->addElement($item);
		
		$item = new CMS_Acl_Form_ElementViewAccessSelect("access");
		$item->setLabel(_t('Access').' : ');
		$item->setRequired(true);
		$this->addElement($item);
		
		/*** TYPE_PAGE ***/
		
		//////////////// Checkbox new document or existing link  
		
   		$item = new CMS_Form_Element_Radio('linkType');
    	$item->setRequired(true);
        $item->setLabel(_t('Select the element Type:'));
        $item->setMultiOptions(array("1" => _t('New page'), "2" => _t('Link existing page') , "3" => _t('External link')));
        $item->setValue(2);
        $item->setSeparator('');
        
		$this->addElement($item);
		
		/////////////////// GROUP New page 

		$chooseType = new Zend_Form_Element_Hidden("chooseType");
		$this->addElement($chooseType);
		
		$hooks = CMS_Application_Hook::getInstance();
		$allPages = $hooks->apply_filters("listCreateApi");
		
		$arrayName = array();
		$first = true;
		
		foreach($allPages as $key => $type)
		{
			$button = new Zend_Form_Element_Button($key);
			$button->setLabel("");
			
			if ($first === true){
				$button->setAttrib("class", "active typeChoice");
				$chooseType->setValue($key);
				$first = false;
			}
			else {
				$button->setAttrib("class", "typeChoice");
			}
			
			$this->addElement($button);
			$arrayName[] = $key;
		}
		
		$this->addDisplayGroup($arrayName, 'newgroup');
            
        $newgroup = $this->getDisplayGroup('newgroup');
        
        $newgroup->setAttrib('class', 'newgroup');
        $newgroup->setDecorators(array(
                    'FormElements',
                    array('HtmlTag',array('tag'=>'div', 'class' => 'newgroup'))
        ));
		
		
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
		
		$item = new Zend_Form_Element_Checkbox("tblank");
		$item->setLabel(_t("Open in new window"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom("submit");     
        $item->setLabel(_t('Submit'));
        $this->addElement($item);
	}
}