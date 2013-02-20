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

class CMS_Acl_DbTable_Users {   

	public function authenticate ($gid, $email)
	{	
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT id FROM " . DB_TABLE_PREFIX . "users WHERE isActive = 1 AND `group` = ? AND email = ?", array($gid, $email));
	    
		return $results->fetch(Zend_Db::FETCH_OBJ);
	}
	
}
