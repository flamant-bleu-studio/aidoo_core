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

class Bloc_Weather_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();

		$item = new Zend_Form_Element_Text("code");
		$item->setLabel(_t("Code myweather2"));
		$item->setDescription(_t("faire un compte http://www.myweather2.com/ilogin.aspx"));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text("latlong");
		$item->setLabel(_t("Lat,Long"));
		$item->setDescription(_t("Voir sur googleMap - 'plus d'info sur cet endroit'"));
		$this->addElement($item);
		
	}
}