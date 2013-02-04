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

class Advertising_Form_Campaign extends CMS_Form_Default
{
	public function __construct()
	{
		parent::__construct($options);
	}
	
	public function init()
	{
		$datas = new Zend_Form_Element_Hidden("datas");
		$this->addElement($datas);
		
		$item = new Zend_Form_Element_Text("title");
		$item->setLabel(_t('Title'));
		$item->setDescription(_t('Title of your campaign'));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Checkbox('limited');
		$item->setLabel(_t('Limited time period'));
		$item->setDescription(_t("Check to activate"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_DatePicker('date_start');
		$item->setLabel(_t('Activation Date'));
		$item->setDescription(_t("Choose activation date"));
		$item->setAttrib('size',20);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_DatePicker('date_end');
		$item->setLabel(_t('DeActivation Date'));
		$item->setDescription(_t("Choose deactivation date"));
		$item->setAttrib('size',20);
		$this->addElement($item);
			
	}
}