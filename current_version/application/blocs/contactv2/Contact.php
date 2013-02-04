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

class Bloc_Contactv2_Contact extends CMS_Bloc_Abstract  implements CMS_Bloc_Interface {
	
	public $type_form;
	
	protected $_adminFormClass = "Bloc_Contactv2_AdminForm";
	
	public function runtimeFront($view)
	{
		$contacts = Contact_Object_Contact::get(array("type" => $this->type_form));
		
		if( $xml = new Zend_Config_Xml(APPLICATION_PATH . "/modules/contact/types/" . $this->type_form . "/type.xml") )
		{
			if( $xml->bloc && $xml->bloc == "true" )
			{
				$form = new CMS_Form_Default(array("xml" => $xml->nodes));
				
				$item = new Zend_Form_Element_Submit("send");
				$item->setValue(_t("Send"));
				$form->addElement($item);
			}
			else
				throw new Zend_Exception(_t("This type from doesn't available to bloc"));
		}
		else
			throw new Zend_Exception(_t("This type xml doesn't exist"));
		
		$view->form = $form;
		$view->type_form = $this->type_form;
	}
	
	public function save($post)
	{	
		$this->type_form = $post["type_form"];
		
		$id = parent::save($post);
		
		return $id;
	}
}