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

class Menu_Form_Menu extends CMS_Form_Default
{
	public function init()
	{
		$el = new CMS_Form_Element_Text("label");
		$el->setLabel(_t("Title"));
		$el->setRequired(true);
		$el->setTranslatable(true);
		$this->addElement($el);
		
		$el = new CMS_Form_Element_Text("subtitle");
		$el->setLabel(_t("Description"));
		$el->setTranslatable(true);
		$this->addElement($el);
		
		$el = new CMS_Form_Element_SubmitCustom("submit");
		$el->setLabel(_t("submit"));
		$this->addElement($el);
	}
}