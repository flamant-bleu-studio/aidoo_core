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

class Advertising_Object_Advert extends CMS_Object_MonoLangEntity
{
	public $id;
	public $parent_id;
	
	public $datas;
	
	protected static $_modelClass = "Advertising_Model_DbTable_Advert";
	protected static $_model;
	
	public function deleteByParentId($parent_id)
	{
		if(!is_numeric($parent_id))
			throw new Zend_Exception(_t('There is no number'));
		
		if( !self::$_model )
			self::$_model = new self::$_modelClass();
		
		if(!self::$_model)
			throw new Zend_Exception(_t("Model is not instantiated"));
		
		$return = self::$_model->deleteEntity(array("parent_id" => $parent_id));
		
		return ($return == 1) ? true : false;
	}
	
}

