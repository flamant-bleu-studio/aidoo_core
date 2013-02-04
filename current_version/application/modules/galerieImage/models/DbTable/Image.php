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

class GalerieImage_Model_DbTable_Image extends CMS_Model_MonoLang {

	protected $_name = "galeries_images";
	
	protected $_disableAutoDate = true;
	
	protected $_primaryKey 	= "id";
	protected $_values 		= array("parent_id", "description", "isPermanent", "date_start", "date_end", "path", "path_thumb", "path2", "path_thumb2", "bg_color", "datas");
	
	public function insert($datas){
		return parent::insert($datas, false);
	}
	
	public function update($datas, $where){
		return parent::update($datas, $where, false);
	}
}