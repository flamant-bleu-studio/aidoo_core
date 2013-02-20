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

class CMS_Acl_DbTable_ViewAccess {   

	public function getAllViewAccess ()
	{
		global $multi_site_prefix;
		
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT id, name, groups
								FROM " . DB_TABLE_PREFIX . $multi_site_prefix."view_access");
	    
		return $results->fetchAll(Zend_Db::FETCH_OBJ);
	}
	
	public function getViewAccess ($id = null)
	{
		global $multi_site_prefix;
		
		if($id === null)
			throw new Zend_Exception(_t('Missing parameter'));
			
		$id = (int) $id;
		
		$db = Zend_Registry::get('db');
	    $results = $db->query("SELECT id, name, groups
								FROM " . DB_TABLE_PREFIX . $multi_site_prefix."view_access
								WHERE id = ?", array($id));
	    
	    $viewAccess = $results->fetch(Zend_Db::FETCH_OBJ);
	    $viewAccess->groups = json_decode($viewAccess->groups);
	    
		return $viewAccess;
	}
	
	public function addViewAccess ($name = null, $datas = null)
	{
		global $multi_site_prefix;
		
		if($name === null || $datas === null)
			throw new Zend_Exception(_t('Missing parameter'));
		
		$db = Zend_Registry::get('db');
		
	    $db->insert(DB_TABLE_PREFIX . $multi_site_prefix.'view_access', array(
	    	"name" 		=> $name,
	    	"groups" 	=> json_encode($datas)
	    ));
	    
	}
	
	public function updateViewAccess ($id = null, $name = null, $datas = null)
	{
		global $multi_site_prefix;
		
		if($id === null || $name === null || $datas === null)
			throw new Zend_Exception(_t('Missing parameter'));
			
		$id = (int) $id;		

		$db = Zend_Registry::get('db');
		
	    $db->update(
	    	DB_TABLE_PREFIX . $multi_site_prefix.'view_access', 
	    	array(
		    	"name" 		=> $name,
		    	"groups" 	=> json_encode($datas)
	    	), 
	    	"id = ".$id
	    );
	    
	}
	
	public function delViewAccess ($id = null)
	{
		global $multi_site_prefix;
		
		if($id === null)
			throw new Zend_Exception(_t('Missing parameter'));
			
		$id = (int) $id;
		
		$db = Zend_Registry::get('db');
	    $db->delete(DB_TABLE_PREFIX . $multi_site_prefix."view_access", "id = ".$id);

	}
}
