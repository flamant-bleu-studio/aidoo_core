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

class Bloc_Diaporama_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new Zend_Form_Element_Select('mode');
		$item->setLabel(_t('Selection mode'));
		$item->addMultiOptions(array( 'specific' => _t('Set a slideshow') , 'byPage' => _t('Current page defines the slideshow')));
		$this->addElement($item);
		
		$diaporamas = GalerieImage_Object_Galerie::get( array( 'type' => GalerieImage_Object_Galerie::TYPE_DIAPORAMA ) );
		$options = array();
		if( $diaporamas )
			foreach ( $diaporamas as $diaporama )
				$options[$diaporama->id] = $diaporama->title;

		$item = new Zend_Form_Element_Select('diaporamaId');
		$item->setLabel(_t('Diaporama'));
		$item->addMultiOptions($options);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select('diaporamaIdPage');
		$item->setLabel(_t('Default diaporama'));
		$item->addMultiOption('', _t('None'));
		$item->addMultiOptions($options);
		$this->addElement($item);
				
		$item = new Zend_Form_Element_Select('bx_type');
		$item->setLabel(_t('Type d\'animation'));
		$item->addMultiOptions(array(
				'fade' 			=> 'Fading',
				'horizontal'  	=> 'Slide horizontal',
				'vertical'  	=> 'Slide vertical',
				'ticker'  		=> 'Défilement doux'
		));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_Text('pause');
		$item->setLabel(_t('Pause (in ms) between each transition'));
		$item->setValue(4000);
		$item->setSuffix('ms');
		$item->addValidator(new Zend_Validate_Int());
		$item->setRequired(true);
		$this->addElement($item);
		
		
		$item = new Zend_Form_Element_Checkbox('pagination');
		$item->setLabel(_t('Active pagination'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('displaySlideQty');
		$item->setLabel(_t('Number of slides to display'));
		$item->addValidator(new Zend_Validate_Int());
		$item->setRequired(true);
		$item->setValue(1);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('moveSlideQty');
		$item->setLabel(_t('Number of slides to move'));
		$item->addValidator(new Zend_Validate_Int());
		$item->setRequired(true);
		$item->setValue(1);
		$this->addElement($item);
	}
}