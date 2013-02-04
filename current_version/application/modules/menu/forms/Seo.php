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

class Menu_Form_Seo extends CMS_Form_Default
{
	
    public function init( )
    {

		$item = new CMS_Form_Element_Text('seo_title');
		$item->setLabel(_t('Page title'));
		$item->setTranslatable(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('seo_url_rewrite');
		$item->setLabel(_t('Rewrite Url'));
		$item->setTranslatable(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Textarea('seo_meta_keywords');
		$item->setLabel(_t('Keywords'));
		$item->setDescription(_t('Séparés par des virgules'));
		$item->setAttrib("rows", "4");
		$item->setAttrib("cols", "60");
		$item->setTranslatable(true);
		$this->addElement($item);

		$item = new CMS_Form_Element_Textarea('seo_meta_description');
		$item->setLabel(_t('Description'));
		$item->setAttrib("rows", "4");
		$item->setAttrib("cols", "60");
		$item->setTranslatable(true);
		$this->addElement($item);		

    }
}
