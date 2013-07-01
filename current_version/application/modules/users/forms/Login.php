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

class Users_Form_Login extends CMS_Form_Default
{
	public function init() {
		
		$typeLogin = $this->getAttrib('typeLogin');
		
		$item = new Zend_Form_Element_Hidden('type');
		$item->setValue('login');
		$this->addElement($item);
		
		if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_PASSWORD) {
			
			$item = new Zend_Form_Element_Text("email");
			$item->setRequired(true);
			$item->setLabel(_t("Email"));
			$item->setDescription(_t("Enter your email adress"));
			$item->addValidator(new Zend_Validate_EmailAddress());
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Password("password");
			$item->setLabel(_t('Password'));
			$item->setRequired(true);
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Submit("submit");
			$item->setValue(_t("Login"));
			$this->addElement($item);
			
		}
		else if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_ONLY) {
			
			$item = new Zend_Form_Element_Text("email");
			$item->setRequired(true);
			$item->setLabel(_t("Email"));
			$item->setDescription(_t("Enter your email adress"));
			$item->addValidator(new Zend_Validate_EmailAddress());
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Submit("submit");
			$item->setValue(_t("Login"));
			$this->addElement($item);
			
		}
	}
}