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

class Users_Model_DbTable_UsersMeta extends CMS_Db_Table_Abstract {   
	
	protected $_name = 'user_metas';
	
	public function getUserMeta ($userId, $key)
	{
    	$select = $this->select();
    	$select = $select->where("user_id='$userId' AND meta='$key'");
    	$row = $this->fetchRow($select);
    	
    	if($row)
	   		return $row->value;
	   	else
	   		return null;
	}
	
	public function getAllUserMeta ($userId)
	{
		return $this->getAdapter()->fetchPairs("SELECT meta, value FROM " . DB_TABLE_PREFIX . "user_metas WHERE user_id = ?", array($userId));
	}

	public function addUserMeta ($userId, $key, $value)
	{
    	$select = $this->select();
    	$select = $select->where("user_id='$userId' AND meta='$key'");
    	$row = $this->fetchRow($select);
	    if(!$row) {
		    $row = $this->createRow();
		    $row->user_id 	= $userId;
	    }
	    $row->meta 	= $key;
	    $row->value = $value;
	    $row->save();
	    return true;
	}
	
	public function updateUserMeta ($userId, $key, $value)
	{
    	$select = $this->select();
    	$select = $select->where("user_id='$userId' AND meta='$key'");
    	$row = $this->fetchRow($select);
	    if(!$row) {
		    $row = $this->createRow();
		    $row->user_id 	= $userId;
	    }
	    $row->meta 	= $key;
	    $row->value = $value;
	    $row->save();
	    return true;
	}
	
	public function removeUserMeta($userId, $key)
	{
    	if($key)
    	{
    		$where = array(
	    		$this->getAdapter()->quoteInto("user_id = ?", $userId),
	    		$this->getAdapter()->quoteInto("meta = ?", $key)
    		);
    	}
    	else
    		$where = $this->getAdapter()->quoteInto("user_id = ?", $userId);
    		
    	if($this->delete($where))
    		return true;
    	
    	return false;
	}	
	
	public function searchUserMeta($value){
		$db = $this->getAdapter();
		
		return $db->fetchCol("SELECT user_id FROM " . DB_TABLE_PREFIX . "user_metas WHERE value LIKE ? ", array("%".$value."%"));
	}
	
	public function getIDFromMetaKey ($meta, $value)
	{
    	$select = $this->select();
    	$where = $select->where("meta='$meta' AND value='$value'");
	    $result = $this->fetchRow($where);
    	return $result;
	}
	
	
}