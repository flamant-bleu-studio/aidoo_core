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

class seo_Form_seoGenericForm extends CMS_Form_Default
{
    public function init( )
    {
    	$hidden = $this->createElement('hidden', 'generic');
    	$this->addElement($hidden);
    	
    	$title = $this->createElement('text', 'title');
		$title->setLabel(_t('Pages Title'));
		$title->setAttrib('size',80);
		$this->addElement($title);
		
		$keywords = $this->createElement('text', 'keywords');
		$keywords->setLabel(_t('Keywords'));
		$keywords->setDescription("(séparés par des virgules)");
		$keywords->setAttrib('size',80);
		$this->addElement($keywords);

		$description = $this->createElement('text', 'description');
		$description->setLabel(_t('Description'));
		$description->setAttrib('size',80);
		$this->addElement($description);
		
		$item = new CMS_Form_Element_SubmitCustom("submit");
		$item->setValue(_t('Submit'));
		$item->setLabel(_t('Submit'));
		$this->addElement($item);
    }

}
