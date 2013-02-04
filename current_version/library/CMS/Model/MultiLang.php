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

abstract class CMS_Model_MultiLang extends CMS_Model_Abstract {
	
	protected $_lang_ids;
	
	protected $_lang_column_name = "id_lang";
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// Récupération des langues actives pour le front
		$cms_config = CMS_Application_Config::getInstance();
		$this->_lang_ids = array_keys(json_decode($cms_config->get("availableFrontLang"), true));
	}
	
	public function get($where = array(), $order = null, $limit = null, $id_lang = null){
		
		if($id_lang) {
			if ( is_array($where) )
				$where = array_merge($where, array($this->_lang_column_name => $id_lang));
			else if( is_int($where) )
				$where = array($this->_lang_column_name => $id_lang, "A.".$this->getPrimaryKeyName() => $where);
			else
				$where = array($this->_lang_column_name => $id_lang);
		}
		
		$return = $this->prepareFieldsForLoad($this->_get($where, $order, $limit));
		
		return $return ? $return : null;
	}
	
	public function getOne($id, $id_lang = null){
		
		if($id_lang)
			$where = array("A.".$this->getPrimaryKeyName() => $id, $this->_lang_column_name => $id_lang);
		else 
			$where = array("A.".$this->getPrimaryKeyName() => $id);
		
		$return = $this->prepareFieldsForLoad($this->_get($where, null, null));
		
		return ($return) ? reset($return) : null ;
	}
	
	private function _get($where = null, $order = null, $limit = null){
		
		$sql = "SELECT * FROM ".$this->getTableName()." A
			LEFT JOIN ".$this->getTableName()."_lang B
			ON A.".$this->_primaryKey." = B.".$this->_primaryKey;
		
		parent::generateSQL($sql, $where, $order, $limit);
		
		$return = $this->getAdapter()->fetchAll($sql, null, zend_db::FETCH_OBJ);
				
		return $return ? $return : null;
	}
	
	public function insert($datas, $autoDate = true){
		
		$datas = $this->prepareFieldsForSave($datas);
		
		if($this->_disableAutoDate === true)
			$autoDate = false;

		$id = parent::insert($this->_name, $this->getValues($datas), $autoDate);
		
		$tvalues = $this->getTranslatedValues($datas);
		
		foreach($this->_lang_ids as $lang_id){

			$tvalues[$lang_id][$this->_primaryKey] = $id;
			$tvalues[$lang_id][$this->_lang_column_name] = $lang_id;

			parent::insert($this->_name."_lang", $tvalues[$lang_id], false);
			
		}

		return $id;
	}
	
	public function update($datas, $where, $autoDate = true){

		$datas = $this->prepareFieldsForSave($datas);
				
		if($this->_disableAutoDate === true)
			$autoDate = false;
		
		$datas_common = $this->getValues($datas);
		
		if (!empty($datas_common))
			parent::update($this->_name, $datas_common, $where, $autoDate);
		

		$tvalues = $this->getTranslatedValues($datas);

		$whereCond = array();
		
		if(is_int($where))
			$whereCond[$this->_primaryKey] = $where;
		else 
			$whereCond = $where;
			
		foreach($this->_lang_ids as $lang_id){
			
			$whereCond[$this->_lang_column_name] = $lang_id;
			parent::update($this->_name."_lang", $tvalues[$lang_id], $whereCond, false, false);
		}
	
		
		return true;
	}
	
	public function delete($where){
			
		// Requete de suppression multi-table
		$sql = "DELETE A,B 
				FROM ".$this->getTableName()." A, ".$this->getTableName()."_lang B
				WHERE A.".$this->_primaryKey." = B.".$this->_primaryKey . " 
				AND ";
		
		// Si clé primaire présente : prefixage du nom de cette clé pour éviter l'ambiguité
		if(is_int($where)) {
			$where = array("A.".$this->getPrimaryKeyName() => $where);
		}
		else if(is_array($where)) {
			if(in_array($this->getPrimaryKeyName(), $where)){
				$where["A.".$this->getPrimaryKeyName()] = $where[$this->getPrimaryKeyName()];
				unset($where[$this->getPrimaryKeyName()]);
			}
		}
		
		$this->appendWhereClause($sql, $where, false);
		
		$return = $this->getAdapter()->query($sql);

		return $return;
	}
	
	/*
	 * Retourne les valeurs sans traduction
	 */
	public function getValues($datas){
		
		if(!isset($datas["common"]) || !is_array($datas["common"])){
			return array();
		}
		
		return $datas["common"];
	}
	
	/*
	 * Retourne les valeurs avec traduction
	 */
	public function getTranslatedValues($datas){
		
		foreach($datas as $key => $values){
			if(!in_array($key, $this->_lang_ids))
				unset($datas[$key]);
		}
		
		return $datas;
	}
	
	protected function prepareFieldsForSave($datas){
		
		$preparedDatas = array();
		
		foreach($datas as $key => $value){
			
			if(in_array($key, $this->_values)){
				$preparedDatas["common"][$key] = $value;
			}
			else if(in_array($key, $this->_translatedValues) && is_array($value)){
				
				foreach($this->_lang_ids as $lang_id){
					$preparedDatas[$lang_id][$key] = $value[$lang_id];
				}
			}
		}
		
		return $preparedDatas;
	}
	
	protected function prepareFieldsForLoad($datas){
		
		// Si aucun retour de la requête, on renvoit null
		if(!$datas)
			return null;
		
		$processedData = array();
		
		foreach ($datas as $data) {
			
			$processedData[$data->{$this->_primaryKey}][$this->_primaryKey] = $data->{$this->_primaryKey};
			
			// Récupération des dates
			if(isset($data->date_add) && $data->date_add)
				$processedData[$data->{$this->_primaryKey}]["date_add"] = $data->date_add;
			if(isset($data->date_upd) && $data->date_upd)
				$processedData[$data->{$this->_primaryKey}]["date_upd"] = $data->date_upd;
			
			// Récupération des éléments communs (non tranductible)
			foreach ($data as $key => $value) {
				if(in_array($key, $this->_values)){
					$processedData[$data->{$this->_primaryKey}][$key] = $value;
				}
			}
			
			// Récupération des éléments traductibles
			foreach ($data as $key => $value) {
				if(in_array($key, $this->_translatedValues)){
					$processedData[$data->{$this->_primaryKey}][$key][$data->{$this->_lang_column_name}] = $value ? $value : null;
				}
			}
			
		}
		
		return $processedData ? $processedData : null;
	}
}