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

class Blocs_Form_TemplateOptions extends CMS_Form_Default
{
	public function init()
	{
		$item = new Zend_Form_Element_Text('title');
		$item->setLabel(_t('Title'));
		$item->setDescription(_t('Template title'));
		$item->setRequired(true);
		$this->addElement($item);
		
       	/* ---- START : Thèmes ---- */
       	$item = new Zend_Form_Element_Select('theme');
       	$item->setLabel(_t('Theme'));
       	$item->setDescription(_t('Theme of your template'));
       	$item->addMultiOption('', _t('None'));
		
       	try {
           	$themes = new Zend_Config_Xml(PUBLIC_PATH . '/skins/' . SKIN_FRONT . '/skin.xml', 'templatesThemes');
           	
            if ($themes->theme) {
				$themes = $themes->theme->toArray();
				
				// Reset level for Zend_Config_XML
				if(key($themes) != '0')
					$themes = array($themes);
				
				foreach ($themes as $theme)
					$item->addMultiOption($theme['class'], $theme['name']);
            }
        }
        catch(Exception $e){}
        
        $this->addElement($item);
        /* ---- END : Thèmes ---- */
		
		$item = new Zend_Form_Element_Text('classCss');
		$item->setLabel(_t('CSS Class'));
		$item->setDescription(_t('CSS Class of template'));
		$this->addElement($item);
		
		
		$item = new Zend_Form_Element_Checkbox('defaut');
		$item->setLabel(_t('Default ?'));
		$item->setDescription(_t('Make this template your default template'));
		$this->addElement($item);
		
		/** BACKGROUND **/
		
		$item = new Zend_Form_Element_Select('bgType');
		$item->setLabel(_t('Backgroud type'));
		$item->addMultiOptions(array(
			''	=> _t('None'),
			'1' => _t('Picture'),
			'2' => _t('Single color'),
			'3' => _t('Gradient'),
		));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_MultiUpload('bgPicture');
		$item->setLabel(_t('Picture'));
		$item->setMaxNumberOfFiles(1);
		$item->setAutoUpload(true);
		$item->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif', 'bmp'));
		$item->setUploadPath('templates');
		$this->addElement($item);
		
		$item = new CMS_Form_Element_ColorPicker('bgColor1');
		$item->setLabel(_t('Color 1'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_ColorPicker('bgColor2');
		$item->setLabel(_t('Color 2'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('bgRepeat');
		$item->setLabel(_t('Repeat'));
		$item->addMultiOptions(array(
			'0' => _t('No'),
			'1' => _t('Horizontal'),
			'2' => _t('Vertical'),
			'3' => _t('Both')
		));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('bgGradient');
		$item->setLabel(_t('Orientation'));
		$item->addMultiOptions(array(
			'0' => _t('Vertical') 	. ' ↓',
			'1' => _t('Horizontal') . ' →'
			
		));
		$this->addElement($item);
	}
}