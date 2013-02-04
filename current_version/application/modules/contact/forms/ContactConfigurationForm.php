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

class Contact_Form_ContactConfigurationForm extends CMS_Form_Default
{
    public function init( )
    {
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Votre email : ');
		$email->setRequired(TRUE);
		$email->setAttrib('size',50);
		$this->addElement($email);
		
		$qualite = new Zend_Form_Element_Text('qualite');
		$qualite->setLabel('Qualité : ');
		$qualite->setRequired(TRUE);
		$qualite->setAttrib('size',50);
		$this->addElement($qualite);
		
		$actif = new Zend_Form_Element_Hidden('actif');
		$this->addElement($actif);
		
		$submit = $this->addElement('submit', 'submit', array('label' => 'Modifier'));	
    }	
}
