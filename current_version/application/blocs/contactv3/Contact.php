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

class Bloc_Contactv3_Contact extends CMS_Bloc_Abstract  implements CMS_Bloc_Interface {
	
	public $type_form;
	
	protected $_adminFormClass = "Bloc_Contactv3_AdminForm";
	protected static $_translatableFields = array();
	
	public function runtimeFront($view)
	{
		//$contacts = Contact_Object_Contact::get(array("type" => $this->type_form));
		$contact = Contact_Object_Contact::getOne(array("type" => $this->type_form));
		
		$form = $contact->getForm();
		
		$view->content = $contact->content;
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