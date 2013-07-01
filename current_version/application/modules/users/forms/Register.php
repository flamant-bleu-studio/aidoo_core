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

class Users_Form_Register extends CMS_Form_Default
{
	public function init()
	{
		$typeLogin = $this->getAttrib('typeLogin');
		
		$item = new Zend_Form_Element_Hidden('type');
		$item->setValue('register');
		$this->addElement($item);
		
		if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_PASSWORD) {
			
			$item = new Zend_Form_Element_Text("username");
			$item->setRequired(true);
			$item->setLabel(_t("Username"));
			$item->setDescription(_t("Enter your nickname"));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Text("email");
			$item->setRequired(true);
			$item->setLabel(_t("Email"));
			$item->setDescription(_t("Enter your email adress"));
			$item->setValidators( array( new Zend_Validate_EmailAddress() ) );
			$this->addElement($item);
	
			$item = new Zend_Form_Element_Select("civility");
			//$item->setRequired(true);
			$item->setLabel(_t("Civility"));
			$item->setDescription(_t("Choose your civility"));
			$item->addMultiOptions(array(""=>_t("Choose"), "Mlle"=>"Mlle", "Mme"=>"Mme", "M"=>"M"));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Text("firstname");
			$item->setRequired(true);
			$item->setLabel(_t("First name"));
			$item->setDescription(_t("Enter your first name"));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Text("lastname");
			$item->setRequired(true);
			$item->setLabel(_t("Last name"));
			$item->setDescription(_t("Enter your last name"));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Password("password");
			$item->setRequired(true);
			$item->setLabel(_t("Password"));
			$item->setDescription(_t("Enter your password"));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Password('verifPassword');
			$item->setLabel(_t('Confirm password'))
					->setRequired(true)
					->addValidator('identical', true, array('password'));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Submit("submit");
			$item->setValue(_t("Register"));
			$this->addElement($item);
			
		}
		else if ($typeLogin == CMS_Acl_User::TYPE_LOGIN_MAIL_ONLY) {
			
			$item = new Zend_Form_Element_Text("email");
			$item->setRequired(true);
			$item->setLabel(_t("Email"));
			$item->setDescription(_t("Enter your email adress"));
			$item->setValidators( array( new Zend_Validate_EmailAddress() ) );
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Text("firstname");
			$item->setRequired(true);
			$item->setLabel(_t("First name"));
			$item->setDescription(_t("Enter your first name"));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Text("lastname");
			$item->setRequired(true);
			$item->setLabel(_t("Last name"));
			$item->setDescription(_t("Enter your last name"));
			$this->addElement($item);
			
			$item = new Zend_Form_Element_Submit("submit");
			$item->setValue(_t("Register"));
			$this->addElement($item);
		}
	}
}