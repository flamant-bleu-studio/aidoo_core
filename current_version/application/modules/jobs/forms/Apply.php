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

class Jobs_Form_Apply extends CMS_Form_Default
{
	
	public function __construct ($uploadPath, $job=null, $options=null)
	{
		parent::__construct($options);
		
		$this->setAttrib('enctype', 'multipart/form-data');

		$object = $this->createElement('hidden', 'object');
		
		if ($job)
			$object->setValue($job->job_title);
		else
			$object->setValue("Libre");
		
		$this->addElement($object);
		
		
		$item = new Zend_Form_Element_Select("civilite");
		$item->addMultiOptions(array(
			"" => "Sélectionner",
			"M." => "Monsieur",
			"Mme" => "Madame",
			"Melle" => "Melle"
		));
		$item->setLabel("Civilité");
        $item->setRequired(true);
        $this->addElement($item);
		
		$item = $this->createElement('text', 'firstName');
		$item->setLabel('Prénom');
		$item->setAttrib('size',30);
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = $this->createElement('text', 'lastName');
		$item->setLabel('Nom');
		$item->setAttrib('size',30);
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = $this->createElement('textarea', 'adress');
		$item->setAttrib('cols', '45');
		$item->setAttrib('rows', '5');
		$item->setLabel('Adresse');
		$this->addElement($item);
		
		$item = $this->createElement('text', 'cp');
		$item->setLabel('Code postal');
		$item->setAttrib('size',30);
		$item->addValidator(new Zend_Validate_PostCode("fr_FR"));
		$this->addElement($item);
		
		$item = $this->createElement('text', 'city');
		$item->setLabel('Ville');
		$item->setAttrib('size',30);
		$this->addElement($item);
		
		$item = $this->createElement('text', 'phone');
		$item->setLabel('Tél. portable');
		$item->setAttrib('size',30);
		$item->addValidator(new CMS_Validate_Phone());
		$this->addElement($item);
		
		$item = $this->createElement('text', 'email');
		$item->setLabel('Email');
		$item->setAttrib('size',30);
		$item->addValidator(new Zend_Validate_EmailAddress());
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = $this->createElement('textarea', 'message');
		$item->setLabel('Message');
		$item->setAttrib('cols', '45');
		$item->setAttrib('rows', '10');
		$this->addElement($item);
		
		$item = new Zend_Form_Element_File('cv');
		$item->setLabel('CV');
		$item->setDisableLoadDefaultDecorators(false);
		//$item->setDestination(CMS_PATH . $uploadPath);
		$item->addValidator('Count', false, 1);
		$item->addValidator('Extension', false, array('zip','doc','pdf','rtf','docx','odt'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Submit('submit');
		$item->setValue('Envoyer');
		$this->addElement($item);
	}

}