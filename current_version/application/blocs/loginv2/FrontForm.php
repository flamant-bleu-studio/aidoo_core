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

class Bloc_Loginv2_FrontForm extends CMS_Bloc_ParentForm
{
	public function init()
	{		
		$item = new Zend_Form_Element_Hidden('type');
		$item->setValue(_t('login'));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('email');
		$item->setLabel(_t("email"));
		$item->setValue(_t('EMAIL'));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('password');
		$item->setLabel(_t("Password"));
		$item->setValue(_t('PASSWORD'));
		$item->setRequired(true);
		$this->addElement($item);
		
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t("Login"));
		$item->setLabel(_t('CONNEXION'));
		$this->addElement($item);
	}
}