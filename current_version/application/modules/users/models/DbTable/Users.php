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

class Users_Model_DbTable_Users  extends CMS_Db_Table_Abstract {   

	
	// New ***
	
	public function get($filters = null)
	{
		$sql 	= "SELECT id FROM " . DB_TABLE_PREFIX . "users";
		$params = array();
		
		if ($filters){
			list($sqlCond, $params) = self::generateFiltersSQL($filters);
			$sql .= $sqlCond;	
		}

		$return = $this->getAdapter()->fetchCol($sql, $params); 
		
		if(!empty($return))
			return $return;
		else
			return null;
	}
	
	public function getNext($id, $where) {
		
		$sql = "SELECT MIN(id) FROM " . DB_TABLE_PREFIX . "users";
		
		$params 	= array();
			
		if ($where){
			list($sqlCond, $params) = self::generateFiltersSQL($where);
			$sql .= $sqlCond;	
		}
		
		$sql .= " AND id > ?";
		$params[] = $id;
		
		$return = $this->getAdapter()->fetchOne($sql, $params);
		
		return !empty($return) ? $return : null;
	}
	
	public function getPrev($id, $where) {
		
		$sql = "SELECT MAX(id) FROM " . DB_TABLE_PREFIX . "users";
		
		$params 	= array();
			
		if ($where){
			list($sqlCond, $params) = self::generateFiltersSQL($where);
			$sql .= $sqlCond;	
		}
		
		$sql .= " AND id < ?";
		$params[] = $id;
		
		$return = $this->getAdapter()->fetchOne($sql, $params);
		
		return !empty($return) ? $return : null;
	}
	
	private function generateFiltersSQL($filters = null){

		$sql = "";
		$params = array();
		
		// FILTRES
		if($filters)
	    {
			$sql .= " WHERE 1";
		
			$valid_filters = array("id", "id_facebook", "email", "group");

			foreach ($filters as $key_filter => $value_filter)
			{		
				if(in_array($key_filter, $valid_filters) && $value_filter) {
				
					$key_filter = $this->getAdapter()->quoteIdentifier($key_filter);
					
					$sql .= " AND (";
					
					if(is_array($value_filter)){
						
						if(count($value_filter) > 1) {
							foreach($value_filter as $subvalue) {
								$sql .= " ".$key_filter . " = ? OR";
								$params[] = $subvalue;
							}
							$sql .= " 0";
						}
						else {
							$sql .= " ".$key_filter . " = ?";
							$params[] = $value_filter[0];
						}
					}
					else {
						$sql .= " ".$key_filter . " = ?";
						$params[] = $value_filter;
					}
					
					$sql .= " )";
				}
			}
			
			// LIMIT & OFFSET
			if($filters['offset'] || $filters['limit'] ){
				if($filters['limit'] && !$filters['offset'])
					$sql .= " LIMIT ".$filters['limit'];
				elseif(!$filters['limit'] && $filters['offset'])
					$sql .= " LIMIT ".$filters['offset'].", 999999999999";
				elseif($filters['limit'] && $filters['offset'])
					$sql .= " LIMIT ".$filters['offset'].", ".$filters['limit'];
			}
			
		}
		
		return array($sql, $params);
	}
	
	public function createEntity($datas){
		
		$id = (int)$id;
		
	    $inserted = $this->getAdapter()->insert(
			DB_TABLE_PREFIX . "users",
			array( 	
				'id_facebook'	=> $datas['id_facebook'],
				'group'			=> $datas['group'],
				'email' 		=> $datas['email'],
				'password'  	=> $datas['password_encrypt'] ? sha1($datas['password']) : $datas['password'],
				'isActive'		=> $datas['isActive'],
				'isConfirm'		=> $datas['isConfirm'],
				'civility'		=> $datas['civility'],
				'firstname'		=> $datas['firstname'],
				'lastname'		=> $datas['lastname'],
				'username'		=> $datas['username'],
				'date'			=> date('Y-m-d H:i:s'),
				'date_update' 	=> date('Y-m-d H:i:s')
			)
		);
		
		if($inserted == 1)
			return $this->getAdapter()->lastInsertId("users");
		else
			return null;
		
	}
	public function updateEntity($id, $datas){
		
		$id = (int)$id;

	    $updated = $this->getAdapter()->update(
			DB_TABLE_PREFIX . "users",
			array( 	
				'id_facebook'	=> $datas['id_facebook'],
				'group'			=> $datas['group'],
				'email' 		=> $datas['email'],
				'civility'		=> $datas['civility'],
				'firstname'		=> $datas['firstname'],
				'lastname'		=> $datas['lastname'],
				'username'		=> $datas['username'],
				'isActive'		=> $datas['isActive'],
				'isConfirm'		=> $datas['isConfirm'],
				'date_update' 	=> date('Y-m-d H:i:s')
			),
			'id = '. $id 
		);
		
		if($updated == 1)
			return true;
		else
			return false;
		
	}
	
	// *** New
	
	public function updatePassword($userId, $password){
		
		$id = (int)$userId;

		$db = Zend_Registry::get('db');
	    $updated = $db->update(
			DB_TABLE_PREFIX . "users",
			array( 	
				'password' 	=> sha1($password)
			),
			'id = '. $id 
		);
			
		return true;
	}
	
	/**
	 * Return all Users rows (without superadmin)
	 *
	 * @return Zend_Db_Table_Rowset Users_Model_DbTable_UserRow
	 */
	public function getAll()
	{
				
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT id FROM " . DB_TABLE_PREFIX . "users");
	    
	    $results = $results->fetchAll(Zend_DB::FETCH_COLUMN);
	    
	    if(count($results) > 0)
	    {
		    $users = array();
		    
		    foreach($results as $userId)
		    {
		    	$users[] = new Users_Object_User($userId);
		    }
		
			return $users;
	    }
		else
			return null;
	}
	
	public function count($filters)
	{
		
		$sql 	= "SELECT count(id) FROM " . DB_TABLE_PREFIX . "users";
		$params = array();
		
		if ($filters){
			list($sqlCond, $params) = self::generateFiltersSQL($filters);
			$sql .= $sqlCond;
		}
	
		return (int)$this->getAdapter()->fetchOne($sql, $params);
	}
	
	/**
	 * Return Users rows
	 *
	 * @return Users_Model_DbTable_UserRow
	 */
	public function getUser($id = null){
		
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT * FROM " . DB_TABLE_PREFIX . "users WHERE id = ?", array($id));
	    
	    $result = $results->fetch(Zend_DB::FETCH_OBJ);

	    return $result;
	}
	
	
	/**
	 * Return Users according to email
	 *
	 * @return Users_Model_DbTable_UserRow
	 */
	public function getUserByEmail($email)
	{
		$db = Zend_Registry::get('db');

	    $results = $db->query("SELECT * FROM " . DB_TABLE_PREFIX . "users WHERE email = ?", array($email));
	    $result = $results->fetch(Zend_DB::FETCH_OBJ);
	    return $result;
	}
	
	public function deleteUser($id = null)
	{
		// Sécurisation
		$id = (int) $id;
		if(!$id)
			throw new Zend_Exception('Missing parameter');

		// Suppression
		$db = Zend_Registry::get('db');
		$db->delete(DB_TABLE_PREFIX . "users",'id = '. $id );
	}

	public function getPassword($id)
	{
		$db = Zend_Registry::get('db');
		
	    $results = $db->query("SELECT password FROM " . DB_TABLE_PREFIX . "users WHERE id = ?", array($id));
	    $result = $results->fetch(Zend_DB::FETCH_OBJ);
	    return $result;
	}
}