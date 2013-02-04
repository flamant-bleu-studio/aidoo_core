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

class Admin_Model_DbTable_Lang extends CMS_Db_Table_Abstract {
	

	public function listLangTable() {
	
	    return $this->getAdapter()->fetchAll("SHOW TABLES LIKE '%_lang' ", null, zend_db::FETCH_COLUMN);
	}
	
	public function duplicateLang($table_name, $from_id_lang, $to_id_lang){
		
		$from_id_lang = (int)$from_id_lang;
		$to_id_lang = (int)$to_id_lang;
		
		$db = $this->getAdapter();
		
		$rows = $db->fetchAll("SELECT * FROM " . $table_name . " WHERE id_lang = ? ", array($from_id_lang));
		
		foreach($rows as $row){
			
			if(strrpos($table_name, "core_pages_lang") !== false)
				unset($row["url_rewrite"]);
			
			$row["id_lang"] = $to_id_lang;
			
			$db->insert($table_name, $row);
			
		}
		
	}
	
	public function deleteLang($table_name, $id_lang){
		
		$id_lang = (int)$id_lang;
		
		$db = $this->getAdapter();
	
		$db->delete($table_name, $db->quoteInto("id_lang = ?", $id_lang));
			
	}

}