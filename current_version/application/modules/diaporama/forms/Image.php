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

class Diaporama_Form_Image extends CMS_Form_Default
{
	public function init()
    {
		parent::addLinkElements();
    	
		$description = new CMS_Form_Element_TinyMCE('text');
		$description->setLabel(_t('Text'));
		$this->addElement($description);
		
		$bg_color_image = new CMS_Form_Element_ColorPicker('background_color');
		$bg_color_image->setLabel(_t('Background Color'));
		$bg_color_image->setDescription(_t('Background Color of the image'));
		$this->addElement($bg_color_image);
		
		$save = new CMS_Form_Element_SubmitCustom("save");
		$save->setLabel(_t('Save'));
		$save->setValue(_t('Save'));
		$this->addElement($save);
    }
}