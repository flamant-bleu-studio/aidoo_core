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

abstract class CMS_Model_MonoLang extends CMS_Model_Abstract {

	// Lang_Model_DbTable_MonoLang
	public function get($where = array(), $order = null, $limit = null){
		
		$return = $this->_get($where, $order, $limit);
		
		return $return ? $return : null;
	}
	
	public function getOne($id){
		
		$rows = $this->_get($id);
		
		if($rows)
			$rows = reset($rows);
		
		return $rows ? $rows : null;
	}
	
	private function _get($where = null, $order = null, $limit = null){
		
		$sql = "SELECT * FROM ".$this->getTableName(). " A ";
		
		parent::generateSQL($sql, $where, $order, $limit);

		$return = $this->getAdapter()->fetchAll($sql);
		
		return $return ? $return : null;
	}
	
	public function insert($datas, $autoDate = true){
		foreach($datas as $key => $value){
			if(!in_array($key, $this->_values) && $key != $this->_primaryKey){
				unset($datas[$key]);
			}
		}

		if($this->_disableAutoDate === true)
			$autoDate = false;
		
		return parent::insert($this->_name, $datas, $autoDate);
	}
	
	public function update($datas, $where, $autoDate = true){
		foreach($datas as $key => $value){
			if(!in_array($key, $this->_values) && $key != $this->_primaryKey){
				unset($datas[$key]);
			}
		}
		
		if($this->_disableAutoDate === true)
			$autoDate = false;
		
		return parent::update($this->_name, $datas, $where, $autoDate);
	}
	
	public function delete($where){
		return parent::delete($this->_name, $where);
	}
	
}