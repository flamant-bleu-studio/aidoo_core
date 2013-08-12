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

class Diaporama_Form_AddImage extends CMS_Form_Default
{
    public function init()
    {
		$images = new CMS_Form_Element_MultiUpload('images');
		$images->setAllowedExtensions(array("jpg", "jpeg", "png", "gif", "bmp"));
		$images->setUploadPath("diaporama");
		$images->setAutoUpload(true);
		$images->setSortable(true);
    	$this->addElement($images);
		
		$save = new CMS_Form_Element_SubmitCustom('save');
		$save->setValue(_t('Save'));
		$save->setLabel(_t('Save'));
		$this->addElement($save);
    }
}