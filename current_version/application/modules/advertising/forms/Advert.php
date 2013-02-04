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

class Advertising_Form_Advert extends CMS_Form_Default
{
	
	public function init()
	{
		$item = new CMS_Form_Element_ImageSelect("image");
		$item->setLabel(_t('Choose your picture'));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Radio('link_type');
    	$item->setLabel(_t('Select your Link Type:'));
        $item->setDescription(_t("Choose you link type"));
        $item->setMultiOptions(array( _t('Internal link') , _t('External link')));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('page_link');
		$item->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'existingpage', 'id' => 'existingpage'.$index));
		$item->setLabel(_t("Choose page to link"));
		$item->setDescription(_t("Choose page to link"));
		
		$types = CMS_Page_Type::get();
	    
		if( $types ) {
			foreach ($types as $type)
			{
				$pages = CMS_Page_Object::get(array("enable" => 1, "visible" => 1, "type" => $type->type));
				
				if( !empty($pages) ) {
					$item->addMultiOptions(array($type->type => array()));
					foreach ($pages as $page) 
					{
						$item->addMultiOption($page->url_system, ' - '.$page->title);
					}
				}
				unset($pages);
			}
		}
		$this->addElement($item);
		
		$item = $this->createElement('text', 'external_page');
		$item->setLabel(_t('Enter external adress to link'));
		$item->setDescription(_t("Without http://"));
		$item->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'externalpage', 'id' => 'externalpage'.$index));
		$item->setAttrib('size',50);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('window');
        $item->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'id' => 'tblank'.$index));
        $item->setLabel(_t('Open in new window'));
        $item->setDescription(_t("Check the box"));
        $this->addElement($item);
        
        $item = new Zend_Form_Element_Text("weight");
        $item->setLabel(_t('Weight'));
        $item->setDescription(_t("Weight of your advertising"));
        $this->addElement($item);
        
        $item = new Zend_Form_Element_Checkbox("addtext");
        $item->setLabel(_t('Add a text above the image'));
        $item->setDescription(_t("Check to add"));
        $this->addElement($item);
        
        $item = new CMS_Form_Element_TinyMCE('text');
		$item->setLabel(_t('Text'));
		$item->setAttrib('cols', '100');
		$item->setAttrib('rows', '5');
		$item->automaticallyAddControl(false);
		$this->addElement($item);
	}
}