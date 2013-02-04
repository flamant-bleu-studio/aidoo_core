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

abstract class CMS_Object_MonoLangEntityWithNodes extends CMS_Object_MonoLangEntity {
	
	protected function loadNodesContent($property) {
		
		$classObject = static::$_nodes[$property];
		$this->$property = $classObject::get(array("parent_id" => $this->id), array("id")); 
	}
	
	public function __get($property){
		
		$method = 'get'.ucfirst($property);
		
		if(array_key_exists($property, static::$_nodes))
			return $this->getNodes($property);
		else if(method_exists($this, $method)) 
			return $this->$method();
		
	}
	
	public function __set($property, $value){
		
		$method = 'set'.ucfirst($property);
		
		if(array_key_exists($property, static::$_nodes))
			$this->setNodes($property, $value);
		else if(method_exists($this, $method)) 
			$this->$method($value);
		
	}
	
	public function getNodes($property){
		
		if(!$this->$property){
			$this->loadNodesContent($property);
		}
		
		return $this->$property;
	}
	
	public function setNodes($property, $value){
		
		$this->$property = $value;
	}
	
	public function save() {

		$nodes = array();
		foreach(static::$_nodes as $property => $objectName){
			$nodes[$property] = $this->$property;
		}
		unset($this->nodes);

		$id = parent::save();
		
		$this->deleteNodes();

		foreach(static::$_nodes as $property => $objectClass){
			if ($nodes[$property]){
				foreach($nodes[$property] as $node){
					$node->setPrimaryKey(null);
					$node->parent_id = $id;
					$node->save();
				}
			}
		}

		return $id;
	}	

	private function deleteNodes($id = null) {

		if(!$id)
			$id = $this->getPrimaryKey();
		
		if(!$id)
			return;
		
		if(static::$_nodes){
			foreach(static::$_nodes as $nodeObject){
				$nodeObject::deleteFromSQL(array("parent_id" => $id));
			}
		}
	}
	
	public function delete()
	{
		return self::deleteFromPrimaryKey($this->getPrimaryKey());
	}
	
	public static function deleteFromPrimaryKey($id = null) {	

		if(parent::deleteFromPrimaryKey($id)){
			self::deleteNodes($id);
			return true;
		}
		
		return false;
	}
	
	/**
	 * INDISPONIBLE AVEC LES OBJETS DE TYPE CMS_Object_MonoLangEntityWithNodes
	 */
	public static function deleteFromSQL($where = null){	
		throw new Exception(_t("Unable to delete from SQL condition with object 'CMS_Object_MonoLangEntityWithNodes'"));
	}

	
	public function fromArray(array $propertyLst) {
		
		parent::fromArray($propertyLst);
		
		foreach(static::$_nodes as $property => $objectName){
			
			$values = $this->$property;
			$this->$property = array();
			
			if(!empty($values)){
				foreach($values as $datas){
					$node = new $objectName();
					$node->fromArray($datas);
					array_push($this->$property, $node);
				}
			}
		}
	}
}