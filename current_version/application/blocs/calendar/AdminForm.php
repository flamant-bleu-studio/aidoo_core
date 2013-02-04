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

class Bloc_Calendar_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new CMS_Form_Element_AdvancedMultiSelect('id_calendar');
		$item->setRequired(true);
		$item->setLabel(_t('Calendars'));
		
		$calendars = Calendar_Object_Calendar::get(array('status' => 1));
		foreach ($calendars as $c)
			$item->addMultiOption($c->id_calendar, $c->name);
		
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('display');
		$item->setRequired(true);
		$item->setLabel(_t('Display'));
		$item->addMultiOption(1, _t('Month navigator'));
		$this->addElement($item);
	}
}