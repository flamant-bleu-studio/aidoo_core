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

class Users_Model_DbTable_Group {   

	public function getGroupByGroupName ($group_name = null)
	{
		if(!$group_name)
			throw new Exception('This methode require one parameter');
		
		$db = Zend_Registry::get('db');
		
	    $results = $db->query("SELECT id, name
								FROM user_groups
								WHERE name = ?", array($group_name));
	    
		return $results->fetch(Zend_Db::FETCH_OBJ);
	}
	
	public function getGroupByGroupId ($group_id = null)
	{
		if(!$group_id)
			return null;
				
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT id, name 
								FROM user_groups
								WHERE id = ?", array($group_id));
	    
		return $results->fetch(Zend_Db::FETCH_OBJ);
	}
	
	public function getAllGroups ()
	{
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT node.name, node.id AS id, (COUNT(parent.name) - 1) AS level
								FROM user_groups AS node, user_groups AS parent
								WHERE node.lft BETWEEN parent.lft AND parent.rgt
								GROUP BY node.name
								ORDER BY node.lft");
	    
		$return = $results->fetchAll(Zend_Db::FETCH_OBJ);

	    return $return;
	}

	public function getGroupChildren ($parent_id = null)
	{
		if(!$parent_id)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$db = Zend_Registry::get('db');
	    $results = $db->query("	SELECT node.id
								FROM user_groups AS node, user_groups AS parent
								WHERE node.lft BETWEEN parent.lft AND parent.rgt
								AND parent.id = ?", array($parent_id));
	    
		return $results->fetchAll(Zend_Db::FETCH_COLUMN);
	}
	
	public function add($datas){
		
		if($datas['name'] === null || $datas['parent'] === null)
			throw new Zend_Exception(_t('Missing parameter'));
			
		// Sécurisation
		$parent_id 	= (int) $datas['parent'];
		
		$db = Zend_Registry::get('db');

		$db->beginTransaction();

		try {
			
			$db->query("SELECT @myLeft := lft FROM user_groups WHERE id = ?", $parent_id);
		    $db->query("UPDATE user_groups SET rgt = rgt + 2 WHERE rgt > @myLeft");
		    $db->query("UPDATE user_groups SET lft = lft + 2 WHERE lft > @myLeft");
		    $db->query("INSERT INTO user_groups (lft, rgt, name) VALUES (@myLeft + 1, @myLeft + 2, ?)", array($datas['name']));

		    $db->commit();
		    
		} catch (Exception $e) {
			
		    $db->rollBack();
		    throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
		    
		}
		
		return $db->lastInsertId();
	}
	public function delGroup($id = null){
		
		$id = (int)$id;
		
		if(!$id)
			throw new Zend_Exception(_t('Missing parameter'));

		$db = Zend_Registry::get('db');

		$db->beginTransaction();

		try {

			$db->query("SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft +1 FROM user_groups WHERE id = ?", $id);
			$db->query("DELETE FROM user_groups WHERE lft = @myLeft");
			
		 	$db->query("UPDATE user_groups SET rgt = rgt - 1, lft = lft - 1 WHERE lft BETWEEN @myLeft AND @myRight");
		    $db->query("UPDATE user_groups SET rgt = rgt - 2 WHERE rgt > @myRight");
		    $db->query("UPDATE user_groups SET lft = lft - 2 WHERE lft > @myRight");
	
		    $db->commit();
		    
		} catch (Exception $e) {
			
		    $db->rollBack();
		    throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
		    
		}
	}	
	
	
	public function delGroupRecursive($id = null){
		
		if(!$id)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$db = Zend_Registry::get('db');

		$db->beginTransaction();

		try {

			$db->query("SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft +1 FROM user_groups WHERE id = ?", $id);
			$db->query("DELETE FROM user_groups WHERE lft BETWEEN @myLeft AND @myRight");
		    $db->query("UPDATE user_groups SET rgt = rgt - @myWidth WHERE rgt > @myRight");
		    $db->query("UPDATE user_groups SET lft = lft - @myWidth WHERE lft > @myRight");
	
		    $db->commit();
		    
		} catch (Exception $e) {
			
		    $db->rollBack();
		    throw new Zend_Db_Exception($e->getMessage(), $e->getCode());
		    
		}
	}
	
}