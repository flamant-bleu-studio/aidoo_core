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

class CMS_Application_Hook 
{
	
	private static $_instance;
	
	private $_hooks;
	
	/**
	 * Retrieve singleton instance
	 *
	 * @return CMS_Application_Hook
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new CMS_Application_Hook();
		}
		return self::$_instance;
	}
	
	private function __construct()
	{
		$this->_hooks = array();
	}
	
	public function add($hook_name, $function_name, $priority = 100)
	{
		
		if(isset($this->_hooks[$hook_name][$priority]) && is_array($this->_hooks[$hook_name][$priority]))
			array_push($this->_hooks[$hook_name][$priority], $function_name);
		else
			$this->_hooks[$hook_name][$priority] = array($function_name);
	}
	
	public function exec_actions($hook_name)
	{
		if(!isset($this->_hooks[$hook_name]))
			return;
		
		$hooks = $this->_hooks[$hook_name];

		ksort($hooks);
		
		$args = func_get_args();
		
		foreach($hooks as $hook_prio)
		{
			foreach($hook_prio as $hook)
			{
				if (is_callable($hook)) 
				{
					call_user_func_array($hook, array_slice($args, 1));
				}
			}
		}
	}
	
	public function apply_filters($hook_name, $value = null)
	{
		
		if(!isset($this->_hooks[$hook_name]))
			return;
		
		$hooks = $this->_hooks[$hook_name];

		ksort($hooks);

		$args = func_get_args();
		
		foreach($hooks as $hook_prio)
		{
			foreach($hook_prio as $hook)
			{
				if (is_callable($hook)) 
				{
					$args[1] = $value;
					$value = call_user_func_array($hook, array_slice($args, 1));
				}
			}
		}
		return $value;
	}
}