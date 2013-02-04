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

abstract class CMS_Object_Tree extends CMS_Object_MultiLangEntity {
	
	protected $_store_parent_id;
	protected $name_tree_field = null;
	protected $id_tree = null;
	
	public $parent_id;
	public $level;	
	public $lft;
	public $rgt;
	
	public $children;
	
	protected function loadContent($id = null)
	{
		if(!(int)$id)
			throw new Exception(_t("ID MISSING"));
	
		$this->id = $id;
		
		$row = parent::loadContent($id);
		
		self::_getModel();
		
		$this->_store_parent_id	= static::$_model->getParentId($id);
		
		$this->parent_id 	= $this->_store_parent_id;
	}
	
	/*
	 * Fonction de récupération de l'arbre
	 * $param est un array comportant le nom du champs de l'identifiant de l'arbre et l'id
	 * $param['id_tree'] && $param['name_tree_field']
	 * */
	public static function getTree($nested = true, $param = array(), $id_lang = CURRENT_LANG_ID ){
	
		self::_getModel($param);
	
		if($id_lang == 'all')
			$id_lang = null;

		$datas = static::$_model->getTree($id_lang);
		
		if(empty($datas))
			return null;
		
		$return = array();
		foreach ($datas as $data_object)
			$return[$data_object[static::$_model->getPrimaryKeyName()]] = new static($data_object, $id_lang);
		
		/*
		* Le tri de l'arbre ne peut se faire que s'il est nesté : l'arbre est nesté, trié, et denesté si besoin.
		* Si l'arbre est multilangue et qu'on le veut à plat : inutile de faire un tri.
		*/

		// l'arbre est multilangue et à plat
		if(!$id_lang && $nested === false) 
			return $return;
	
		// Nestify
		$datas = self::neste($return);

		// Sort si dans une seule langue
		if($id_lang)
			self::sortTree(&$datas);
	
		// Denestify au besoin
		if ($nested === false)
			return self::uneste($datas);
		
		return $datas;
	}
	
	
	/*
	 * Fonction de trie de l'arbre
	 * 
	 * */
	private function sortTree($datas)
	{
		$arrayToOrder = array();
		foreach($datas as &$data) {
			$arrayToOrder[$data->title] = $data;
				
			if(isset($data->children))
			self::sortTree(&$data->children);
		}
	
		setLocale(LC_COLLATE, 'fr_FR.utf8');
		ksort($arrayToOrder, SORT_LOCALE_STRING);
	
		$datas = $arrayToOrder;
	}
	
	private function neste($array){
		$nested = array();
		$depths = array();

		foreach($array as $key => $item){
			if ($item->level > 0){
				if( ($item->level -1) == 0 ) {
		
					$nested[$key] = $item;
					$depths[($item->level-1) + 1] = $key;
				}
				else {
					$parent = &$nested;
					 
					for( $i = 1; $i <= ( ($item->level-1) ); $i++ ) {
		
						if(($item->level-1) != $i)
							$parent = &$parent[$depths[$i]]->children;
						else
							$parent = &$parent[$depths[$i]];
					}
					 
					$parent->children[$key] = $item;
					$depths[($item->level-1) + 1] = $key;
				}
			}
		}
	
		return $nested;
	}
	
	public static function uneste($datas){
	
		$return = array();
	
		foreach ($datas as &$value) {
				
			if(isset($value->children))
				$children = $value->children;
			else
				$children = null;
	
			$value->children = null;
				
			$return[] = $value;
				
			if(isset($children))
				$return = array_merge($return, self::uneste(&$children));
		}
	
		return $return;
	}
	
	public static function excludeTree(array $array, $id){
		foreach($array as $key => &$el){
			if($el->id == $id){
				unset($array[$key]);
				break;
			}
				
			if($el->children)
			$el->children = self::excludeTree($el->children, $id);
		}
	
		return $array;
	}
	
	/*
	* Fonction permettant de savoir si $id est un enfant de l'instance en cours
	* @return true si $id est enfant, false sinon
	* */
	public function isChild($id = null)
	{
		if(!(int)$id)
		throw new Exception(_t("ID MISSING"));
	
		$obj = new static((int)$id);
	
		if ($this->lft < $obj->lft && $this->rgt > $obj->rgt)
		return true;
	
		return false;
	}
	
	/*
	 * Fonction permettant d'ajouter un item dans l'arbre
	 * */
	public function addItem(){
		self::_getModel();
		
		$datas = static::$_model->addItem($this);
	}
	
	protected function _update(){
		parent::_update();
	
		self::_getModel();
		
		if($this->_store_parent_id != $this->parent_id){
			// Déplacer l'item (et ses enfants) dans un autre parent
			static::$_model->updateParent($this->id, $this->parent_id);
		}
	
		return $this->id;
	}
	
	protected static function _getModel($param = array())
	{
		if (empty(static::$_model) && class_exists(static::$_modelClass))
		{
			if ($this instanceof PagesPro_ActivitesController){
				static::$_model = new static::$_modelClass($this->name_tree_field, $this->id_tree);
			} else { 
				if (isset($param['name_tree_field']) && isset($param['id_tree']))
					static::$_model = new static::$_modelClass($param['name_tree_field'], $param['id_tree']);
				else 
					static::$_model = new static::$_modelClass();
			}
			
			return;
		}
	}
}