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

class Bloc_Contactv2_AdminForm extends CMS_Bloc_ParentForm
{
	public function init()
	{
		parent::init();
		
		$types = array();
		
		$folderTypes = realpath(PUBLIC_PATH.'/skins/'.SKIN_FRONT.'/core_features/content_types/contact/');
		$directory = new DirectoryIterator($folderTypes);
		
		$contactType = array();
		$notConfig = array();
		
		if( count($directory) > 0 )
		{
			/** Extract Type **/
			foreach($directory as $file)
			{
				if (!$file->isDot())
				{
					if( file_exists($folderTypes."/".$file->getFilename()."/type.xml") )
					{
						$xml = new Zend_Config_Xml($folderTypes."/".$file->getFilename()."/type.xml");
						if( $xml->bloc && $xml->bloc == "true" )
							$contactType[$xml->name] = $xml->name;
						$object = Contact_Object_Contact::get(array("type" => $xml->name));
						if( !$object->emails && !$object->emailsCci )
							$notConfig[] = $xml->name;
					}
				}
			}
		}

		$view = Zend_Layout::getMvcInstance()->getView();
		$view->notConfig = $notConfig;
		
		$item = new Zend_Form_Element_Select("type_form");
		$item->setLabel(_t("Choose type form contact"));
		$item->setDescription(_t("List type form contact available to bloc"));
		$item->setRequired(true);
		$item->addMultiOptions($contactType);
		$this->addElement($item);
	}
}