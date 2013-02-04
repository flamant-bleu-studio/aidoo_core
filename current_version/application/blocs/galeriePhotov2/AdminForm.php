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

class Bloc_GaleriePhotov2_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new Zend_Form_Element_Radio("typeDiapo");
		$item->setLabel(_t("Select type galerie"));
		$item->addMultiOptions(array( "diaporama"=>"Fixer une galerie" , "diaporamaPage"=>"Laisser la page choisir sa galerie" ));
		$this->addElement($item);
        
		$diaporamas = GalerieImage_Object_Galerie::get( array( "type" => GalerieImage_Object_Galerie::TYPE_GALERIE ) );
		$options = array();
        //print_r($diaporamas);die();
		if( $diaporamas )
		{
			foreach ( $diaporamas as $diaporama )
			{
				$options[$diaporama->id] = $diaporama->title;
			}
		}
		$item = new Zend_Form_Element_Select("diaporamaId");
		$item->setLabel(_t("Select galerie"));
		$item->setDescription("");
		$item->addMultiOptions($options);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("diaporamaIdPage");
		$item->setLabel(_t("Select default diaporama"));
		$item->setDescription("");
		$item->addMultiOption("null", "Aucun");
		$item->addMultiOptions($options);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Select("paginationActive");
		$item->setLabel(_t("Active pagination"));
		$item->setDescription("");
		$item->addMultiOptions(array(1 => _t("yes"), 0 => _t("no")));
		$this->addElement($item);
	}
}