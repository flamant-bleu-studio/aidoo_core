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

abstract class CMS_Object_Abstract {

	protected $_id_lang;
	
	/**
	 * Set the primary key value
	 * @param int $value
	 */
	protected function setPrimaryKey($value){
		$this->{static::$_model->getPrimaryKeyName()} = (int)$value;
	}
	
	/**
	 * Get the primary key value
	 * @return int primary key value
	 */
	protected function getPrimaryKey(){
		return (int)$this->{static::$_model->getPrimaryKeyName()};
	}
	
	public function __construct($data = null, $id_lang = CURRENT_LANG_ID)
	{
		self::_getModel();
		
		if( $data ) {
			
			$this->_id_lang = ($id_lang != "all") ? $id_lang : null ;
			
			// Récupération des données en BDD avec la clé primaire
			if( is_int($data) ) {
				$this->loadContent((int)$data);
			}
			// Construction de l'objet avec les données passées en paramètre
			else if( is_array($data) ) {
				
				if( key_exists(static::$_model->getPrimaryKeyName(), $data) && $data[static::$_model->getPrimaryKeyName()] ) {
					$this->setPrimaryKey($data[static::$_model->getPrimaryKeyName()]);
					
					$this->fromArray($data);
				}
				else
					throw new Zend_Exception("Missing primary key");
			}
			
		}
	}
	
	protected function loadContent($id = null)
	{
		if(!(int)$id)
			throw new Exception(_t("ID MISSING"));
		
		$this->setPrimaryKey((int)$id);
		
		$row = $this->_getInnerRow();
		
		if($row)
			$this->fromArray($row);
		else
			throw new Exception(_t("Unable to load item"));
	}
	
	/**
	 * Retourne des instances d'objets selon des critères de recherche
	 * @param array $where filtre les résultats
	 * @param mixed $order ordonne les résultats
	 * @param mixed $limit limite les résultats
	 * @param mixed $id_lang null => langue courante; int => langue précisée; "all" => toutes les langues (impossible de filtrer les résultats)
	 */
	public static function get($where = array(), $order = null, $limit = null, $id_lang = null)
	{
		self::_getModel();
		
		if($id_lang == null)
			$id_lang = CURRENT_LANG_ID;
		
		if($id_lang == "all")
			$id_lang = null;
			
		$data_objects = static::$_model->get($where, $order, $limit, $id_lang);
		
		if($data_objects)
		{
			$return = array();
			foreach ($data_objects as $data_object)
			{
				$return[] = new static($data_object, $id_lang);
			}
			
			return $return;
		}
		else
			return null;
	}
	
	public static function getOne($where = array(), $order = null, $limit = null, $id_lang = CURRENT_LANG_ID) {
		$return = self::get($where, $order, $limit, $id_lang);
		
		if(is_array($return) && !empty($return))
			return reset($return);
		
		return null;
	}
	
	public static function count($where = array()) {
		self::_getModel();
		return static::$_model->count($where);
	}
	
	public function save()
	{
		if(isset($this->_id_lang))
			throw new Exception("Impossible d'enregistrer un objet avec une seule langue");
		
		// Update
		if($this->getPrimaryKey() != null)
			$return = $this->_update();
		// Insert
		else
			$return = $this->_insert();
		
		return $return;
	}
	
	protected function _insert()
	{
		$datas = $this->toArray();
		
		$className = static::getCalledClass();
		
		CMS_Application_Hook::getInstance()->apply_filters('CMS_Object_BeforeInsert', $this);
		CMS_Application_Hook::getInstance()->apply_filters($className . '_BeforeInsert', $this);
		
		$return = static::$_model->insert($datas);
		
		if( $return ) {
			$this->setPrimaryKey($return);
			
			CMS_Application_Hook::getInstance()->apply_filters('CMS_Object_AfterInsert', $this);
			CMS_Application_Hook::getInstance()->apply_filters($className . '_AfterInsert', $this);
		}
		
		return $return;
	}
	
	protected function _update()
	{
		$datas = $this->toArray();
		
		$className = static::getCalledClass();
		
		CMS_Application_Hook::getInstance()->apply_filters('CMS_Object_BeforeUpdate', $this);
		CMS_Application_Hook::getInstance()->apply_filters($className . '_BeforeUpdate', $this);
		
		static::$_model->update($datas, $this->getPrimaryKey());
		
		CMS_Application_Hook::getInstance()->apply_filters('CMS_Object_AfterUpdate', $this);
		CMS_Application_Hook::getInstance()->apply_filters($className . '_AfterUpdate', $this);
		
		return $this->getPrimaryKey();
	}
	
	/**
	 * Delete current object
	 * @return bool is success delete
	 */
	public function delete()
	{
		$return  = self::deleteFromPrimaryKey($this->getPrimaryKey());
		
		return $return;
	}
	
	/**
	 * Delete item from primary key
	 * 
	 * @param int $id primary key
	 * @return bool is success delete
	 */
	public static function deleteFromPrimaryKey($id = null)
	{
		$id = (int)$id;
		
		if(!$id)
			throw new Exception(_t("Unable to delete : empty primary key"));
		
		self::_getModel();
		
		$className = static::getCalledClass();
		
		CMS_Application_Hook::getInstance()->apply_filters('CMS_Object_BeforeDelete', $id);
		CMS_Application_Hook::getInstance()->apply_filters($className . '_BeforeDelete', $id);
		
		$return = static::$_model->delete($id);
		
		if ($return) {
			CMS_Application_Hook::getInstance()->apply_filters('CMS_Object_AfterDelete', $id);
			CMS_Application_Hook::getInstance()->apply_filters($className . '_AfterDelete', $id);
		}
		
		return ($return == 1) ? true : false;
	}
	
	/**
	 * Delete item(s) from where condition
	 * (indisponible avec le modele "LangBase")
	 * 
	 * @param mixed $where where condition
	 * @return int count rows deleted
	 */
	public static function deleteFromSQL($where = null)
	{	
		if(empty($where))
			throw new Exception(_t("Unable to delete : no 'where' condition specify"));
		
		self::_getModel();
		
		$hookName = static::getCalledClass();
		CMS_Application_Hook::getInstance()->apply_filters($hookName . '_BeforeDelete', $this);
		
		$return = static::$_model->delete($where);
		
		CMS_Application_Hook::getInstance()->apply_filters($hookName . '_AfterDelete', $this);
		
		return $return;
	}

	public function fromArray(array $propertyLst)
    {
    	unset($propertyLst[static::$_model->getPrimaryKeyName()]);
    	
    	foreach ($propertyLst as $property => $value)
    	{
    		if(property_exists($this, $property)){
    			// Si l'objet n'est chargé qu'en une seule langue
    			if($this->_id_lang && is_array($value))
    				$this->$property = $value[$this->_id_lang];
    			else
    				$this->$property = $value;
    		}
    	}
    }
	
	public function toArray()
	{
		$properties = $this->_getProperties();

		foreach ($properties as $property)
			$array[$property] = $this->$property;
		
		return $array;
	}

	protected function _getInnerRow($id = null)
	{
		if ($id == null)
			$id = $this->getPrimaryKey();
	
		$lang =  ($this->_id_lang) ? $this->_id_lang : null;
		
		return static::$_model->getOne($id, $lang);
	}

	protected function _getProperties()
	{
		$propertyArray = array();
		$class = new Zend_Reflection_Class($this);
		$properties = $class->getProperties();
		foreach ($properties as $property)
		{
			if ($property->isPublic())
				$propertyArray[] = $property->getName();
		}
		return $propertyArray;
	}

	protected static function _getModel()
	{
		if (empty(static::$_model) && class_exists(static::$_modelClass))
		{
			static::$_model = new static::$_modelClass();
			return;
		}
	}
	
	/**
	 * Get name of child class
	 */
	public static function getCalledClass() {
		return get_called_class();
	}
	
	public function getPrimaryKeyName(){
		self::_getModel();
		return static::$_model->getPrimaryKeyName();
	}
}