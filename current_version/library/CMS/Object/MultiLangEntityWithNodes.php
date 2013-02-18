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

abstract class CMS_Object_MultiLangEntityWithNodes extends CMS_Object_MultiLangEntity {
	
	protected function loadNodesContent($property) {
		
		// Instanciation du modèle des Nodes + stockage en attribut static
		if(!(static::$_nodes[$property]["model"] instanceof static::$_nodes[$property]["modelClass"])) {
			static::$_nodes[$property]["model"] = new static::$_nodes[$property]["modelClass"]();
		}
		
		// Récupération des Nodes
		$return = static::$_nodes[$property]["model"]->get(array(static::$_model->getPrimaryKeyName() => $this->getPrimaryKey()), null, null, $this->_id_lang);

		
		// Formatage si multi ou mono langue
		if($return){
			$this->{$property} = new stdClass();
			foreach($return as $r){
				if($this->_id_lang && is_array($r["value"]))
    				$this->{$property}->$r["name"] = $r["value"][$this->_id_lang];
    			else
    				$this->{$property}->$r["name"] = $r["value"];
			}
		}
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
	
	public function save()
	{
		
		parent::save();
		
		$this->deleteNodes();
		
		if(static::$_nodes){
			foreach(static::$_nodes as $property => $node){
				foreach($this->$property as $name => $val){
					
					// Instanciation du modèle des Nodes + stockage en attribut static
					if(!($node["model"] instanceof $node["modelClass"])) {
						$node["model"] = new $node["modelClass"]();
					}
					
					$datas = array(static::$_model->getPrimaryKeyName() => $this->getPrimaryKey());
					/**
					 * @todo: les champs Name et Value sont des conventions de nommage. A remplacer par une mécanique dynamique
					 */
					$datas["name"] = $name;
					$datas["value"] = $val;
					
					$node["model"]->insert($datas, false);
				}
			}
		}
		
		return $this->getPrimaryKey();
	}	

	private function deleteNodes($id = null){

		if(!$id)
			$id = $this->getPrimaryKey();
		
		if(!$id)
			return;
		
		if(static::$_nodes){
			foreach(static::$_nodes as &$node){
				
				// Instanciation du modèle des Nodes + stockage en attribut static
				if(!($node["model"] instanceof $node["modelClass"])) {
					$node["model"] = new $node["modelClass"]();
				}
				
				// Suppression des nodes en vue de les ré-enregistrer
				$node["model"]->delete(array(static::$_model->getPrimaryKeyName() => $id));
			}
		}
	}

	public static function deleteFromPrimaryKey($id = null)
	{	
		if(parent::deleteFromPrimaryKey($id)){
			self::deleteNodes($id);
			return true;
		}
		
		return false;
	}
	
	/**
	 * INDISPONIBLE AVEC LES OBJETS DE TYPE CMS_Object_MultiLangEntityWithNodes
	 */
	public static function deleteFromSQL($where = null){	
		throw new Exception(_t("Unable to delete from SQL condition with object 'CMS_Object_MultiLangEntityWithNodes'"));
	}
	
	public function toArray()
	{
		$array = parent::toArray();
	
		if(static::$_nodes){
			foreach(static::$_nodes as $property => $node){
				$array[$property] = (array)$this->getNodes($property);
			}
		}

		return $array;
	}
}