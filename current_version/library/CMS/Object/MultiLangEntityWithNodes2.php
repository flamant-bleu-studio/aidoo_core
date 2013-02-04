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

abstract class CMS_Object_MultiLangEntityWithNodes2 extends CMS_Object_MultiLangEntity {
	
	protected function loadNodesContent($property) 
	{
		// Si parent chargé avec toutes ses langues, de même pour les nodes
		$id_lang = ($this->_id_lang === null) ? 'all' : $this->_id_lang;

		$classObject = static::$_nodes[$property]['className'];
		$defaultNodesOrder = (isset(static::$_nodes[$property]['defaultNodesOrder'])) ? static::$_nodes[$property]['defaultNodesOrder']: null;
		
		$this->$property = $classObject::get(array(static::$_nodes[$property]['foreignKey'] => $this->getPrimaryKey()), $defaultNodesOrder, null, $id_lang); 
	}
	
	public function __get($property)
	{
		$method = 'get'.ucfirst($property);
		
		if(array_key_exists($property, static::$_nodes))
			return $this->getNodes($property);
		else if(method_exists($this, $method)) 
			return $this->$method();
	}
	
	public function __set($property, $value)
	{
		$method = 'set'.ucfirst($property);
		
		if(array_key_exists($property, static::$_nodes))
			$this->setNodes($property, $value);
		else if(method_exists($this, $method)) 
			$this->$method($value);
	}
	
	public function getNodes($property)
	{
		if (!$this->$property) {
			$this->loadNodesContent($property);
		}
		
		return $this->$property;
	}
	
	public function setNodes($property, $value)
	{
		$this->$property = $value;
	}
	
	public function save()
	{
		$nodes = array();
		foreach (static::$_nodes as $property => $nodeInfo) {
			$nodes[$property] = $this->$property;
		}
		unset($this->nodes);

		$id = parent::save();
		
		$this->deleteNodes();

		foreach (static::$_nodes as $property => $nodeInfo) {
			foreach ($nodes[$property] as &$node) {
				$node->{$nodeInfo['foreignKey']} = $id;
				$node->save();
			}
		}

		return $id;
	}	

	private function deleteNodes($id = null) 
	{
		if (!$id)
			$id = $this->getPrimaryKey();
		
		if (!$id)
			return;
		
		if (static::$_nodes) {
			foreach (static::$_nodes as $nodeObject => $nodeInfo) {
				$nodeInfo['className']::deleteFromSQL(array($nodeInfo['foreignKey'] => $id));
			}
		}
	}
	
	public function delete()
	{
		return self::deleteFromPrimaryKey($this->getPrimaryKey());
	}
	
	public static function deleteFromPrimaryKey($id = null)
	{	
		if (parent::deleteFromPrimaryKey($id)) {
			self::deleteNodes($id);
			return true;
		}
		
		return false;
	}
	
	/**
	 * INDISPONIBLE AVEC LES OBJETS DE TYPE CMS_Object_MonoLangEntityWithNodes
	 */
	public static function deleteFromSQL($where = null)
	{	
		throw new Exception(_t("Unable to delete from SQL condition with object 'CMS_Object_MonoLangEntityWithNodes'"));
	}

	
	public function fromArray(array $propertyLst)
	{
		parent::fromArray($propertyLst);
		
		foreach (static::$_nodes as $property => $nodeInfo) {
			
			$values = $this->$property;
			$this->$property = array();
			
			$className = $nodeInfo['className'];
			
			if(!empty($values)){
				foreach($values as $datas){
					
					$node = new $className();
					$node->fromArray($datas);
					array_push($this->$property, $node);
				}
			}
		}
		
	}
	
	/**
	 * Change l'ordre par défaut des objets enfants
	 * 
	 * @param string $nodeName Nom du noeud concerné
	 * @param string $fieldName nom du champ utilisé pour l'ordonnancement
	 * 
	 * @throws Exception
	 */
	public function changeDefaultNodesOrder($nodeName, $fieldName)
	{
		if(!isset(static::$_nodes[$nodeName]))
			throw new Exception(_t('Node name doesn\'t exist'));
		else if(!property_exists(static::$_nodes[$nodeName]['className'], $fieldName))
			throw new Exception(_t('Field name doesn\'t exist'));
		
		static::$_nodes[$nodeName]['defaultNodesOrder'] = $fieldName;
	}
}