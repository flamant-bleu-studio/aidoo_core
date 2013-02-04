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

class Users_Form_UserForm extends CMS_Form_Default
{
	public function init()
	{
		$item = new Zend_Form_Element_Select("civility");
		$item->setRequired(true);
		$item->setLabel(_t("Civility"));
		$item->addMultiOptions(array(""=>_t("choose"), "M"=>"M", "Mme"=>"Mme", "Mlle"=>"Mlle"));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('firstname');
		$item->setLabel(_t('First name'))
				->setRequired(true)
				->setAttrib('size',20);
		$this->addElement($item);

		$item = new Zend_Form_Element_Text('lastname');
		$item->setLabel(_t('Last name'))
				->setRequired(TRUE)
				->setAttrib('size',20);
		$this->addElement($item);

		$item = new Zend_Form_Element_Text('email');
		$item->setLabel(_t('Email'))
				->setRequired(TRUE)
				->addValidator('EmailAddress', false)
				->setAttrib('size',20);
		$this->addElement($item);		
		
		$item = new CMS_Acl_Form_ElementGroupSelect('group');
		$item->setLabel(_t("User group"))
				->setRequired(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Radio('isActive');
        $item->setLabel(_t('Enable account ?'))
            	->addMultiOptions(array(
                    '1' => _t('Yes'),
                    '0' => _t('No') 
                 ))
                 ->setSeparator('')
                 ->setValue('1');
        $this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setLabel(_t("Create"));
		$this->addElement($item);
	}
	
}
