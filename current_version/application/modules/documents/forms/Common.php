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

class Documents_Form_Common extends CMS_Form_Default
{
	public function init()
	{
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		/**
		 * Content Type
		 */
		
		$item = new Zend_Form_Element_Hidden('type');
		$this->addElement($item);
		
		/**
		 * Status
		 */
		
		$item = new Zend_Form_Element_Select('status');
		$item->setLabel(_t('Status'));
		$item->addMultiOptions(array('0'=>_t('Drafted'),'1'=>_t('Published')));
		$this->addElement($item);
		
		
		/**
		 * View Access
		 */
		
		$item = new CMS_Acl_Form_ElementViewAccessSelect("access");
		$item->setLabel(_t('Access'));
		$item->setRequired(TRUE);
		$this->addElement($item);
		
		/**
		 * Content Title
		 */
		
		$item = new CMS_Form_Element_Text('title');
		$item->setLabel(_t('Title'));
		$item->setRequired(true);
		$item->setTranslatable(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t('Save'));
		$item->setLabel(_t('Save'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom("submitandquit");
		$item->setValue(_t('Save & Quit'));
		$item->setLabel(_t('Save & Quit'));
		$this->addElement($item);
	}
}