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

class Users_Form_GroupForm extends CMS_Form_Default
{
	public function init()
	{

		$id = $this->createElement('hidden', 'id');
		$id->setDecorators(array('ViewHelper'));
		$this->addElement($id);

		$firstname = $this->createElement('text', 'name');
		$firstname->setLabel('Name : ');
		$firstname->setRequired(TRUE);
		$firstname->setAttrib('size',20);
		$this->addElement($firstname);
		
		/*$item = new CMS_Acl_Form_ElementGroupSelect('parent');
		$item->setLabel(_t('Group parent'));
		$item->setRequired(TRUE);
		$this->addElement($item);*/

		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t('Submit'));
		$item->setLabel(_t('Submit'));
		$this->addElement($item);
	}
	
	/*public function setAction($action)
    {
		$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseAction = rtrim($baseurl ,'/') . $action;
        parent::setAction($baseAction);
    }*/
}
