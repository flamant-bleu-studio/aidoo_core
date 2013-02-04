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

class Blocs_Model_DbTable_Templates extends CMS_Model_MonoLang {	
	
	protected $_name = 'core_templates';
	protected $_dependentTables = array('Blocs_Model_DbTable_Map');
	
	protected $_primaryKey 	= 'id_template';
	protected $_values 		= array('title', 'defaut', 'theme', 'classCss', 'bgType', 'bgColor1', 'bgColor2', 'bgGradient', 'bgPicture', 'bgRepeat');
	
		
	public function setDefault($id = null)
	{
		$this->getAdapter()->update($this->getTableName(), array('defaut' => 0), 'defaut = 1');
		
		if($this->getAdapter()->update($this->getTableName(), array('defaut' => 1), 'id_template = '.(int)$id) == 1)
			return true;
		else
			return false;
	}
	
	public function insert($datas){
		return parent::insert($datas, false);
	}
	
	public function update($datas, $where){
		return parent::update($datas, $where, false);
	}
}
