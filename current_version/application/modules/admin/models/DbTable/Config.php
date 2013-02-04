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

class Admin_Model_DbTable_Config extends CMS_Db_Table_Abstract {
	
	protected $_name = 'config';
	
	public function getAll()
	{

	    $select = $this->select();
	    $rows = $this->fetchAll($select);
	   
		if($rows->count() > 0)
			return $rows;
		else
			return null;
	}
	
	public function getAllPDO()
	{
   		$sql = "SELECT name, value FROM ".$this->getTableName();
		
	    return $this->getAdapter()->fetchAll($sql, null, PDO::FETCH_OBJ);
	}
	
	public function setConfigItem($name, $value)
	{
	    // fetch the row if it exists
	    $select = $this->select();
	    $select->where("name = ?", $name);
	    $row = $this->fetchRow($select);
	    
	    //if it does not then create it
	    if(!$row) 
	    {
	        $row = $this->createRow();
	        $row->name = $name;
	    }
	    
	    //set the content
	    $row->value = $value;
	    $row->save();
	}

}