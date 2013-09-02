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

class Bloc_BlocResponsive_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new CMS_Form_Element_ColorPicker('background_color');
		$item->setLabel(_t('Background color'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('text');
		$item->setLabel(_t('Text'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_ColorPicker('text_color');
		$item->setLabel(_t('Text color'));
		$this->addElement($item);	
		
		$item = new Zend_Form_Element_Select('icon');
		
		$directoryIcons = new DirectoryIterator(PUBLIC_PATH . '/skins/'.SKIN_FRONT.'/icon/');
		foreach ($directoryIcons as $file) {
			if ( !$file->isDir() ) {
				$name = substr($file->getFileName(), 0, strlen($file->getFileName())-4);
				$icons[] = array($name => $name);
				$item->addMultiOption($name, $name);
			}
		}
		
		$item->setLabel(_t('Icon'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('id_page');
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
	}

}