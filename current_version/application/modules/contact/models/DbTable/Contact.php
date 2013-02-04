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

class Contact_Model_DbTable_Contact extends CMS_Model_MonoLang
{
	protected $_name 		= "contact";
	protected $_primaryKey 	= "id";
	protected $_values 		= array("type", "emails", "emailsCci", "content", "typeSelect", "auto_response", "save_data");

	public function getNbSubmission($id = null)
	{
		$sql = 'SELECT COUNT(DISTINCT(num_send)) FROM 1_contact_save where parent_id = '.$id ;
		return $this->getAdapter()->fetchOne($sql);
	}
	
	public function getKeyInForm($id = null)
	{
		$sql = 'SELECT DISTINCT 1_contact_save.key  FROM 1_contact_save where parent_id = '.$id;
		return $this->getAdapter()->fetchAll($sql);
	}
}