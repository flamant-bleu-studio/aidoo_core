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

class Users_Form_OptionsUsers extends CMS_Form_Default {
	
	public function init() {

		$item = new Zend_Form_Element_Checkbox('mailAdminNewAccount');
		$item->setLabel(_t('Notify admin'));
		$item->setDescription(_t("Notify the administrator when an account is created"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text("emailNotify");
		$item->setLabel(_t('Email'));
		$item->setDescription(_t("Email notification"));
		$item->addValidator(new Zend_Validate_EmailAddress());
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('formatDisplayName');
		$item->setLabel(_t('Format display username'));
		$item->setDescription(_t('Select format to display username'));
		$item->addMultiOptions(array(
			Users_Object_User::$FORMAT_DISPLAY_PSEUDO			 => _t("Nickname"),
			Users_Object_User::$FORMAT_DISPLAY_FIRSTNAME 		 => _t("Firstname"),
			Users_Object_User::$FORMAT_DISPLAY_FIRSTNAME_LASTNAME => _t("Firstname + lastname"),
		));
		$this->addElement($item);
		
		$groups = Users_Lib_Manager::getAllGroups();
		
		$item = new CMS_Form_Element_AdvancedMultiSelect('groupFrontList');
		$item->setLabel(_t('Which groups appear in the front list'));
		
		$item->addMultiOption(1, "SuperAdmin");
		
		foreach($groups as $group)
			if($group->name != "Public")
				$item->addMultiOption($group->id, $group->name);
		
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('typeLogin');
		$item->setLabel(_t('Type of login'));
		
		$item->addMultiOptions(array(
			CMS_Acl_User::TYPE_LOGIN_MAIL_PASSWORD => _t('Email + Password'),
			CMS_Acl_User::TYPE_LOGIN_MAIL_ONLY => _t('Mail only')
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('pageMiddleOffice');
		$item->setLabel(_t('Home page middle office'));
		
		$_pages = CMS_Page_PersistentObject::get();
		$pages = array();
		
		if ($_pages) {
			foreach ($_pages as $page) {
				if (!empty($page->title[CURRENT_LANG_ID]) && !empty($page->type))
					$pages[$page->id_page] = $page->title[CURRENT_LANG_ID] . ($page->type ? ' - ' . $page->type : '');
			}
		}
		
		$item->addMultiOptions($pages);
		$this->addElement($item);
	}
}