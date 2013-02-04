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

require_once APPLICATION_PATH.'/modules/admin/models/DbTable/Config.php';

class CMS_Application_Config {
	
	private static $_instance;
	private $_configModel;
	private $_cache;
	
	/**
	 * Retrieve singleton instance
	 *
	 * @return CMS_Application_Config
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new CMS_Application_Config();
		}
		return self::$_instance;
	}
	
	private function __construct(){
		$this->_configModel = new Admin_Model_DbTable_Config();
		
		$rows = $this->_configModel->getAllPDO();
			
		$this->_cache = array();
		foreach($rows as $row){
			$this->_cache[$row->name] = $row->value;
		}
	}
	
	 /**
	 * Retrieve value from db_table Config
	 *
	 * @return string value
	 */
	public function get($name){

		if(array_key_exists($name, $this->_cache))
			return $this->_cache[$name];
		else{
			return null;
		}
	}
	
	/**
	 * Set name/value to db_table Config
	 *
	 * @return string value
	 */
	public function set($name, $value){
				
		$this->_configModel->setConfigItem($name, $value);

		self::$_instance = null;
	}
	
	/**
	 * Retrieve active language ID
	 *
	 * @return
	 */
	public function getActiveLang(){
		return CURRENT_LANG_CODE;
	}
	
}