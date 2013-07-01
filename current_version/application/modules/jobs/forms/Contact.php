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

class Jobs_Form_Contact extends CMS_Form_Default
{
	
	public function init()
	{
		$item = new Zend_Form_Element_Text("mod_jobs_contact");
		$item->setRequired(true);
		$item->setLabel(_t("Adresses de contact"));
		$item->setDescription(_t("Candidatures spontanées (Séparés par des ;)"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Submit("save");
		$item->setValue(_t("save"));
		$this->addElement($item);
	}
	
}