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

abstract class CMS_Model_Abstract extends CMS_Db_Table_Abstract {
	
	protected $_disableAutoDate = false;
	
	//Lang_Model_DbTable_Base
	protected function generateSQL(&$sql, $where, $order, $limit) {
		$this->appendWhereClause($sql, $where);
		$this->appendOrderClause($sql, $order);
		$this->appendLimitClause($sql, $limit);
	}
	
	protected function appendWhereClause(&$sql, $where, $appendKeyword = true){
		
		if(!empty($where)){

			$db = $this->getAdapter();
			$sql .= ($appendKeyword) ? " WHERE " : " ";
			
			
			if(is_int($where)){
				$sql .= $db->quoteInto($this->getPrimaryKeyName()." = ?", $where);
			}
			else if(is_array($where) && !empty($where)){
				foreach($where as $key => $val){
					
					// Si c'est un array : condition sql en tant que première case + valeurs bind cases suivantes
					if(is_array($val)){
						
						$sql .= " (".$val[0].") AND ";
						unset($val[0]);

						if(is_array($val) && !empty($val))
							foreach($val as $v)
								$sql = $db->quoteInto($sql, $v, null, 1);
					
					
					}
					else {		
						$sql .= $db->quoteInto($key." = ? AND ", $val);
					}
				}
				
				$sql .= "1 ";
			}
		}
	}

	protected function appendOrderClause(&$sql, $order, $appendKeyword = true){

		if(!empty($order)){
			
			$db = $this->getAdapter();
			$sql .= ($appendKeyword) ? " ORDER BY " : " ";
			
			// $order = une colonne
			if(is_string($order)){
				$sql .= $db->quoteIdentifier($order);
			}
			// $order = tableau de colonne
			else if(is_array($order) && !empty($order)){
				
				$count 	= count($order);
				$i 		= 1;
				
				foreach($order as $key => $val){
	
					/*
					 * pas de clé, la valeur définie la colonne à ordonner
					 * $order = array("title", ....
					 */
					if(is_int($key)){
						$sql .= $db->quoteIdentifier($val);
					}
					/*
					 * la clé définie la colonne, la valeur définie l'ordre
					 * $order = array("title" => "DESC", ....
					 */
					else {
						
						$val = strtoupper($val);
						
						if($val != "ASC" && $val != "DESC")
							throw new Exception("Invalid order SQL");
						
						$sql .= $db->quoteIdentifier($key)." ".$val;
					}
					
					// Gestion de la dernière virgule en cas de multi order
					if($i < $count){
						$sql .= ", ";
					}
					$i++;
				}
				
			}
		}
	}
	
	protected function appendLimitClause(&$sql, $limit, $appendKeyword = true){
		
		if(!empty($limit)){
			
			$sql .= ($appendKeyword) ? " LIMIT " : " ";
				
			// Si entier seul : $limit = nombre d'élements voulu
			if(is_int($limit)){
				$sql .= $limit;
			}
			else if(is_array($limit)){
				
				$db = $this->getAdapter();
				
				// Juste un nombre d'élément
				if($limit['limit'] && !$limit['offset']){
					$sql .= $db->quoteInto("?", $limit['limit']);
				}
				// juste un offset
				elseif(!$limit['limit'] && $limit['offset']){
					$sql .= $db->quoteInto("?, 999999999999", $limit['offset']);
				}
				// Nombre + offset
				elseif($limit['limit'] && $limit['offset']){
					$sql .= $db->quoteInto("?, ", $limit['offset']);
					$sql .= $db->quoteInto("?", $limit['limit']);
				}
				else {
					throw new Exception("Paramètres LIMIT SQL invalides");
				}
			}
			else {
				throw new Exception("Paramètres LIMIT SQL invalides");
			}
		}
		
		
	}

	public function count($where = array()){
		
		$sql = 'SELECT count(DISTINCT('.$this->_primaryKey.')) FROM '.$this->_name;
		
		self::appendWhereClause($sql, $where);
		
		return $this->getAdapter()->fetchOne($sql, null, zend_db::FETCH_COLUMN);
	}
	
	/**
	 * Insert new Row in database
	 * 
	 * @param string $table Nom de la table
	 * @param array $values Tableau associatif des valeurs à insérer
	 * @param bool $autoDate La table contient elle les champs date_add et date_upd
	 */
	public function insert($table, $values, $autoDate = true){
		
		if($autoDate == true){
			$values["date_add"] = date('Y-m-d H:i:s');
			$values["date_upd"] = date('Y-m-d H:i:s');
		}
		
		$db = $this->getAdapter();
		if($db->insert($table, $values) != 1)
			return false;
			
		return $db->lastInsertId($table);
	}
	
	/**
	 * Update existing Row in database
	 * 
	 * @param string $table Nom de la table
	 * @param array $values Tableau associatif des valeurs à insérer
	 * @param bool $autoDate La table contient elle le champs date_upd
	 */
	public function update($table, $values, $where, $autoDate = true){
		
		$db = $this->getAdapter();
		
		if(!empty($where)){
			$this->appendWhereClause($sql, $where, false, false);
		}
		else {
			throw new Exception("Aucune condition spécifiée");
		}
		
		if($autoDate == true){
			$values["date_upd"] = date('Y-m-d H:i:s');
		}
		
		return $db->update($table, $values, $sql);
	}
	
	public function delete($table, $where){
		
		$db = $this->getAdapter();
		
		if(!empty($where)){
			$this->appendWhereClause($sql, $where, false);
		}
		else {
			throw new Exception("Aucune condition spécifiée");
		}
		
		return $db->delete($table, $sql);
	}
	
	/**
	 * Retourne le nom de la clé primaire
	 */
	public function getPrimaryKeyName(){
		return $this->_primaryKey;
	}
		
}