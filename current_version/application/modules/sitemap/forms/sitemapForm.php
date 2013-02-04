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

class sitemap_Form_sitemapForm extends CMS_Form_Default
{
	
    public function init()
    {
    	$menus = Menu_Object_Menu::getAllMenuID();
    	$menus_generate = array();
    	
		foreach ($menus as $menu) {
			$menu_obj = new Menu_Object_Menu((int)$menu);
			$menu_obj->disableRenderNested();
			$menu_obj->generate();
			
			foreach($menu_obj->items as $item)
			{
				$checkbox = new Zend_Form_Element_Checkbox("hide_".$item->id_menu);
				$checkbox->setLabel(str_repeat("_", $item->level-1).$item->label);
				$checkbox->setDecorators(array(
					'ViewHelper',
					array('Label', 'options' => array(
						'placement' => 'append')
					),
					array('decorator' => array('data' => 'HtmlTag'), 
						'options' => array('tag' => 'div')
					)
				));
				
				$this->addElement($checkbox);
			}
		}
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t('Submit'));
		$item->setLabel(_t('Submit'));
		$this->addElement($item);
    }
	
	public function decodeParams($params)
	{
		$arr = json_decode($params);
		
		if ($arr)
		{
			foreach ($arr as $paramKey => $paramValue)
			{
				$paramElement = $this->getElement($paramKey);
				if ($paramElement)
					$paramElement->setValue($paramValue);
			}
		}
	}
	
	public function encodeParams()
	{
		$elements = $this->getElements();
		$arr = array(); 
		
		foreach ($elements as $name => $value)
		{
			if (substr($name,0,5)=="hide_")
			{
				$paramValue = $value->getValue($name);
				if ($paramValue)
					$arr += array ($name => $paramValue ); 
			}
		}
		return json_encode($arr);
	}
}
