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

class Bloc_BlocResponsive_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new CMS_Form_Element_ColorPicker('background_color');
		$item->setLabel(_t('Background color'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_MultiUpload('background_image');
		$item->setMaxNumberOfFiles(1);
		$item->setLabel(_t('Background image'));
		$item->setUploadPath('others');
		$item->setAllowedExtensions(array('jpg', 'png', 'jpeg'));
		$item->setAutoUpload(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('text');
		$item->setLabel(_t('Text'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_ColorPicker('background_text');
		$item->setLabel(_t('Background text'));
		$item->setDescription(_t('Opacity in front'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_ColorPicker('text_color');
		$item->setLabel(_t('Text color'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('icon');
		$item->addMultiOption(0, _t('None'));
		$directoryIcons = new DirectoryIterator(PUBLIC_PATH . '/skins/'.SKIN_FRONT.'/icon/');
		foreach ($directoryIcons as $file) {
			if ( !$file->isDir() ) {
				$name = substr($file->getFileName(), 0, strlen($file->getFileName())-4);
				$icons[] = array($name => $name);
				$item->addMultiOption($name, $name);
			}
		}
		
		$item->setLabel(_t('Icon'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Checkbox('load_ajax');
		$item->setLabel(_t('Load ajax ?'));
		$this->addElement($item);
		
		$this->addLinkElements();
	}
}