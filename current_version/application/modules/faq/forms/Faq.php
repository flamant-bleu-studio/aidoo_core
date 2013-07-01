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

class Faq_Form_Faq extends CMS_Form_Default 
{
	public function init() 
	{
		
		$faq = new CMS_Form_Element_Text('title');
		$faq->setLabel(_t("Title"));
		$faq->setRequired(true);
		$faq->setTranslatable(true);
		$this->addElement($faq);

		/**
		 * View Access
		 */
		
		$item = new CMS_Acl_Form_ElementViewAccessSelect("access");
		$item->setLabel(_t('Access'));
		$item->setRequired(TRUE);
		$this->addElement($item);
		
		$intro = new CMS_Form_Element_TinyMCE('intro');
		$intro->setLabel(_t("Top content"));
		$intro->setTranslatable(true);
		$this->addElement($intro);
		
		$intro = new CMS_Form_Element_TinyMCE('outro');
		$intro->setLabel(_t("Bottom content"));
		$intro->setTranslatable(true);
		$this->addElement($intro);
	}
}
