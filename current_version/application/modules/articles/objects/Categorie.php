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

class Articles_Object_Categorie extends CMS_Object_MultiLangEntity
{
	public $id_categorie;
	public $parent_id;
	public $level;
	public $title;
	public $countByPage;
	public $typeView;
	public $image;
	public $description;
	public $fb_comments_number_show;
	
	public $date_add;
	public $date_upd;
	
	protected static $_model;
	protected static $_modelClass = "Articles_Model_DbTable_Categories";
	
	protected static $_modelMapClass = "Articles_Model_DbTable_Map";
	protected static $_modelMap;
	
	public static function deleteFromPrimaryKey($id) {
		parent::deleteFromPrimaryKey($id);
		
		self::_getMapModel();
		
		self::$_modelMap->delete(array('id_categorie' => $id));
	}
	
	protected static function _getMapModel() {
		if (empty(static::$_modelMap) && class_exists(static::$_modelMapClass)) {
			static::$_modelMap = new static::$_modelMapClass();
			return;
		}
	}
	
	/*
	 * Fonction de récupération des catégorie de la même catégorie parente (catégorie du même level)
	 * */
	public function getCatSameLevel()
	{
		return self::get(array('parent_id' => $this->parent_id));
	}
}
