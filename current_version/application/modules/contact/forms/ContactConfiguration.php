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

class Contact_Form_ContactConfiguration extends CMS_Form_Default
{
	private $name;
	
	public function __construct($options)
	{
		$this->name = $options["name"];
		unset($options["name"]);
		
		parent::__construct($options);
	}
	
	public function init()
	{	
		$item = new Zend_Form_Element_Radio($this->name."typeContact");
		$item->setLabel(_t("Type de formulaire"));
		$item->addMultiOption(0, "Classique");
		$item->addMultiOption(1, "Select");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text($this->name."selectName");
		$item->setLabel(_t("Name"));
		$item->setDescription(_t("Name in select"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text($this->name."selectMails");
		$item->setLabel(_t("Adresses mails destinataires"));
		$item->setDescription(_t("Séparé par des points virgules"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text($this->name."emails");
		$item->setLabel(_t("Adresses mails destinataires"));
		$item->setDescription(_t("Séparé par des points virgules"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text($this->name."emailsCci");
		$item->setLabel(_t("Adresses mails Cci"));
		$item->setDescription(_t("Séparé par des points virgules"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_TinyMCE($this->name."content");
		$item->setLabel(_t("Content"));
		$item->setDescription("Contenue de la page contact");
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox($this->name."response_check");
		$item->setLabel(_t("Response activation"));
		$item->setDescription("Automatique response");
		$this->addElement($item);
		
		$item = new CMS_Form_Element_TinyMCE($this->name."auto_response");
		$item->setLabel(_t("Auto response"));
		$item->setDescription("Automatique response");
		$this->addElement($item);
	}
}