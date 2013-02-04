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

class Jobs_Form_Jobs extends CMS_Form_Default
{
	
	public function init()
	{		

		$item = $this->createElement('text', 'job_title');
		$item->setLabel('Titre');
		$item->setRequired(true);
		$this->addElement($item);
				
		$item = $this->createElement('text', 'contract_type');
		$item->setLabel('Type du contrat');
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = $this->createElement('text', 'sector');
		$item->setLabel('Secteur géographique');
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = $this->createElement('text', 'domain');
		$item->setLabel('Domaine');
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = $this->createElement('text', 'contact');
		$item->setLabel('Email de contact');
		$item->addValidator(new Zend_Validate_EmailAddress());
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new CMS_Form_Element_TinyMCE('description');
		$item->setAttrib('id', 'description');
		$item->setLabel(_t("Job Description"));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom('save');
		$item->setValue(_t('Save'));
		$item->setLabel(_t('Save'));
		$this->addElement($item);
		
		$item = new CMS_Form_Element_SubmitCustom('savequit');
		$item->setValue(_t('Save & Quit'));
		$item->setLabel(_t('Save & Quit'));
		$this->addElement($item);
		
	}	
}
