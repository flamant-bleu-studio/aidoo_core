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

class Bloc_Map_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$item = new Zend_Form_Element_Radio('service');
		$item->setLabel(_t('Service'));
		$item->addMultiOptions(array(
				'googlemap' => 'Google Map',
				'mapquest' => 'Map Quest'
		));
		$item->setRequired(true);
		$item->setValue('byForm');
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('apiKey');
		$item->setLabel(_t('API Key '));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Checkbox('getDirections');
		$item->setLabel(_t('Get directions'));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Radio('mode');
		$item->setLabel(_t('Configuration mode'));
		$item->addMultiOptions(array(
			'byForm' => _t('Configure with this form'),
			'byPage' => _t('Retrieve configuration from the displayed page')
		));
		$item->setRequired(true);
		$item->setValue('byForm');
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('latitude');
		$item->setLabel(_t('Latitude '));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('longitude');
		$item->setLabel(_t('Longitude '));
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('zoom');
		$item->setLabel(_t('Zoom'));
		$item->setDescription(_t('From 0 to 18'));
		$item->addValidator('Between', true, array('min'=>0, 'max'=> 18, true));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('mapWidth');
		$item->setLabel(_t('Map width (px)'));
		$item->setRequired(true);
		$this->addElement($item);
		
		$item = new Zend_Form_Element_Text('mapHeight');
		$item->setLabel(_t('Map height (px)'));
		$item->setRequired(true);
		$this->addElement($item);
	}

}